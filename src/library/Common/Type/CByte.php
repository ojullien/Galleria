<?php namespace Common\Type;
/**
 * Byte type class.
 *
 * This file contains a class which implements byte type and usefull
 * conversion methods for bytes.
 *
 * @package Common\Type
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This complex class implements byte type and usefull
 * conversion methods for bytes.
 */
final class CByte extends \Common\Type\CTypeAbstract
{

    /** Private methods
     ******************/

    /**
     * Test shorthand notation.
     * Returns TRUE if the value is a shorthand notation, FALSE otherwise.
     *
     * @param string $sValue The input value.
     * @param array $aMatches [optional] If $aMatches is provided,
     * then it is filled with the results of search.
     * @return boolean
     */
    private function isShorthanded($sValue, array& $aMatches = null)
    {
        $bReturn = FALSE;
        if( is_string($sValue) )
        {
            $sPattern = '/^[[:digit:].]+[kmg]$/i';
            $bReturn = (@preg_match( $sPattern, $sValue )) ? TRUE : FALSE;
            // Explode result
            if( isset($aMatches) && $bReturn )
            {
                $aMatches = str_split( $sValue, strlen($sValue)-1 );
            }//if( isset(...
        }//if( is_string(...
        return $bReturn;
    }

    /** Public methods
     *****************/

    /**
     * Constructor
     *
     * @param mixed $value
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __construct($value)
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, $value );
        }//if( defined(...
        //@codeCoverageIgnoreEnd
        $this->setValue($value);
    }

    /**
     * Writes data to variable.
     * Accept integer value and shorthand notation.
     *
     * @param integer|string $value
     * @return mixed
     */
    public function setValue($value)
    {
        if( $value instanceof \Common\Type\CTypeAbstract )
        {
            $value = $value->getValue();
        }
        // Trim string
        if( is_string($value) )
        {
            $value = trim($value);
        }//if( is_string(...

        // Numeric case
        if( !$this->setNumeric($value, 0) )
        {
            // Shorthanded notation case
            if( $this->isShorthanded($value) )
            {
                $this->_Value = $value;
            }//if(...
        }//if( !...
        return $value;
    }

    /**
     * Reads data from variable.
     *
     * @return numeric|NULL
     */
    public function getValue()
    {
        $mixedReturn = self::DEFAULT_VALUE;
        if( isset($this->_Value) )
        {
            $mixedReturn = $this->_Value;
        }//if(...
        return $mixedReturn;
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

    /**
     * Converts the value to MByte
     * @return mixed
     */
    public function convertToMByte()
    {
        // Default values
        $mixedReturn = self::DEFAULT_VALUE;
        $aMatches = array();

        if( isset($this->_Value) )
        {
            // There is a value to convert
            $mixedReturn = $this->_Value;

            if( $this->isShorthanded( $mixedReturn, $aMatches) )
            {
                // Shorthanded notation case
                switch ( strtolower($aMatches[1]) )
                {
                    case 'k':
                        // KByte to MByte.
                        $mixedReturn = $aMatches[0] / 1024;
                        break;
                    case 'g':
                        // GByte to MByte.
                        $mixedReturn = $aMatches[0] * 1024;
                        break;
                    default:
                        // Mbyte to MByte.
                        $mixedReturn = (float)$aMatches[0];
                        break;
                }//switch (...
            }
            else
            {
                // Convert from Byte to MByte
                $mixedReturn = $mixedReturn / 1024 / 1024;
            }//if(...

        }//if(...

        return $mixedReturn;
    }

}
