<?php namespace Common\Gd;
/**
 * Dimensions.
 *
 * This file contains a class which enforces strong typing of the dimension type
 *
 * @package Common\Gd
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class enforces strong typing of the image dimension type.
 */
final class CDimensions
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
     * Width
     * @var integer
     */
    private $_iWidth = self::DEFAULT_VALUE;

    /**
     * Height
     * @var integer
     */
    private $_iHeight = self::DEFAULT_VALUE;

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
     * @param integer $width
     * @param integer $height
     * @throws \InvalidArgumentException if the parameters are not valid.
     */
    public function __construct( $width, $height )
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, 'w="'.$width.'" h='.$height.'"' );
        }//if( defined(...
        //@codeCoverageIgnoreEnd

        // Check width
        $pValidator = new \Common\Type\CInt( $width, 1 );
        if( $pValidator->isValid() )
        {
            $this->_iWidth = $pValidator->getValue();
        }

        // Check height
        $pValidator->setValue( $height, 1 );
        if( $pValidator->isValid() )
        {
            $this->_iHeight = $pValidator->getValue();
        }
        unset($pValidator);

        if( ($this->_iWidth===FALSE) || ($this->_iHeight===FALSE) )
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
        return serialize( array( 'width'=>(string)$this->_iWidth
                               ,'height'=>(string)$this->_iHeight ));
    }

    /**
     * Returns width
     * @return integer
     */
    public function getWidth()
    {
        return $this->_iWidth;
    }

    /**
     * Returns height
     * @return integer
     */
    public function getHeight()
    {
        return $this->_iHeight;
    }

    /** Usefull functions
     ********************/

    /**
     * Computes the new size of $iSize based on the aspect ratio calculated from $iFrom and $iTo.
     *
     * Example: if $iFrom is old size of width, $iTo is new  size of width and $iSize is
     * old size of height then this function returns the new size of height.
     *
     * @param integer $iFrom
     * @param integer $iTo
     * @param integer $iSize
     * @return integer
     */
    private function scale( $iFrom, $iTo, $iSize )
    {
        $iRatio = 100 * $iTo / $iFrom;
        return ceil($iSize * $iRatio / 100);
    }

    /**
     * Computes new sizes by absolute pixel. The dimensions are either enlarged
     * or reduced to fit into the specified sizes.
     *
     * This function maintains the aspect ratio: if Width > Height then the
     * function will maintain a proportional height value, and vice versa.
     *
     * @param \Common\Gd\CDimensions $iSizes
     * @return \Common\Gd\CDimensions
     * @throws \RuntimeException if something bad occured
     */
    public function resizeByAbsolute( \Common\Gd\CDimensions $iSizes )
    {
        // Sets width and calculates new height
        $newSizes = array( $iSizes->getWidth()
                         , $this->scale( $this->_iWidth
                                       , $iSizes->getWidth()
                                       , $this->_iHeight) );

        // Tests if computed sizes fit into the specified ones.
        if( ($newSizes[0]>$iSizes->getWidth())
                || ($newSizes[1]>$iSizes->getHeight()) )
        {
            // Do not fit, sets height and calculates new width
            $newSizes = array( $this->scale( $this->_iHeight
                                           , $iSizes->getHeight()
                                           , $this->_iWidth)
                             , $iSizes->getHeight() );
        }

        // Tests if computed sizes fit into the specified ones.
        //@codeCoverageIgnoreStart
        if( ($newSizes[0]>$iSizes->getWidth())
                || ($newSizes[1]>$iSizes->getHeight()) )
        {
            // Do not fit, raise an exception
            $sBuffer = 'Invalid resize operation.';
            $sBuffer .= " From: "   . $this->_iWidth      . "*" . $this->_iHeight;
            $sBuffer .= " To: "     . $iSizes->getWidth() . "*" . $iSizes->getHeight();
            $sBuffer .= " Values: " . $newSizes[0]        . "*" . $newSizes[1];
            throw new \RuntimeException( $sBuffer );
        }
        //@codeCoverageIgnoreEnd

        return new \Common\Gd\CDimensions( $newSizes[0], $newSizes[1] );
    }

    /**
     * Computes new sizes by relative percentage. Dimensions are either enlarged
     * or reduced to the specified size.
     *
     * This function maintains the aspect ratio.
     *
     * @param integer $value
     * @return \Common\Gd\CDimensions
     * @throws \InvalidArgumentException if the parameter is not valid.
     */
    public function resizeByPercentage( $value )
    {
        // Initialize
        $iPercentage = new \Common\Type\CInt( $value, 1 );
        $pImageSize = self::DEFAULT_VALUE;

        if( $iPercentage->isValid() )
        {
            $iWidth = $this->_iWidth * $iPercentage->getValue() / 100;
            $iHeight  = $this->_iHeight * $iPercentage->getValue() / 100;
            $pImageSize = new \Common\Gd\CDimensions( $iWidth, $iHeight );
        }
        else
        {
            unset( $iPercentage );
            throw new \InvalidArgumentException( 'Invalid arguments.' );
        }
        return $pImageSize;
    }

    /**
     * Computes new sizes by absolute pixel. The dimensions are either enlarged
     * or reduced to a closest match of the specified sizes.
     *
     * This function maintains the aspect ratio: if Width > Height then the
     * function will maintain a proportional height value, and vice versa.
     *
     * @param \Common\Gd\CDimensions $iSizes
     * @return \Common\Gd\CDimensions
     * @throws \RuntimeException if something bad occured
     */
    public function resizeClose( \Common\Gd\CDimensions $iSizes )
    {
        // Sets height and calculates new width
        $newSizes = array( $this->scale( $this->_iHeight
                                       , $iSizes->getHeight()
                                       , $this->_iWidth)
                            , $iSizes->getHeight() );

        // Tests if computed sizes are closed to the specified ones.
        if( ($newSizes[0]<$iSizes->getWidth())
                || ($newSizes[1]<$iSizes->getHeight()) )
        {
            // Do not fit, sets width and calculates new height
            $newSizes = array( $iSizes->getWidth()
                             , $this->scale( $this->_iWidth
                                           , $iSizes->getWidth()
                                           , $this->_iHeight) );
        }

        // Tests if computed sizes are closed to the specified ones.
        //@codeCoverageIgnoreStart
        if( ($newSizes[0]<$iSizes->getWidth())
                || ($newSizes[1]<$iSizes->getHeight()) )
        {
            // Do not fit, raise an exception
            $sBuffer = 'Invalid resize-close operation.';
            $sBuffer .= " From: "   . $this->_iWidth      . "*" . $this->_iHeight;
            $sBuffer .= " To: "     . $iSizes->getWidth() . "*" . $iSizes->getHeight();
            $sBuffer .= " Values: " . $newSizes[0]        . "*" . $newSizes[1];
            throw new \RuntimeException( $sBuffer );
        }
        //@codeCoverageIgnoreEnd

        return new \Common\Gd\CDimensions( $newSizes[0], $newSizes[1] );
    }

}
