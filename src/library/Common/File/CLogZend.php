<?php namespace Common\File;
/**
 * Log
 *
 * This file contains a class for general purpose logging.
 *
 * @package Common\File
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class implements usefull functions for logging purpose.
 */
final class CLogZend extends \Common\File\CResourceAbstract
                     implements \Common\File\CLogInterface
{
    /** Constants */
    const LOGFILESIZE = 1048576;

    /**
     * Max size of the file
     * @var integer
     */
    private $_iMaxSize = self::LOGFILESIZE;

    /**
     * Creates directory if not exists.
     * Returns TRUE on success or FALSE on failure.
     *
     * @return boolean
     * @throws \InvalidArgumentException If the name of the directory is not valid.
     */
    protected function manageDirectory()
    {
        $pPath = new \Common\Type\CPath( $this->getPath() );
        $pDirectory = new \Common\File\CDirectory( $pPath );
        $bReturn = $pDirectory->createDirectory();
        unset($pDirectory,$pPath);
        return $bReturn;
    }

    /**
     * Opens file. If is full, renames it, creates and opens a new one.
     * Returns TRUE on success or FALSE on failure.
     *
     * @return boolean
     * @throws \Zend_Log_Exception If an error occures.
     */
    protected function manageFile()
    {
        // Check if the file exists and the size is OK
        if( $this->exists() )
        {
            if( !$this->isFile() )
            {
                throw new \RuntimeException( 'The resource exists but is not a regular file.' );
            }//if( !$this->isFile() )

            // Check the size
            if( $this->getSize() >= $this->_iMaxSize )
            {
                // The file is full, rename it, create and open a new one.
                $sExtension = $this->getExtension();
                $sNameNew = $this->getPath() . DIRECTORY_SEPARATOR
                          . $this->getBasename($sExtension)
                          . date('Ymd_His')
                          . '.' . $sExtension;
                $sNameOld = $this->getPathname();
                rename( $sNameOld, $sNameNew );
            }//if( $this->getSize() >= $this->_iMaxSize )
        }//if( $this->exists() )

        // Open the file
        $writer = new \Zend_Log_Writer_Stream( $this->getPathname() );
        $formatter = new \Zend_Log_Formatter_Simple( '[%timestamp%] [%priorityName%] [client %client%] [user %user%] %message%' . PHP_EOL );
        $writer->setFormatter($formatter);
        $this->_pFileOpened = new \Zend_Log($writer);
        $this->_pFileOpened->setTimestampFormat("D M d G:i:s Y");
        unset($formatter,$writer);
        return TRUE;
    }

    /**
     * Write to file.
     * Returns the number of bytes written, or FALSE on error.
     *
     * @param \Common\Type\CString       $sMessage     Message
     * @param \Common\Type\CEnumPriority $enumPriority Priority
     * @param \Common\Type\CString       $sUser        [Optional] User name
     * @return integer|boolean
     */
    public function write( \Common\Type\CString $sMessage,
                           \Common\Type\CEnumPriority $enumPriority,
                           \Common\Type\CString $sUser = NULL )
    {
        $iWritten = FALSE;
        if( isset($this->_pFileOpened) )
        {
            $this->_pFileOpened->log( (string)$sMessage,
                    $enumPriority->getValue(),
                    array( 'client'=> ((isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'unknown'),
                             'user'=> ((isset($sUser)) ? (string)$sUser : 'visitor') ));
            $iWritten = 1;
        }//if( isset(...
        return $iWritten;
    }

    /**
     * Set the maximum file size.
     *
     * @param  integer $value In bytes. Min 1048576 bytes.
     * @return integer The old value
     */
    public function setMaxFileSize( $value )
    {
        // Initialize
        $iReturn = $this->_iMaxSize;
        $pValue = new \Common\Type\CInt( $value, self::LOGFILESIZE+1 );
        // Set
        if( $pValue->isValid() )
        {
            $this->_iMaxSize = $pValue->getValue();
        }
        unset($pValue);
        return $iReturn;
    }

}
