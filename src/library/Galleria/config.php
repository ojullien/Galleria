<?php
/**
 * Configuration file.
 *
 * This file contains configuration settings
 *
 * @package Galleria
 */

/** Environment settings
 ***********************/

// Define version
define('APPLICATION_NAME', 'EV');
define('APPLICATION_VERSION', '2012.09.12');

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV'
     , (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production') );

// Define the directory separator for windows or unix environment
defined('DIRECTORY_SEPARATOR') || define('DIRECTORY_SEPARATOR','/');

// Define the absolute path to the project path and zend library.
// Define base URL
define('PROJECT_PATH'    , realpath('/var/www/galleria') );
define('APPLICATION_URL' , 'https://www.galleria.it/');

// Define the absolute paths to the library
define('LIBRARY_PATH', realpath( PROJECT_PATH . DIRECTORY_SEPARATOR . 'library') );

// Ensure library is on include_path
$aPaths = array( LIBRARY_PATH, get_include_path());
set_include_path( implode( PATH_SEPARATOR, $aPaths));

// Timestamp of the start of the application, with microsecond's precision
$sKey = APPLICATION_NAME . '_START_TIME';
if( !isset($_SERVER[$sKey]) )
    $_SERVER[$sKey] = microtime(true);

/** Require
 **********/

// Load classes
require_once 'Common/Type/CTypeAbstract.php';
require_once 'Common/Type/CByte.php';
require_once 'Common/Type/CPath.php';
require_once 'Common/Type/CInt.php';
require_once 'Common/Type/CString.php';

// Load debug class
// if( !defined('STDIN') )
    // require_once 'Common/Debug/CDebug.php';

/** Application settings
 ***********************/
define( 'EV_DIRDATA', realpath( PROJECT_PATH . DIRECTORY_SEPARATOR . 'data') );
define( 'EV_DIRGALL', realpath(   EV_DIRDATA . DIRECTORY_SEPARATOR . 'gallerie') );
define( 'EV_DIRLOGS', realpath(   EV_DIRDATA . DIRECTORY_SEPARATOR . 'logs') );
define( 'EV_GALHRES', 'hi-res' );
define( 'EV_TAGGALL', 'gal' );
define( 'EV_TAGIMAG', 'img' );
