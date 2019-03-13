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
 *
 * @package     File
 * @subpackage  Log
 * @category    Common
 * @version     1.0.0
 * @since       1.0.0
 */
final class CLog extends \Common\File\CResourceAbstract
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
     * @throws \RuntimeException If an error occures.
     */
    protected function manageFile()
    {
        $sMode = 'w+b';

        // Clears file status cache
        clearstatcache();

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
            }
            else
            {
                // The file is not full, append data
                $sMode = 'a+b';
            }//if( $this->getSize() >= $this->_iMaxSize )
        }//if( $this->exists() )

        // Open the file
        $this->_pFileOpened = $this->openFile($sMode);

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
        $iWritten = NULL;
        if( isset($this->_pFileOpened) )
        {
            $sBuffer  = '['         . date('D M d G:i:s Y') . ']';
            $sBuffer .= ' ['        . (string)$enumPriority . ']';
            $sBuffer .= ' [client ' . ((isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'unknown') . ']';
            $sBuffer .= ' [user '   . ((isset($sUser)) ? (string)$sUser : 'visitor') . ']';
            $sBuffer .= ' '         . (string)$sMessage     . PHP_EOL;

            // Write
            $iWritten = $this->_pFileOpened->fwrite( $sBuffer );

        }//if( isset(...

        // flush
        if( is_null($iWritten) )
        {
            $iWritten = FALSE;
        }
        else
        {
            $this->_pFileOpened->fflush();
        }//if( is_null(...

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
