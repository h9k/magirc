<?php
// Root path
define('PATH_ROOT', __DIR__ . '/../../');

use \Gettext\Translator as Translator;

class Magirc {
    public $db;
    public $cfg;
    public $slim;
    public $translator;
    public $service;

    function __construct($useTemplateEngine = false) {
        $this->db = self::initializeDatabase();
        $this->cfg = self::initializeConfiguration();
        $this->service = self::initializeService();
        $this->slim = self::initializeFramework($useTemplateEngine);
        $this->translator = new Translator();
        $this->translator->register();
        self::initializeLocalization();
    }

    private function initializeFramework($useTemplateEngine) {
        if ($useTemplateEngine) {
            $configuration = [
                'settings' => [
                    'displayErrorDetails' => $this->cfg->debug_mode > 0,
                ],
            ];

            $container = new \Slim\Container($configuration);
            $container['view'] = function ($c) {
                $view = new \Slim\Views\Twig(__DIR__ . '/../../theme/'.$this->cfg->theme.'/tpl', [
                    'cache' => __DIR__ . '/../../tmp',
                    'debug' => $this->cfg->debug_mode > 0,
                    'translation_function' => 'translate',
                    'translation_function_plural' => 'translate_plural'
                ]);
                $view->addExtension(new \Slim\Views\TwigExtension(
                    $c['router'],
                    $c['request']->getUri()
                ));
                $view->addExtension(new Twig_Extensions_Extension_I18n);
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
                        'server' => $_SERVER,
                        'cfg' => $this->cfg->config
                    ])->withStatus(500);
                };
            };
            $container['locales'] = $this->getLocalesSelect();
            $container['config'] = $this->cfg->config;
        } else {
            $configuration = [
                'settings' => [
                    'http.version' => '1.0'
                ],
            ];
            $container['notFoundHandler'] = function ($c) {
                return function ($request, $response) use ($c) {
                    return $response->withJson(array('error' => "HTTP 404 Not Found"))->withStatus(404);
                };
            };
            $container['notAllowedHandler'] = function ($c) {
                return function ($request, $response) use ($c) {
                    return $response->withJson(array('error' => "HTTP 405 Not Allowed"))->withStatus(405);
                };
            };
            $container['errorHandler'] = function ($c) {
                return function ($request, $response, $exception) use ($c) {
                    return $response->withJson(array('error' => "HTTP 500 Internal Server Error"))->withStatus(500);
                };
            };
            $container = new \Slim\Container($configuration);
        }
        return new \Slim\App($container);
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
        define('BASE_URL', $cfg->base_url . '/');
        if ($cfg->debug_mode < 1) {
            ini_set('display_errors','off');
            error_reporting(E_ERROR);
        }
        return $cfg;
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
        putenv("LC_ALL=$locale");
        setlocale(LC_ALL, $locale);
        bindtextdomain('messages', 'locale');
        bind_textdomain_codeset('messages', 'UTF-8');
        textdomain('messages');
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
                    case 'tr_TR';
                        $language = "Türkçe";
                        break;
                    case 'pt_PT':
                        $language = "Português";
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
     * Prepares the given data array for use with DataTables
     * (Used by the RESTful API)
     * @param mixed $data Data
     * @param string $idcolumn Column name to use as index for the DataTables automatic row id. If not specified, the first column will be used.
     */
    function arrayForDataTables($data, $idcolumn = null) {
        if (@$_GET['format'] == "datatables") {
            if (!$idcolumn && count($data) > 0) $idcolumn = key($data[0]);
            foreach ($data as $key => $val) {
                if (is_array($data[$key])) $data[$key]["DT_RowId"] = $val[$idcolumn];
                else $data[$key]->DT_RowId = $val->$idcolumn;
            }
            return array('data' => $data);
        }
        return $data;
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
