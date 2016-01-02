<?php
// Root path
define('PATH_ROOT', __DIR__ . '/../../');

class Magirc {
    public $db;
    public $cfg;
    public $slim;
    public $service;

    function __construct($useTemplateEngine = false) {
        $this->db = self::initializeDatabase();
        $this->cfg = self::initializeConfiguration();
        $this->service = self::initializeService();
        $this->slim = self::initializeFramework($useTemplateEngine);
        self::initializeLocalization();
    }

    private function initializeFramework($useTemplateEngine) {
        if ($useTemplateEngine) {
            $container = self::initializeTemplateEngine();
            return new \Slim\App($container);
        }
        return new \Slim\App();
    }

    private function initializeDatabase() {
        require_once(__DIR__.'/MagircDB.php');
        $db = MagircDB::getInstance();
        $db->query("SHOW TABLES LIKE 'magirc_config'", SQL_INIT);
        if (!$db->record) {
            die('Database table missing. Please run setup.');
        }
        return $db;
    }

    private function initializeConfiguration() {
        $cfg = new Config($this->db);
        if ($cfg->db_version < DB_VERSION) die('Upgrade in progress. Please wait a few minutes, thank you.');
        date_default_timezone_set($cfg->timezone);
        define('DEBUG', $cfg->debug_mode);
        define('BASE_URL', $cfg->base_url);
        if ($cfg->debug_mode < 1) {
            ini_set('display_errors','off');
            error_reporting(E_ERROR);
        }
        return $cfg;
    }

    private function initializeTemplateEngine() {
        $configuration = [
            'settings' => [
                'displayErrorDetails' => $this->cfg->debug > 0,
            ],
        ];
        $container = new \Slim\Container($configuration);
        $container['view'] = function ($c) {
            $view = new \Slim\Views\Twig(__DIR__ . '/../../theme/'.$this->cfg->theme.'/tpl', [
                'cache' => __DIR__ . '/../../tmp',
                'debug' => $this->cfg->debug > 0
            ]);
            $view->addExtension(new \Slim\Views\TwigExtension(
                $c['router'],
                $c['request']->getUri()
            ));
            $view->addExtension(new Twig_Extensions_Extension_I18n());
            return $view;
        };
        $container['notFoundHandler'] = function ($c) {
            return function ($request, $response) use ($c) {
                return $c['view']->render($response, 'error.twig', [
                    'err_code' => 404,
                    'cfg' => $this->cfg->config
                ])->withStatus(404);
            };
        };
        $container['notAllowedHandler'] = function ($c) {
            return function ($request, $response) use ($c) {
                return $c['view']->render($response, 'error.twig', [
                    'err_code' => 405,
                    'cfg' => $this->cfg->config
                ])->withStatus(405);
            };
        };
        $container['errorHandler'] = function ($c) {
            return function ($request, $response, $exception) use ($c) {
                return $c['view']->render($response, 'error_fatal.twig', [
                    'err_msg' => $exception->getMessage(),
                    'err_extra' => nl2br($exception->getTraceAsString()),
                    'server' => $_SERVER
                ])->withStatus(500);
            };
        };
        $container['locales'] = $this->getLocalesSelect();
        $container['config'] = $this->cfg->config;
        return $container;
    }

    private function initializeService() {
        define('IRCD', $this->cfg->ircd_type);
        switch($this->cfg->service) {
            case 'anope':
                require_once(__DIR__.'/../../lib/magirc/services/Anope.class.php');
                return new Anope();
                break;
            case 'denora':
                require_once(__DIR__.'/../../lib/magirc/services/Denora.class.php');
                return new Denora();
                break;
            default:
                return null;
        }
    }

