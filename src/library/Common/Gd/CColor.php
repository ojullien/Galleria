<?php namespace Common\Gd;
/**
 * RGBA color type class.
 *
 * This file contains a class which enforces strong typing of the RGBA color type
 *
 * @package Common\Gd
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class enforces strong typing of the RGBA color type.
 */
final class CColor
{

    /** Constants */
    const DEFAULT_VALUE = NULL;

    /** Private attributs
     ********************/

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    private $_sDebugID = '';

    /**
     * Value
     * @var \SplFixedArray
     */
    private $_Value = self::DEFAULT_VALUE;

   /** Private methods
     *****************/

    /**
     * Writes data to variable.
     *
     * @param array $value Array should contains four values (red,green,blue, transparency)
     */
    private function setValue( $value )
    {
        // Initialize
        $this->_Value = self::DEFAULT_VALUE;

        // Check parameter's type
        if( $value instanceof \Common\Gd\CColor )
        {
            $value = $value->toArray();
        }
        elseif( $value instanceof \SplFixedArray )
        {
            $value = $value->toArray();
        }

        // Check argument value
        if( is_array($value) && (count($value)>3) )
        {
            $iRed = new \Common\Type\CInt( $value[0], 0, 255);
            $iGreen = new \Common\Type\CInt( $value[1], 0,255);
            $iBlue = new \Common\Type\CInt( $value[2], 0,255);
            $iTransparency = new \Common\Type\CInt( $value[3], 0,127);
            if( $iRed->isValid()
             && $iGreen->isValid()
             && $iBlue->isValid()
             && $iTransparency->isValid() )
            {
                $this->_Value = new \SplFixedArray(4);
                $this->_Value[0] = $iRed->getValue();
                $this->_Value[1] = $iGreen->getValue();
                $this->_Value[2] = $iBlue->getValue();
                $this->_Value[3] = $iTransparency->getValue();
            }
            unset($iRed,$iGreen,$iBlue,$iTransparency);
        }
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
    public function __set($name, $value)
    {
        throw new \BadMethodCallException( 'Writing data to inaccessible properties is not allowed.' );
    }

    /**
     * Constructor
     *
     @param array $value Array should contains four values (red,green,blue, transparency)
     * @throws \InvalidArgumentException if the parameter is not valid.
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __construct( $value )
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, $value );
        }//if( defined(...
        //@codeCoverageIgnoreEnd
        $this->setValue($value);
        if( !isset($this->_Value) )
        {
            throw new \InvalidArgumentException( 'Invalid argument.' );
        }
    }

    /**
     * Reads data from variable.
     *
     * @return SplFixedArray|NULL
     */
    public function getValue()
    {
        $aReturn = self::DEFAULT_VALUE;
        if( isset($this->_Value) )
        {
            $aReturn = $this->_Value;
        }//if(...
        return $aReturn;
    }

    /**
     * Returns a PHP array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_Value->toArray();
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
            $sReturn = serialize($this->_Value);
        }//if(...
        return $sReturn;
    }

}
