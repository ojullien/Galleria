<?php namespace Common\Type;
/**
 * Float/Double type class.
 *
 * This file contains a class which enforces strong typing of the
 * float/double type
 *
 * @package Common\Type
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class enforces strong typing of the float/double type.
 */
final class CFloat extends \Common\Type\CTypeAbstract
{
    /**
     * Constructor
     *
     * @param float $value
     * @param float $fMin [OPTIONAL] Min
     * @param float $fMax [OPTIONAL] Max
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __construct($value, $fMin = NULL, $fMax = NULL)
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, $value );
        }//if( defined(...
        //@codeCoverageIgnoreEnd
        $this->setValue($value, $fMin, $fMax);
    }

    /**
     * Writes data to variable.
     *
     * @param float $value
     * @param float $fMin [OPTIONAL] Min
     * @param float $fMax [OPTIONAL] Max
     * @return mixed
     */
    public function setValue($value, $fMin = NULL, $fMax = NULL)
    {
        if( $value instanceof \Common\Type\CTypeAbstract )
        {
            $value = $value->getValue();
        }
        if( is_string($value) )
        {
            $value = trim($value);
        }//if( is_string(...
        $this->setNumeric( $value, $fMin, $fMax );
        return $value;
    }

    /**
     * Reads data from variable.
     *
     * @return float|NULL
     */
    public function getValue()
    {
        $iReturn = self::DEFAULT_VALUE;
        if( isset($this->_Value) )
        {
            $iReturn = (float)$this->_Value;
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
            $sReturn = (string)$this->_Value;
        }//if(...
        return $sReturn;
    }

}