    private function initializeLocalization() {
        $locale = self::getLocale();
        $domain = "messages";
        //putenv("LC_ALL={$locale}.utf8");
        //setlocale(LC_ALL, $locale.'.UTF-8');
        putenv("LC_ALL={$locale}");
        setlocale(LC_ALL, $locale);
        bindtextdomain($domain, PATH_ROOT.'locale/');
        bind_textdomain_codeset($domain, 'UTF-8');
        textdomain($domain);
        define('LOCALE', $locale);
        define('LANG', substr($locale, 0, 2));
    }

    private function getLocale() {
        $locales = self::getLocales();
        if (isset($_GET['locale']) && in_array($_GET['locale'], $locales)) {
            setcookie('magirc_locale', $_GET['locale'], time()+60*60*24*30, '/');
            return $_GET['locale'];
        }
        if (isset($_COOKIE['magirc_locale']) && in_array($_COOKIE['magirc_locale'], $locales)) {
            return $_COOKIE['magirc_locale'];
        }
        return $this->detectLocale($locales);
    }

    /**
     * Gets a list of available locales
     * @return array
     */
    private function getLocales() {
        $locales = array();
        foreach (glob(PATH_ROOT."locale/*") as $filename) {
            if (is_dir($filename)) {
                $locales[] = basename($filename);
            }
        }
        return $locales;
    }

    private function getLocalesSelect() {
        $locales = array();
        foreach (glob(__DIR__."/../../locale/*") as $filename) {
            if (is_dir($filename)) {
                $locale = basename($filename);
                //This is dirty but I'm lazy...
                switch ($locale){
                    case 'en_US':
                        $language = "English";
                        break;
                    case 'de_DE':
                        $language = "Deutsch";
                        break;
                    case 'es_ES':
                        $language = "Español";
                        break;
                    case 'fr_FR':
                        $language = "Français";
                        break;
                    case 'it_IT':
                        $language = "Italiano";
                        break;
                    case 'nl_NL':
                        $language = "Nederlands";
                        break;
                    case 'ms_MY';
                        $language = "Melayu";
                        break;
                    default:
                        $language = $locale;
                }
                $locales[$locale] = $language;
            }
        }
        return $locales;
    }

