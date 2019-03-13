<?php namespace Common\Gd;
/**
 * Coordinates.
 *
 * This file contains a class which enforces strong typing of the coordinate type
 *
 * @package Common\Gd
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class enforces strong typing of the image coordinate type.
 */
final class CCoordinates
{
    /** Constants */
    const DEFAULT_VALUE = FALSE;

    /** Private variables
     ********************/

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    private $_sDebugID = '';

    /**
     * X-axis
     * @var float
     */
    private $_fX = self::DEFAULT_VALUE;

    /**
     * Y-axis
     * @var float
     */
    private $_fY = self::DEFAULT_VALUE;

    /**
     * Z-axis
     * @var float
     */
    private $_fZ = self::DEFAULT_VALUE;

    /** Public methods
     *****************/

    /**
     * Destructor
     */
    public function __destruct()
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') && !defined('COMMON_DEBUG_OFF') )
            \Common\Debug\CDebug::getInstance()->addMemoryDelete($this->_sDebugID);
        //@codeCoverageIgnoreEnd
    }

    /**
     * Constructor.
     *
     * @param float $x >=0
     * @param float $y >=0
     * @param float $z >=0
     * @throws \InvalidArgumentException if the parameters are not valid.
     */
    public function __construct( $x, $y, $z )
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, 'x="'.$x.'" y="'.$y.'" z="'.$z.'"' );
        }//if( defined(...
        //@codeCoverageIgnoreEnd

        // Check x-axis
        $pValidator = new \Common\Type\CFloat( $x, 0.0 );
        if( $pValidator->isValid() )
        {
            $this->_fX = $pValidator->getValue();
        }

        // Check y-axis
        $pValidator->setValue( $y, 0.0 );
        if( $pValidator->isValid() )
        {
            $this->_fY = $pValidator->getValue();
        }

        // Check z-axis
        $pValidator->setValue( $z, 0.0 );
        if( $pValidator->isValid() )
        {
            $this->_fZ = $pValidator->getValue();
        }
        unset($pValidator);

        if( ($this->_fX===self::DEFAULT_VALUE)
                || ($this->_fY===self::DEFAULT_VALUE)
                || ($this->_fZ===self::DEFAULT_VALUE) )
        {
            throw new \InvalidArgumentException( 'Invalid arguments.' );
        }
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
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return serialize( array( 'x'=>(string)$this->_fX
                                ,'y'=>(string)$this->_fY
                                ,'z'=>(string)$this->_fZ ));
    }

    /**
     * Returns X-axis value
     * @return float
     */
    public function getX()
    {
        return $this->_fX;
    }

    /**
     * Returns Y-axis value
     * @return float
     */
    public function getY()
    {
        return $this->_fY;
    }

    /**
     * Returns Z-axis value
     * @return float
     */
    public function getZ()
    {
        return $this->_fZ;
    }

}
