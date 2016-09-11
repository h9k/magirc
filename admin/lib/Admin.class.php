<?php
// Root path
define('PATH_ROOT', __DIR__ . '/../../');

// Database configuration
class Magirc_DB extends DB {
    private static $instance = NULL;

    public static function getInstance() {
        if (is_null(self::$instance) === true) {
            $db = array();
            if (file_exists(__DIR__.'/../../conf/magirc.cfg.php')) {
                include(__DIR__.'/../../conf/magirc.cfg.php');
            } else {
                die ('magirc.cfg.php configuration file missing');
            }
            $dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
            $args = array();
            if (isset($db['ssl']) && $db['ssl_key']) $args[PDO::MYSQL_ATTR_SSL_KEY] = $db['ssl_key'];
            if (isset($db['ssl']) && $db['ssl_cert']) $args[PDO::MYSQL_ATTR_SSL_CERT] = $db['ssl_cert'];
            if (isset($db['ssl']) && $db['ssl_ca']) $args[PDO::MYSQL_ATTR_SSL_CA] = $db['ssl_ca'];
            self::$instance = new DB($dsn, $db['username'], $db['password'], $args);
            if (self::$instance->error) die('Error opening the MagIRC database<br />' . self::$instance->error);
        }
        return self::$instance;
    }
}

class Admin {
    public $slim;
    public $tpl;
    public $db;
    public $cfg;

    function __construct() {
        $this->db = Magirc_DB::getInstance();
        $this->cfg = new Config();
        $configuration = [
            'settings' => [
                'displayErrorDetails' => $this->cfg->debug_mode > 0,
            ],
        ];
        $container = new \Slim\Container($configuration);
        $container['view'] = function ($c) {
            $view = new \Slim\Views\Twig(__DIR__ . '/../tpl', [
                'cache' => __DIR__ . '/../../tmp',
                'debug' => $this->cfg->debug_mode > 0
            ]);
            $view->addExtension(new \Slim\Views\TwigExtension(
                $c['router'],
                $c['request']->getUri()
            ));
            $engine = new Aptoma\Twig\Extension\MarkdownEngine\MichelfMarkdownEngine();
            $view->addExtension(new \Aptoma\Twig\Extension\MarkdownExtension($engine));
            return $view;
        };
        $container['notFoundHandler'] = function ($c) {
            return function ($request, $response) use ($c) {
                return $c['view']->render($response, 'error.twig', [
                    "err_code" => 404
                ])->withStatus(404);
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
        $this->slim = new \Slim\App($container);
    }

    /**
     * Admin Login
     * @param string $username
     * @param string $password
     * @return boolean true: successful, false: failed
     */
    function login($username, $password) {
        if (!isset($username) || !isset($password)) {
            return false;
        }
        if ($this->db->selectOne('magirc_admin', array('username' => trim($username), 'password' => md5(trim($password))))) {
            $_SESSION['username'] = $_POST['username'];
            $_SESSION["ipaddr"] = $_SERVER["REMOTE_ADDR"];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns session status
     * @return boolean true: valid session, false: no valid session
     */
    function sessionStatus() {
        if (!isset($_SESSION["username"])) {
            $_SESSION["message"] = "Access denied";
            return false;
        }
        if (!isset($_SESSION["ipaddr"]) || ($_SESSION["ipaddr"] != $_SERVER["REMOTE_ADDR"])) {
            $_SESSION["message"] = "Access denied";
            return false;
        }
        return true;
    }

    /**
     * Saves the given configuration parameter and value
     * @param string $parameter
     * @param string $value
     * @return boolean true: updated, false: not updated
     */
    function saveConfig($parameter, $value) {
        $this->cfg->$parameter = $value;
        return $this->db->update('magirc_config', array('value' => $value), array('parameter' => $parameter));
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
     * Saves the HTML content for the given page
     * @param string $name Page name
     * @param string $text HTML content
     * @return boolean true: updated, false: not updated
     */
    function saveContent($name, $text) {
        $name = str_replace('content_', '', $name);
        return $this->db->update('magirc_content', array('text' => $text), array('name' => $name));
    }
}