    /**
     * Detects the best locale based on HTTP ACCEPT_LANGUAGE
     * @param array $available_languages Array of available locales
     * @return string Locale
     */
    private function detectLocale($available_locales) {
        $hits = array();
        $bestlang = $this->cfg->locale;
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            preg_match_all("/([[:alpha:]]{1,8})(-([[:alpha:]|-]{1,8}))?(\s*;\s*q\s*=\s*(1\.0{0,3}|0\.\d{0,3}))?\s*(,|$)/i", $_SERVER['HTTP_ACCEPT_LANGUAGE'], $hits, PREG_SET_ORDER);
            $bestqval = 0;
            foreach ($hits as $arr) {
                $langprefix = strtolower ($arr[1]);
                $qvalue = empty($arr[5]) ? 1.0 : floatval($arr[5]);
                if (in_array($langprefix,$available_locales) && ($qvalue > $bestqval)) {
                    $bestlang = $langprefix;
                    $bestqval = $qvalue;
                }
            }
        }
        return $bestlang;
    }

    /**
     * Gets the page content for the specified name
     * @param string $name Content identifier
     * @return string HTML content
     */
    function getContent($name) {
        $ps = $this->db->prepare("SELECT text FROM magirc_content WHERE name = :name");
        $ps->bindParam(':name', $name, PDO::PARAM_STR);
        $ps->execute();
        return $ps->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * Checks thet permission for the given type and target.
     * Terminates the program with an appropriate error message on failure.
     * (Used by the RESTful API)
     * @param string $type Choices: 'channel'
     * @param string $target For example the channel name
     */
    function checkPermission($type, $target) {
        $result = 200;
        switch($type) {
            case 'channel':
                $result = $this->service->checkChannel($target);
                break;
        }
        // In case of error the application will terminate, otherwise it will continue normally
        switch ($result) {
            case 404: $this->slim->notFound();
            case 403: $this->slim->halt(403, $this->jsonOutput(array('error' => "HTTP 403 Access Denied")));
        }
    }

    /**
     * Encodes the given data as a JSON object.
     * (Used by the RESTful API)
     * @param mixed $data Data
     * @param boolean $datatables allow/forbid DataTables format
     * @param string $idcolumn Column name to use as index for the DataTables automatic row id. If not specified, the first column will be used.
     */
    function jsonOutput($data, $datatables = false, $idcolumn = null) {
        if ($datatables && @$_GET['format'] == "datatables") {
            if (!$idcolumn && count($data) > 0) $idcolumn = key($data[0]);
            foreach ($data as $key => $val) {
                if (is_array($data[$key])) $data[$key]["DT_RowId"] = $val[$idcolumn];
                else $data[$key]->DT_RowId = $val->$idcolumn;
            }
            echo json_encode(array('data' => $data));
        } else {
            echo json_encode($data);
        }
    }

    /**
     * Returns the session status
     * @return boolean true: valid session, false: invalid or no session
     */
    function sessionStatus() {
        if (!isset($_SESSION["loginUsername"])) {
            $_SESSION["message"] = "Access denied";
            return false;
        }
        if (!isset($_SESSION["loginIP"]) || ($_SESSION["loginIP"] != $_SERVER["REMOTE_ADDR"])) {
            $_SESSION["message"] = "Access denied";
            return false;
        }
        return true;
    }

    /**
     * Returns the given text with html tags for colors and styling
     * @param string $text IRC text
     * @return string HTML text
     */
    public static function irc2html($text) {
        $lines = explode("\n", utf8_decode($text));
        $out = '';

        foreach ($lines as $line) {
            $line = nl2br(htmlentities(utf8_decode($line), ENT_COMPAT));
            // replace control codes
            $line = preg_replace_callback('/[\003](\d{0,2})(,\d{1,2})?([^\003\x0F]*)(?:[\003](?!\d))?/', function($matches) {
                        $colors = array('#FFFFFF', '#000000', '#00007F', '#009300', '#FF0000', '#7F0000', '#9C009C', '#FC7F00', '#FFFF00', '#00FC00', '#009393', '#00FFFF', '#0000FC', '#FF00FF', '#7F7F7F', '#D2D2D2');
                        $options = '';

                        if ($matches[2] != '') {
                            $bgcolor = trim(substr($matches[2], 1));
                            if ((int) $bgcolor < count($colors)) {
                                $options .= 'background-color: ' . $colors[(int) $bgcolor] . '; ';
                            }
                        }

                        $forecolor = trim($matches[1]);
                        if ($forecolor != '' && (int) $forecolor < count($colors)) {
                            $options .= 'color: ' . $colors[(int) $forecolor] . ';';
                        }

                        if ($options != '') {
                            return '<span style="' . $options . '">' . $matches[3] . '</span>';
                        } else {
                            return $matches[3];
                        }
                    }, $line);
            $line = preg_replace('/[\002]([^\002\x0F]*)(?:[\002])?/', '<strong>$1</strong>', $line);
            $line = preg_replace('/[\x1F]([^\x1F\x0F]*)(?:[\x1F])?/', '<span style="text-decoration: underline;">$1</span>', $line);
            $line = preg_replace('/[\x12]([^\x12\x0F]*)(?:[\x12])?/', '<span style="text-decoration: line-through;">$1</span>', $line);
            $line = preg_replace('/[\x16]([^\x16\x0F]*)(?:[\x16])?/', '<span style="font-style: italic;">$1</span>', $line);
            $line = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\S+]*(\?\S+)?)?)?)@', "<a href='$1' class='topic'>$1</a>", $line);
            // remove dirt
            $line = preg_replace('/[\x00-\x1F]/', '', $line);
            $line = preg_replace('/[\x7F-\xFF]/', '', $line);
            // append line
            if ($line != '') {
                $out .= $line;
            }
        }

        return $out;
    }

}
