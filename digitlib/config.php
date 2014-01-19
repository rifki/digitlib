<?php
/**
 * Config constans
 * @author Muhamad Rifki <rifki@rifkilabs.net>
 * @version 1.1
 */

/**
 * Config Environment
 */
define('ENV', 'development');
if (defined(ENV)) {
    switch (ENV) {
        case 'development':
            ini_set('display_errors', 1);
            error_reporting(E_ALL ^ E_NOTICE);
            break;
        case 'production':
            ini_set('display_errors', 0);
            error_reporting(0);
            break;
        default:
            exit('Environment not set');
    }
}

/**
 * Database server
 * Example localhost
 */
defined('DB_SERVER') ? null : define('DB_SERVER', 'localhost');

/**
 * Database username
 */
defined('DB_USERNAME') ? null : define('DB_USERNAME', 'root');

/**
 * Database password
 */
defined('DB_PASSWORD') ? null : define('DB_PASSWORD', 'root');

/**
 * Database name
 */
defined('DB_NAME') ? null : define('DB_NAME', 'dbname');

/**
 * Base url
 * example: http://example.com/microsite/quiz
 */
defined('BASE_URL') ? null : define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].'/microsite/quiz/');

defined('SITE_PATH') ? null : define('SITE_PATH', __DIR__);

/**
 * Facebook appliation id
 */
defined('FB_APP_ID') ? null : define('FB_APP_ID', 'YOUR_FB_APP_ID');

/**
 * Facebook application secret
 */
defined('FB_APP_SECRET') ? null : define('FB_APP_SECRET', 'YOUR_FB_SECRET');

/**
 * Today: time()
 * For detail: http://php.net/manual/en/function.time.php
 */
defined('TODAY') ? null : define('TODAY', time());
