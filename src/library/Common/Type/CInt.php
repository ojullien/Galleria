<?php namespace Common\Type;
/**
 * Integer type class.
 *
 * This file contains a class which enforces strong typing of the integer type
 *
 * @package Common\Type
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class enforces strong typing of the integer type.
 */
final class CInt extends \Common\Type\CTypeAbstract
{
    /**
     * Constructor
     *
     * @param integer $value
     * @param integer $iMin [OPTIONAL] Min
     * @param integer $iMax [OPTIONAL] Max
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __construct( $value, $iMin = NULL, $iMax = NULL)
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, $value );
        }//if( defined(...
        //@codeCoverageIgnoreEnd
        $this->setValue($value, $iMin, $iMax);
    }

    /**
     * Writes data to variable.
     *
     * @param integer $value
     * @param integer $iMin [OPTIONAL] Minimum value
     * @param integer $iMax [OPTIONAL] Maximum value
     * @return mixed
     */
    public function setValue($value, $iMin = NULL, $iMax = NULL)
    {
        if( $value instanceof \Common\Type\CTypeAbstract )
        {
            $value = $value->getValue();
        }
        if( is_string($value) )
        {
            $value = trim($value);
        }//if( is_string(...
        $this->setNumeric( $value, $iMin, $iMax );
        return $value;
    }

    /**
     * Reads data from variable.
     *
     * @return integer|NULL
     */
    public function getValue()
    {
        $iReturn = self::DEFAULT_VALUE;
        if( isset($this->_Value) )
        {
            $iReturn = (int)$this->_Value;
        }//if(...
        return $iReturn;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        $sReturn = '';
        if( isset($this->_Value) )
        {
            $sReturn = sprintf( '%d', $this->_Value );
        }//if(...
        return $sReturn;
    }

}
