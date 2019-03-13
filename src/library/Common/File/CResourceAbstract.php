<?php namespace Common\File;
/**
 * File resource class.
 *
 * This file contains a class which implements main methods for file resources.
 *
 * @package Common\File
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class implements main methods for file resources.
 */
abstract class CResourceAbstract extends \SplFileInfo
{
    /** Constants */
    const DEFAULT_VALUE = NULL;

    /** Private variable
     *******************/

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    private $_sDebugID = '';

    /** Protected variables
     **********************/

    /**
     * Opened file object
     * @var \SplFileObject
     */
    protected $_pFileOpened = self::DEFAULT_VALUE;

    /** Protected methods
     ********************/

    /**
     * Manages directory.
     * Returns TRUE on success or FALSE on failure.
     *
     * @return boolean
     * @throws \InvalidArgumentException If the name of the directory is not valid
     */
    abstract protected function manageDirectory();

    /**
     * Manages file.
     * Returns TRUE on success or FALSE on failure.
     *
     * @return boolean
     * @throws \RuntimeException If the file cannot be opened (e.g. insufficient access rights).
     */
    abstract protected function manageFile();

    /** Public methods
     *****************/

    /**
     * Constructor.
     *
     * @param \Common\Type\CPath $sPath Name of the file. May contains path.
     * @throws \InvalidArgumentException If the path is not valid.
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
        $this->_pFileOpened = self::DEFAULT_VALUE;
        unset($this->_pFileOpened);
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
     * Returns TRUE if the resource exists. The resource may be a file, a dir or
     * a link.
     *
     * @return boolean
     */
    final public function exists()
    {
        return $this->isDir() || $this->isFile() || $this->isLink();
    }

    /**
     * Manages directory and file.
     * Returns TRUE or FALSE if an error occures.
     *
     * @return boolean
     * @throws \RuntimeException If the file cannot be opened (e.g. insufficient access rights).
     * @throws \InvalidArgumentException If the name of the directory is not valid.
     */
    final public function open()
    {
        $bReturn = FALSE;
        if( $this->manageDirectory() )
        {
            $bReturn = $this->manageFile();
        }
        return $bReturn;
    }

    /**
     * Gets the file extension. Returns a string containing the file extension,
     * or an empty string if the file has no extension.
     *
     * @return string
     * @todo PHP VERSION. SHALL BE UPDATED
     * @codeCoverageIgnore
     */
    final public function getExtension()
    {
        $sReturn = '';
        if( version_compare( phpversion(), '5.3.6', '>=') )
        {
            $sReturn = parent::getExtension();
        }
        else
        {
            $sReturn = pathinfo( $this->getFilename(), PATHINFO_EXTENSION);
        }//if( version_compare(...
        return strtolower($sReturn);
    }

}
