<?php
/**
 * Config constans
 * @author Muhamad Rifki <rifki@rifkilabs.net>
 * @version 1.0.1
 */

/**
 * Default timezone
 */
date_default_timezone_set("Asia/Jakarta");

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
defined('DB_USERNAME') ? null : define('DB_USERNAME', 'username');

/**
 * Database password
 */
defined('DB_PASSWORD') ? null : define('DB_PASSWORD', 'password');

/**
 * Database name
 */
defined('DB_NAME') ? null : define('DB_NAME', 'dbname');

/**
 * Base url
 * example: http://example.com/microsite/quiz
 */
defined('BASE_URL') ? null : define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].'/microsite/quiz/');
defined('SITE_URL') ? null : define('SITE_URL', BASE_URL);
defined('URL_HOST') ? null : define('URL_HOST', BASE_URL);

/**
 * Directory Path
 */
defined('SITE_PATH') ? null : define('SITE_PATH', dirname(__DIR__));
defined('DIR_PATH') ? null : define('DIR_PATH', SITE_PATH);

/**
 * Facebook Appliation Setting
 */
defined('FB_APP_ID') ? null : define('FB_APP_ID', 'YOUR_FB_APP_ID');
defined('FB_APP_SECRET') ? null : define('FB_APP_SECRET', 'YOUR_FB_APP_SECRET');

/**
 * Twitter OAuth Settings
 */
defined('CONSUMER_KEY') ? null : define('CONSUMER_KEY', 'YOUR_CONSUMER_KEY');
defined('CONSUMER_SECRET') ? null : define('CONSUMER_SECRET', 'YOUR_CONSUMER_SECRET');
defined('ACCESS_TOKEN') ? null : define('ACCESS_TOKEN', 'YOUR_ACCESS_TOKEN');
defined('ACCESS_TOKEN_SECRET') ? null : define('ACCESS_TOKEN_SECRET', 'YOUR_ACCESS_TOKEN_SECRET');
defined('OAUTH_CALLBACK') ? null : define('OAUTH_CALLBACK', BASE_URL.'your_callback.php?provider=twitter');

/**
 * Today: time()
 * For detail: http://php.net/manual/en/function.time.php
 */
defined('TODAY') ? null : define('TODAY', time());

