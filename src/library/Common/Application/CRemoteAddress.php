<?php namespace Common\Application;
/**
 * The IP address from which the user is viewing the current page.
 *
 * This file contains a class which represents the IP address from which the
 * user is viewing the current page.
 *
 * @package     Common\Application
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class represents the IP address from which the
 * user is viewing the current page.
 */
final class CRemoteAddress
{
    /** Constants */
    const DEFAULT_VALUE = NULL;

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    private $_sDebugID = '';

    /**
     * Remote address
     * @var \Common\Type\CString
     */
    private $_Value = self::DEFAULT_VALUE;

    /**
     * Constructor
     * @param string $value [OPTIONAL]
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __construct( $value = NULL )
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, $_SERVER );
        }//if( defined(...
        //@codeCoverageIgnoreEnd

        if( is_string($value) )
        {
            // Initialize
            $this->_Value = new \Common\Type\CString($value);
        }
        else
        {
            // Current
            if( isset($_SERVER) && is_array($_SERVER) && isset($_SERVER['REMOTE_ADDR']) )
            {
                $this->_Value = new \Common\Type\CString($_SERVER['REMOTE_ADDR']);
            }
            else
            {
                $this->_Value = new \Common\Type\CString('?.?.?.?');
            }//if( isset(...
        }//if( is_string(...
    }

    /**
     * Destructor
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
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
    public function __set($name, $value)
    {
        throw new \BadMethodCallException( 'Writing data to inaccessible properties is not allowed.' );
    }

    /**
     * Determines if the variable is set and is not NULL.
     * Returns TRUE if the variable has value other than NULL, FALSE otherwise.
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->_Value->isValid();
    }

   /**
     * Reads data from variable.
     *
     * @return string|NULL
     */
    public function getValue()
    {
        return $this->_Value->getValue();
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->_Value;
    }

}
