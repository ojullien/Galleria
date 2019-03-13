<?php namespace Common\File;
/**
 * Directory resource class.
 *
 * This file contains a class which implements main methods for directory resources
 *
 * @package Common\File
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class implements main methods for directory resources
 */
final class CDirectory extends \SplFileInfo
{
    /** Constants */
    const DEFAULT_VALUE = NULL;

    /** Private attributs
     ********************/

    /**
     * Path
     * @var \Common\Type\CPath
     */
    private $_sPath = self::DEFAULT_VALUE;

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    private $_sDebugID = '';

    /** Public methods
     *****************/

    /**
     * Constructor
     *
     * @param \Common\Type\CPath $sPath
     * @throws \InvalidArgumentException If the name of the directory is not valid
     */
    public function __construct( \Common\Type\CPath $sPath )
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, $sPath );
        }//if( defined(...
        //@codeCoverageIgnoreEnd

        if( $sPath->isValid() )
        {
            parent::__construct( (string)$sPath );
            $this->_sPath = $sPath;
        }
        else
        {
            throw new \InvalidArgumentException('Invalid argument.');
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset( $this->_sPath );
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') && !defined('COMMON_DEBUG_OFF') )
            \Common\Debug\CDebug::getInstance()->addMemoryDelete($this->_sDebugID);
        //@codeCoverageIgnoreEnd
    }

    /**
     * Reads data from variable.
     *
     * @return string
     */
    public function get()
    {
        return $this->_sPath->getValue();
    }

    /**
     * Returns TRUE if the resource exists. The resource may be a file, a dir or
     * a link.
     *
     * @return boolean
     */
    public function exists()
    {
        return $this->isDir() || $this->isFile() || $this->isLink();
    }

    /**
     * Reads data from variable.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->_sPath;
    }

    /**
     * Writing data to inaccessible properties is not allowed.
     *
     * @param string $name
     * @param mixed $value
     * @codeCoverageIgnore
     */
    public function __set($name, $value)
    {
        throw new \BadMethodCallException( 'Writing data to inaccessible properties is not allowed.' );
    }

    /**
     * Makes directory if not exists.
     * Returns TRUE on success or FALSE on failure.
     *
     * @return boolean
     */
    public function createDirectory()
    {
        $bReturn = $this->exists();
        if( !$bReturn && $this->_sPath->isValid() )
        {
            $bReturn = mkdir( (string)$this->_sPath, 0770, TRUE);
            if( $bReturn )
            {
                $this->_sPath->setValue( $this->getRealPath() );
            }
        }
        return $bReturn;
    }

}
