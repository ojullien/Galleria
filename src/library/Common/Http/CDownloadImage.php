<?php namespace Common\Http;
/**
 * Download an image.
 *
 * This file contains a class which implements methods for image download.
 *
 * @package Common\Http
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * class for image download.
 */
final class CDownloadImage extends \Common\Http\CDownloadAbstract
{

    /** Private variables
     ********************/

    /**
     * MIME type
     * @var string
     */
    private $_sMIME = 'application/octet-stream';

    /** Public methods
     *****************/

    /**
     * Constructor. The file should exists.
     *
     * @param \Common\Type\CPath $filename The file to read.
     * @throws \RuntimeException if the filename cannot be opened.
     */
    public function __construct( \Common\Type\CPath $filename )
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, $filename );
        }//if( defined(...
        //@codeCoverageIgnoreEnd

        $bCanBeSended = FALSE;
        if( $filename->isValid() )
        {
            $this->_Value = new \SplFileInfo( $filename->getValue() );
            // Check extension
            $bExtensionOK = TRUE;
            switch( $this->getExtension() )
            {
                case "gif" : $this->_sMIME = "image/gif"; break;
                case "png" : $this->_sMIME = "image/png"; break;
                case "jpeg":
                case "jpg" : $this->_sMIME = "image/jpeg"; break;
                default    : $bExtensionOK = FALSE; break;
            }//switch(...
            // Check if the image is a regular file
            $bCanBeSended = $bExtensionOK && $this->_Value->isFile() && $this->_Value->isReadable();
        }//if(...

        // Error
        if( FALSE==$bCanBeSended )
        {
            throw new \RuntimeException('The file cannot be opened.');
        }
    }

    /**
     * Send the HTTP header for an image download.
     *
     * @param boolean $bSend FALSE if test mode (do not send headers and file)
     * @throws \RuntimeException If the file cannot be opened.
     */
    protected function sendAttachment( $bSend = TRUE )
    {
        // Send header
        //@codeCoverageIgnoreStart
        if( TRUE===$bSend )
        {
            header('Content-Type: ' . $this->_sMIME );
            header('Content-disposition: attachment; filename="' . $this->_Value->getBasename() .'"' );
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . $this->_Value->getSize() );
        }
        //@codeCoverageIgnoreEnd
    }

}
