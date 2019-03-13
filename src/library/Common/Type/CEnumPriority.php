<?php namespace Common\Type;
/**
 * Priorities enumeration type class.
 *
 * This file contains a class which implements enumeration type for priorities.
 *
 * @package Common\Type
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class implements enumeration type for priorities.
 */
final class CEnumPriority
{
    // The priorities come from the BSD syslog protocol, which is described
    // in the RFC-3164.
    const EMERG   = 0;  // Emergency: system is unusable
    const ALERT   = 1;  // Alert: action must be taken immediately
    const CRIT    = 2;  // Critical: critical conditions
    const ERR     = 3;  // Error: error conditions
    const WARN    = 4;  // Warning: warning conditions
    const NOTICE  = 5;  // Notice: normal but significant condition
    const INFO    = 6;  // Informational: informational messages
    const DEBUG   = 7;  // Debug: debug messages

    /**
     * Current value
     * @var integer
     */
    private $_Value = self::CRIT;

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    protected $_sDebugID = '';

    /**
     * Constructor
     *
     * @param integer $value. [OPTIONAL]. One of the constants.
     * @throws \UnexpectedValueException If the value is not valid.
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __construct( $value=self::ERR )
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, $value );
        }//if( defined(...
        //@codeCoverageIgnoreEnd

        if( is_integer($value) && ($value>=self::EMERG) && ($value<=self::DEBUG) )
        {
            $this->_Value = $value;
        }
        else
        {
            throw new \UnexpectedValueException( 'The argument 1 is not an expected values.' );
        }
    }

    /**
     * Destructor
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __destruct()
    {
        unset( $this->_Value );
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
     * Reads data from variable
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->_Value;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        switch ($this->_Value)
        {
            case self::EMERG:
                $sReturn = 'emergency';
                break;
            case self::ALERT:
                $sReturn = 'alert';
                break;
            case self::CRIT:
                $sReturn = 'critical';
                break;
            case self::WARN:
                $sReturn = 'warning';
                break;
            case self::NOTICE:
                $sReturn = 'notice';
                break;
            case self::INFO:
                $sReturn = 'informational';
                break;
            case self::DEBUG:
                $sReturn = 'debug';
                break;
            default:
                $sReturn = 'error';
                break;
        }
        return $sReturn;
    }

}
