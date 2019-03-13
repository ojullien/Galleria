<?php namespace Common\Type;
/**
 * Parent Class for all types.
 *
 * This file contains a class which implements default variables and methods
 * for all types
 *
 * @package Common\Type
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * Parent class for all types
 */
abstract class CTypeAbstract
{
    /** Constants */
    const DEFAULT_VALUE = NULL;

    /** Protected attributs
     **********************/

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    protected $_sDebugID = '';

    /**
     * Value
     * @var mixed
     */
    protected $_Value = self::DEFAULT_VALUE;

    /** Protected methods
     ********************/

    /**
     * Writes mumeric data to variable property.
     * Returns FALSE if the value is not muneric (and positive if the option is set).
     *
     * @param numeric $value
     * @param numeric $min [OPTIONAL] Min
     * @param numeric $max [OPTIONAL] Max
     * @return boolean
     */
    final protected function setNumeric($value, $min = NULL, $max = NULL)
    {
        $bReturn = FALSE;
        $this->_Value = self::DEFAULT_VALUE;
        if( is_numeric($value) )
        {
            // Numeric case - Cast
            $value = (float)$value;

            // Min
            if( isset($min) && is_numeric($min) )
            {
                $value = ($value>=$min)?$value:self::DEFAULT_VALUE;
            }

            // Max
            if( isset($max) && is_numeric($max) && ($value!=self::DEFAULT_VALUE) )
            {
                $value = ($value<=$max)?$value:self::DEFAULT_VALUE;
            }

            // Ok
            if( isset($value) )
            {
                $this->_Value = $value;
                $bReturn = TRUE;
            }//if(...
        }//if( is_numeric(...
        return $bReturn;
    }

    /** Public methods
     *****************/

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
     * Determines if the variable is set and is not NULL.
     * Returns TRUE if the variable has value other than NULL, FALSE otherwise.
     *
     * @return boolean
     */
    public function isValid()
    {
        return isset($this->_Value);
    }

    /**
     * Gets variable length.
     *
     * @return int
     */
    public function getLength()
    {
        $iReturn = 0;
        if( isset($this->_Value) )
        {
            $iReturn = mb_strlen( $this->_Value, 'UTF-8' );
        }//if(...
        return $iReturn;
    }

    /**
     * Compare two objects. TRUE if the types and values are equals.
     *
     * @param \Common\Type\CTypeAbstract $pType
     * @return boolean
     */
    final public function isIdentical(\Common\Type\CTypeAbstract $pType)
    {
        $bInstance = (get_class($this) === get_class($pType) ) ? TRUE : FALSE;
        $bValue = ($this->getValue() === $pType->getValue() ) ? TRUE : FALSE;
        return ($bInstance && $bValue);
    }

    /**
     * Compare two objects. TRUE if the the values are equals.
     *
     * @param \Common\Type\CTypeAbstract $pType
     * @return boolean
     */
    final public function isEqual(\Common\Type\CTypeAbstract $pType)
    {
        return ($this->getValue() == $pType->getValue()) ? TRUE : FALSE;
    }

    /** Abstract methods
     *******************/

    /**
     * Create a new variable of some type
     *
     * @var mixed|null
     */
    abstract public function __construct($value);

    /**
     * Return to variable like a string
     *
     * @return string
     */
    abstract public function __toString();

    /**
     * Writes data to variable.
     *
     * @param mixed $value
     */
    abstract public function setValue($value);

    /**
     * Reads data from variable.
     *
     * @return mixed|NULL
     */
    abstract public function getValue();

}
