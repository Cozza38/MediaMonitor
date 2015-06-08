<?php
// Define ROOT_DIR and ROOT_URL if they aren't defined elsewhere
if(!defined('ROOT_DIR')) {
    define( 'ROOT_DIR', dirname(__FILE__) );
}
if(!defined('ROOT_URL')) {
    define( 'ROOT_URL', substr($_SERVER['PHP_SELF'], 0, -( strlen($_SERVER['SCRIPT_FILENAME']) - strlen(ROOT_DIR) )) );
}

require_once( 'vendor/autoload.php' );
