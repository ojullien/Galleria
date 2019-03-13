<?php namespace Common\Gd;
/**
 * Parent class for image and graph processing.
 *
 * This file contains a class which implements main methods for image and graph
 * processing.
 *
 * @package Common\Gd
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * Parent class for image and graph processing.
 */
abstract class CGDAbstract
{
    /** Constants */
    const DEFAULT_VALUE = NULL;

    /** Protected variables
     **********************/

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    protected $_sDebugID = '';

    /** Class methods
     ****************/

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

    /** Usefull images drawing methods
     *********************************/

    /**
     * Adds an alpha layer on a TRUE COLOR image (PNG format).
     *
     * @param type $rResource
     * @param \Common\Gd\CColor $pColor Colors mask and transparency
     * @param \Common\Gd\CImageSize $pStartPoint Coordinates of start point
     * @return boolean FALSE on error.
     */
    final protected function addAlphaBlending( $rResource, \Common\Gd\CColor $pColor, \Common\Gd\CCoordinates $pStartPoint  )
    {
        $bReturn = FALSE;
        if( is_resource($rResource) )
        {
            // Set the blending mode
            $bReturn = imagealphablending( $rResource, FALSE );

            // Allocate alpha color
            if( $bReturn )
            {
                $aColor = $pColor->getValue();
                $iColor = imagecolorallocatealpha( $rResource
                                                 , $aColor[0]
                                                 , $aColor[1]
                                                 , $aColor[2]
                                                 , $aColor[3] );
                $bReturn = ($iColor===FALSE)?FALSE:TRUE;
            }

            // Flood the color
            $bReturn = $bReturn && imagefill( $rResource
                                            , $pStartPoint->getX()
                                            , $pStartPoint->getY()
                                            , $iColor );

            // Save alpha channel
            $bReturn = $bReturn && imagesavealpha( $rResource, TRUE );

        }//if( is_resource(...
        return $bReturn;
    }

    /** Abstract methods
     *******************/

    /**
     * Constructor.
     *
     * @param \Common\Type\CPath $filename The file to open/create.
     * @throws \RuntimeException if the filename cannot be opened/created.
     */
    abstract public function __construct( \Common\Type\CPath $filename );

}
