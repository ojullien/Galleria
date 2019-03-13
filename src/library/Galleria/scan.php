<?php
/**
 *  This script scan and updates galleries. May also generates html files.
 *
 * @package Galleria
 */

/** Load configuration settings
 ******************************/
require_once dirname(__FILE__) . '/config.php';

/** Application settings
 ***********************/
define('EV_GALSLID', 'slides' );
define('EV_GALTHUM', 'thumbs' );
define('EV_SLIDEW', 500 );
define('EV_SLIDEH', 332 );
define('EV_THUMBW', 75 );
define('EV_THUMBH', 75 );

/** Requires
 ***********/
require_once( 'Gallerie/application.php' );
require_once( 'Gallerie/file.php' );
require_once( 'Gallerie/gd.php' );
require_once( 'Gallerie/layout.php' );

/** Usefull functions
 ********************/

function parse( \DirectoryIterator $pGallery )
{
    // Initialize
    $aReturn = '';
    $iProgress=0;
    $iIndex=0;
    $pPathIn = new \Common\Type\CPath(NULL);
    $pPathSlide = new \Common\Type\CPath(NULL);
    $pPathThumb = new \Common\Type\CPath(NULL);
    $pSlideDimensions = new \Common\Gd\CDimensions( EV_SLIDEW, EV_SLIDEH );
    $pThumbDimensions = new \Common\Gd\CDimensions( EV_THUMBW, EV_THUMBH );

    // Get the images
    $aImages = glob( $pGallery->getRealPath() . DIRECTORY_SEPARATOR . EV_GALHRES
            . DIRECTORY_SEPARATOR . '{*.png,*.jpg,*.PNG,*.JPG}' ,  GLOB_BRACE );

    // Get the count
    $iMax = count($aImages)*2;

    // For each image
    foreach( $aImages as $image )
    {
        // Get file info
        $pPathIn->setValue($image);
        $pPathSlide->setValue( $pGallery->getRealPath() . DIRECTORY_SEPARATOR
                . EV_GALSLID . DIRECTORY_SEPARATOR . $pPathIn->getBasename() );
        $pPathThumb->setValue( $pGallery->getRealPath() . DIRECTORY_SEPARATOR
                . EV_GALTHUM . DIRECTORY_SEPARATOR . $pPathIn->getBasename() );

        $bValid = $pPathIn->isValid() && $pPathSlide->isValid() && $pPathThumb->isValid();
/*        if( $bValid && (!file_exists( $pPathSlide->getValue() ) || !file_exists( $pPathThumb->getValue() )))
        {
            // Load hi-res image
            $pImage = new \Common\Gd\CImage($pPathIn);
            // Create slide
            $bReturn = createSlide( $pImage, $pSlideDimensions, $pPathSlide );
            printCount( $bReturn, ++$iProgress, $iMax);
            // Create thumb
            $bReturn = $bReturn && createThumb( $pImage, $pThumbDimensions, $pPathThumb );
            printCount( $bReturn, ++$iProgress, $iMax);
            unset($pImage);
            // Stop if error
            if( !$bReturn )
            {
                $aReturn = FALSE;
                break;
            }
            else
            {
                // Generate file
                $aReturn[] = getLine( $pGallery, $pPathIn, ++$iIndex );
            }
        }
*/
        if( $bValid )
        {
            // Initialize
            $bReturn = TRUE;
            $pImage = NULL;

            // Slide case
            if( !file_exists( $pPathSlide->getValue() ) )
            {
                // Load hi-res image
                $pImage = new \Common\Gd\CImage($pPathIn);
                // Create slide
                $bReturn = createSlide( $pImage, $pSlideDimensions, $pPathSlide );
            }// if( !file_exists(...
            printCount( $bReturn, ++$iProgress, $iMax);

            // Thumb case
            if( !file_exists( $pPathThumb->getValue() ))
            {
                // Load hi-res image
                if( !isset($pImage) )
                {
                    $pImage = new \Common\Gd\CImage($pPathIn);
                }
                // Create thumb
                $bReturn = $bReturn && createThumb( $pImage, $pThumbDimensions, $pPathThumb );
            }// if( !file_exists(...
            printCount( $bReturn, ++$iProgress, $iMax);

            // Unload hi-res image
            unset($pImage);

            // Stop if error
            if( !$bReturn )
            {
                $aReturn = FALSE;
                break;
            }
            else
            {
                // Generate file
                $aReturn[] = getLine( $pGallery, $pPathIn, ++$iIndex );
            }
        }//if(...
    }// foreach(...
    unset( $pSlideDimensions, $pThumbDimensions, $pPathIn, $pPathThumb, $pPathSlide );
    return $aReturn;
}

/** Main
 *******/
try
{
    set_time_limit ( 0 );

    // Get the galleries
    $values = new \DirectoryIterator( EV_DIRGALL );
    foreach( $values as $value )
    {
        if( $value->isDir() && !$value->isDot() )
        {
            display( ' ', TRUE );
            display( 'Scan: ' . $value->getBasename(), TRUE);
            display( ' ', TRUE );

            // verify
            if( !$value->isReadable() || !$value->isWritable() )
            {
                throw new \RuntimeException( $value->getBasename() . ' is not readable nor writable.' );
            }

            // Verify hi-res, slides and thumbs directories
            if( !verifyDirectories($value) )
            {
                throw new \RuntimeException( $value->getBasename() . '\'s directories are not readable nor writable.' );
            }

            // Parse
            $aLines = parse($value);

            // Build html file
            if(is_array($aLines) )
            {
                display( ' ', TRUE );
                display( 'Build: ' . $value->getBasename(), TRUE);
                display( ' ', TRUE );
                build( $value, $aLines );
            }

        }
    }
    unset($values);

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
