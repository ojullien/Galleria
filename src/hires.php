<?php
/**
 * Download image.
 *
 * This script allows to download a specified hi-res image from a specified
 * gallery.
 */

/** Load configuration settings
 ******************************/
require_once dirname(__FILE__) . '/library/Galleria/config.php';

/** Download
 ***********/
try
{
    // Get inputs
    require_once 'Common/Filter/CFilters.php';
    $pFilters = new \Common\Filter\CFilters();
    $sType = new \Common\Type\CInt( FILTER_SANITIZE_STRING );

    // Get gallery
    $sPattern = new \Common\Type\CString( '/^[a-z0-9]*$/i' );
    $sVariable =  new \Common\Type\CString( EV_TAGGALL );
    $sGallery = new \Common\Type\CString( $pFilters->filter( $_GET, $sVariable, $sType )
                                        , $sPattern);

    // Get image
    $sPattern->setValue( '/^[a-z0-9' . preg_quote('._-','/') . ']*$/i' );
    $sVariable->setValue( EV_TAGIMAG );
    $sImage = new \Common\Type\CString( $pFilters->filter( $_GET, $sVariable, $sType ) );
    unset( $sVariable, $sPattern, $sType, $pFilters );

    // Build path
    $sPath = NULL;
    if( $sImage->isValid() && $sGallery->isValid() )
    {
        require_once 'Common/Type/CPath.php';
        $sPath = new \Common\Type\CPath( EV_DIRGALL . DIRECTORY_SEPARATOR . $sGallery
                                                    . DIRECTORY_SEPARATOR . EV_GALHRES
                                                    . DIRECTORY_SEPARATOR . $sImage );
    }
    unset($sImage,$sGallery);

    // Send image
    if( isset($sPath) )
    {
        require_once 'Common/Http/CDownloadAbstract.php';
        require_once 'Common/Http/CDownloadImage.php';
        $pDownload = new \Common\Http\CDownloadImage($sPath);
        $pDownload->Send();
        unset($pDownload,$sPath);
        die(0);
    }
    else
    {
        throw new \RuntimeException('The file cannot be opened.');
    }// if( isset(...

}
catch( Exception $exception)
{
    // Time
    $sReturn = 'Server time: ' . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;
    // Exception
    $sReturn .= 'Message: ' . $exception->getMessage() . PHP_EOL . PHP_EOL;
    $sReturn .= 'Stack trace: ' . PHP_EOL;
    $sReturn .= $exception->getTraceAsString();
    $sReturn .= PHP_EOL . PHP_EOL;
    // Request
    $sReturn .= "Request Parameters: " . PHP_EOL;
    if( isset($_GET) && is_array($_GET) && (count($_GET)>0) )
        $sReturn .= '_GET => ' . print_r($_GET, TRUE) . PHP_EOL;
    else
        $sReturn .= '_GET => EMPTY' . PHP_EOL;
    if( isset($_POST) && is_array($_POST) && (count($_POST)>0) )
        $sReturn .= '_POST => ' . print_r($_POST, TRUE) . PHP_EOL;
    else
        $sReturn .= '_POST => EMPTY' . PHP_EOL;
    $sReturn .= PHP_EOL;

    // Log
    $sFile = EV_DIRLOGS . DIRECTORY_SEPARATOR . 'critical-error_' . date('Ymd_His') . '.log';
    $pFile = new \SplFileObject($sFile,'w+b');
    $pFile->fwrite($sReturn);
    unset($pFile);

    // Display error
    if( APPLICATION_ENV=='development')
    {
        echo '<pre>' . htmlentities( $sReturn , ENT_QUOTES, 'UTF-8'). '</pre>', PHP_EOL;
        echo '<pre>' . print_r( $exception->getPrevious(), TRUE ) . '</pre>', PHP_EOL;
    }
    else
    {
        header("HTTP/1.0 404 Not Found");
        exit;
    }

}

/** Debug
 ********/
if( defined('COMMON_DEBUG') )
{
    require_once 'Common/File/CResourceAbstract.php';
    require_once 'Common/Debug/CDebugGraph.php';
    echo \Common\Debug\CDebug::getInstance()->render(), PHP_EOL;
    \Common\Debug\CDebug::deleteInstance();
}
