<?php namespace Common\Http;
/**
 * Parent Class for all download.
 *
 * This file contains a class which implements default constant and methods
 * for download.
 *
 * @package Common\Http
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * Parent class for download
 */
abstract class CDownloadAbstract
{
    /** Constants */
    const DEFAULT_VALUE = NULL;
    const DEFAULT_SIZE = 65536;

    /** Protected variables
     **********************/

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    protected $_sDebugID = '';

    /**
     * File to download
     * @var \SplFileInfo
     */
    protected $_Value = self::DEFAULT_VALUE;

    /** Protected methods
     ********************/

    /**
     * Gets the file extension. Returns a string containing the file extension,
     * or an empty string if the file has no extension.
     *
     * @return string
     * @todo PHP VERSION. SHALL BE UPDATED
     * @codeCoverageIgnore
     */
    final protected function getExtension()
    {
        $sReturn = '';
        if( isset($this->_Value) )
        {
            if( version_compare( phpversion(), '5.3.6', '>=') )
            {
                $sReturn = $this->_Value->getExtension();
            }
            else
            {
                $sReturn = pathinfo( $this->_Value->getFilename(), PATHINFO_EXTENSION);
            }//if( version_compare(...
        }//if( isset(...
        return strtolower($sReturn);
    }

    /** Public methods
     *****************/

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->_Value);
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') && !defined('COMMON_DEBUG_OFF') )
            \Common\Debug\CDebug::getInstance()->addMemoryDelete($this->_sDebugID);
        //@codeCoverageIgnoreEnd
    }

    /**
     * Writing data to inaccessible properties is not allowed.
     *
     * @param string $name
     * @param mixed $value
     * @codeCoverageIgnore
     */
    final public function __set($name, $value)
    {
        throw new \BadMethodCallException( 'Writing data to inaccessible properties is not allowed.' );
    }

    /**
     * Convert to string
     *
     * @return string
     */
    final public function __toString()
    {
        $sReturn = '';
        if( isset($this->_Value) )
        {
            $sReturn = (string)$this->_Value;
        }//if(...
        return $sReturn;
    }

    /**
     * Send a file. Returns FALSE on error.
     *
     * @param boolean $bSend FALSE if test mode (do not send headers and file)
     * @return boolean
     * @throws \RuntimeException If the file cannot be opened.
     */
    public function send( $bSend = TRUE )
    {
        // Send header for attachment
        $this->sendAttachment($bSend);

        // Send header for cache
        $bReturn = FALSE;
        //@codeCoverageIgnoreStart
        if( TRUE===$bSend )
        {
            header("Pragma: public" );
            header("Pragma: no-cache");
            header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Expires: 0" );
            // Send file
            $pHandle = fopen( $this->_Value->getPathname(), 'rb' );
            if( $pHandle!==FALSE )
            {
                while( !feof($pHandle) )
                {
                    $sBuffer = fread($pHandle, 65536);
                    if( $sBuffer!==FALSE )
                    {
                        echo $sBuffer;
                    }
                }//while(...
                fclose($pHandle);
                $bReturn = TRUE;
            }//if(...
        }
        else
        {
            $bReturn = TRUE;
        }//if(...
        //@codeCoverageIgnoreEnd
        return $bReturn;
    }

    /** Abstract methods
     *******************/

    /**
     * Send the HTTP header for an image download.
     *
     * @throws \RuntimeException If the file cannot be opened.
     */
    abstract protected function sendAttachment($bTest = FALSE);

}
