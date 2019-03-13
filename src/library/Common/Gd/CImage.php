<?php namespace Common\Gd;
/**
 * Image processing.
 *
 * This file contains a class which implements methods for image processing.
 *
 * @package Common\Gd
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * class for image processing.
 */
final class CImage extends \Common\Gd\CGDAbstract
{
    /** Private variables
     ********************/

    /**
     * Image resource
     * @var \Common\Gd\CResource
     */
    private $_pResource = self::DEFAULT_VALUE;

    /** Class methods
     ****************/

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset( $this->_pResource );
        parent::__destruct();
    }

    /**
     * Constructor. The file should exists.
     *
     * @param mixed $resource \Common\Gd\CResource opened resource
     *                        \Common\Type\CPath   File to open
     * @throws \InvalidArgumentException if the resource is not valid.
     */
    public function __construct( $resource )
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, $resource );
        }//if( defined(...
        //@codeCoverageIgnoreEnd
        if( $resource instanceof \Common\Gd\CResource )
        {
            $this->_pResource  = $resource;
        }
        elseif( $resource instanceof \Common\Type\CPath )
        {
            $this->_pResource  = new \Common\Gd\CResource( $resource );
        }
        else
        {
            throw new \InvalidArgumentException('Invalid resource.');
        }
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->_pResource;
    }

    /** Save methods
     ***************/

    /**
     * Creates a image file from the given image.
     *
     * @param \Common\Type\CPath $sFilename The path to save the file to.
     * @param integer            $iQuality  Compression level, optional.
     *                                      JPEG: from 0 (worst quality, smaller file) to 100 (best quality, biggest file).
     *                                      PNG: compression level, from 0 (no compression) to 9.
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function save( \Common\Type\CPath $sFilename, $iQuality=NULL )
    {
        return $this->_pResource->save( $sFilename, $iQuality );
    }

    /**
     * Creates a image file from the given image to the specified format.
     *
     * @param \Common\Type\CPath $sFilename  The path to save the file to.
     * @param integer            $iImageType One of the IMAGETYPE_XXX constants.
     * @param integer            $iQuality   Compression level, optional.
     *                                       JPEG: from 0 (worst quality, smaller file) to 100 (best quality, biggest file).
     *                                       PNG: compression level, from 0 (no compression) to 9.
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function saveAs( \Common\Type\CPath $sFilename, $iImageType, $iQuality=NULL )
    {
        return $this->_pResource->saveAs($sFilename, $iImageType, $iQuality);
    }

    /** Resize methods
     *****************/

    /**
     * Changes the size of the image. This method will replace a rectangular area
     * from the current resource at position $sourcePosition and replace it in
     * a rectangular area of dimensions $destinationDimensions at position
     * $destinationPosition.
     *
     * @param \Common\Gd\CDimensions  $destinationDimensions Dimension of the destination image.
     * @param \Common\Gd\CCoordinates $destinationPosition   Destination start position
     * @param \Common\Gd\CDimensions  $sourceDimensions      Dimension of the source image.
     * @param \Common\Gd\CCoordinates $sourcePosition        Source start position
     * @param boolean                $bKeepTransparency      If TRUE then keep transparency in PNG format.
     * @throws \RuntimeException if something bad occured
     */
    private function resample( \Common\Gd\CDimensions  $destinationDimensions
                             , \Common\Gd\CCoordinates $destinationPosition
                             , \Common\Gd\CDimensions  $sourceDimensions
                             , \Common\Gd\CCoordinates $sourcePosition
                             , $bKeepTransparency=TRUE )
    {
        // Check parameters
        $bKeepTransparency = ($bKeepTransparency===FALSE)?FALSE:TRUE;

        // Create a new true color image
        $rNewResource = imagecreatetruecolor( $destinationDimensions->getWidth(), $destinationDimensions->getHeight() );
        if( !is_resource($rNewResource) )
        {
            //@codeCoverageIgnoreStart
            throw new \RuntimeException( 'Resource is not allocated.' );
            //@codeCoverageIgnoreEnd
        }

        // Preserve transparency
        if( $bKeepTransparency && ($this->_pResource->getType()==IMAGETYPE_PNG) )
        {
            $pStartPoint = new \Common\Gd\CCoordinates(0,0,0);
            $pMask = new \Common\Gd\CColor( array(255,255,255,0) );
            $this->addAlphaBlending( $rNewResource, $pMask, $pStartPoint );
            unset( $pMask, $pStartPoint );
        }

        // Resize
        if( !imagecopyresampled( $rNewResource
                               , $this->_pResource->getResource()
                               , $destinationPosition->getX()
                               , $destinationPosition->getY()
                               , $sourcePosition->getX()
                               , $sourcePosition->getY()
                               , $destinationDimensions->getWidth()
                               , $destinationDimensions->getHeight()
                               , $sourceDimensions->getWidth()
                               , $sourceDimensions->getHeight() ) )
        {
            //@codeCoverageIgnoreStart
            throw new \RuntimeException( 'Resize failure.' );
            //@codeCoverageIgnoreEnd
        }

        // On success, replace the original resource
        $this->_pResource->setResource($rNewResource);
    }

    /**
     * Changes the size of an image by absolute pixel. The image is either
     * enlarged or reduced to fit the specified sizes.
     *
     * If $bMaintainAspectRatio is TRUE then the image maintains the aspect
     * ratio: if Width > Height then the image will maintain a proportional
     * height value, and vice versa.
     *
     * @param \Common\Gd\CDimensions $newDimensions Resize image to.
     * @param boolean                $bMaintainAspectRatio if TRUE then keep aspect ratio.
     * @param boolean                $bKeepTransparency if TRUE then keep transparency in PNG format.
     * @throws \RuntimeException if something bad occured
     */
    public function resizeByAbsolute( \Common\Gd\CDimensions $newDimensions
                                    , $bMaintainAspectRatio=TRUE
                                    , $bKeepTransparency=TRUE )
    {
        // Check parameters
        $bMaintainAspectRatio = ($bMaintainAspectRatio===FALSE)?FALSE:TRUE;

        // Compute new dimensions
        if( $bMaintainAspectRatio )
        {
            $pCurrentDimensions = new \Common\Gd\CDimensions( $this->_pResource->getWidth(), $this->_pResource->getHeight() );
            $pDimensions = $pCurrentDimensions->resizeByAbsolute( $newDimensions );
            unset($pCurrentDimensions);
        }
        else
        {
            $pDimensions = $newDimensions;
        }

        // Resize
        if( $this->_pResource->getWidth()  != $pDimensions->getWidth()
         || $this->_pResource->getHeight() != $pDimensions->getHeight() )
        {
            $pDestinationPosition = new \Common\Gd\CCoordinates(0,0,0);
            $pSourcePosition = new \Common\Gd\CCoordinates(0,0,0);
            $pSourceDimensions = new \Common\Gd\CDimensions( $this->_pResource->getWidth(), $this->_pResource->getHeight() );
            $this->resample($pDimensions, $pDestinationPosition, $pSourceDimensions, $pSourcePosition, $bKeepTransparency);
            unset( $pSourceDimensions, $pSourcePosition, $pDestinationPosition );
        }
    }

    /**
     * Changes the size of an image by relative percentage. The image is either
     * enlarged or reduced to fit the specified sizes.
     *
     * @param integer $value
     * @param boolean $bKeepTransparency if TRUE then keep transparency in PNG format.
     * @throws \RuntimeException if something bad occured
     */
    public function resizeByPercentage( $value, $bKeepTransparency=TRUE )
    {
        // Compute new dimensions
        $pCurrentDimensions = new \Common\Gd\CDimensions( $this->_pResource->getWidth(), $this->_pResource->getHeight() );
        $newDimensions = $pCurrentDimensions->resizeByPercentage( $value );
        unset($pCurrentDimensions);

        // Resize
        $pDestinationPosition = new \Common\Gd\CCoordinates(0,0,0);
        $pSourcePosition = new \Common\Gd\CCoordinates(0,0,0);
        $pSourceDimensions = new \Common\Gd\CDimensions( $this->_pResource->getWidth(), $this->_pResource->getHeight() );
        $this->resample($newDimensions, $pDestinationPosition, $pSourceDimensions, $pSourcePosition, $bKeepTransparency);
        unset( $pSourceDimensions, $pSourcePosition, $pDestinationPosition, $newDimensions );
    }

    /**
     * Shrink a portion of the image without affecting the size of the image.
     *
     * @param \Common\Gd\CCoordinates $position          Starting point of the region to crop
     * @param \Common\Gd\CDimensions  $dimensions        Size of the region
     * @param boolean                 $bKeepTransparency if TRUE then keep transparency in PNG format.
     * @throws \InvalidArgumentException if something bad occured
     */
    public function crop( \Common\Gd\CCoordinates $position, \Common\Gd\CDimensions $dimensions, $bKeepTransparency=TRUE )
    {
        // Position shall be int the image
        if( ($position->getX() > $this->_pResource->getWidth())
         || ($position->getY() > $this->_pResource->getHeight()) )
        {
            throw new \InvalidArgumentException('Invalid region.');
        }

        // Cannot crop outside the image
        $width  = ( ($dimensions->getWidth()  + $position->getX()) > $this->_pResource->getWidth() )  ? $this->_pResource->getWidth()  : $dimensions->getWidth();
        $height = ( ($dimensions->getHeight() + $position->getY()) > $this->_pResource->getHeight() ) ? $this->_pResource->getHeight() : $dimensions->getHeight();
        $pDestinationDimensions = new \Common\Gd\CDimensions( $width, $height );
        $pDestinationPosition = new \Common\Gd\CCoordinates(0,0,0);

        // Resample
        $this->resample($pDestinationDimensions, $pDestinationPosition, $pDestinationDimensions, $position, $bKeepTransparency);
        unset( $pDestinationPosition, $pDestinationDimensions );
    }

    /**
     * Shrink a portion of the image from the center and without affecting the
     * size of the image.
     *
     * @param \Common\Gd\CDimensions  $dimensions        Size of the region
     * @param boolean                 $bKeepTransparency if TRUE then keep transparency in PNG format.
     * @throws \InvalidArgumentException if something bad occured
     */
    public function cropFromCenter( \Common\Gd\CDimensions $dimensions, $bKeepTransparency=TRUE )
    {
        // Cannot crop outside the image
        $width  = ( $dimensions->getWidth()  > $this->_pResource->getWidth() )  ? $this->_pResource->getWidth()  : $dimensions->getWidth();
        $height = ( $dimensions->getHeight() > $this->_pResource->getHeight() ) ? $this->_pResource->getHeight() : $dimensions->getHeight();
        $pDimensions = new \Common\Gd\CDimensions( $width, $height );
        $pPosition = new \Common\Gd\CCoordinates( ($this->_pResource->getWidth()-$width)/2, ($this->_pResource->getHeight()-$height)/2, 0);

        // Resample
        $this->crop( $pPosition, $pDimensions, $bKeepTransparency );
        unset( $pPosition, $pDimensions );
    }

    /**
     * Resizes the image down to the closest match and then crops from the
     * centre the desired dimensions.
     *
     * @param \Common\Gd\CDimensions $pFrame            Dimension of the frame the image shall fit to.
     * @param boolean                $bKeepTransparency if TRUE then keep transparency in PNG format.
     * @throws \RuntimeException if something bad occured
     */
    public function resizeAdaptive( \Common\Gd\CDimensions $pFrame
                                    , $bKeepTransparency=TRUE )
    {
        // Compute new dimensions
        $pCurrentDimensions = new \Common\Gd\CDimensions( $this->_pResource->getWidth(), $this->_pResource->getHeight() );
        $pNewDimensions = $pCurrentDimensions->resizeClose( $pFrame );
        unset($pCurrentDimensions);

        // Resize
        if( $this->_pResource->getWidth()  != $pNewDimensions->getWidth()
         || $this->_pResource->getHeight() != $pNewDimensions->getHeight() )
        {
            $pDestinationPosition = new \Common\Gd\CCoordinates(0,0,0);
            $pSourcePosition = new \Common\Gd\CCoordinates(0,0,0);
            $pSourceDimensions = new \Common\Gd\CDimensions( $this->_pResource->getWidth(), $this->_pResource->getHeight() );
            $this->resample($pNewDimensions, $pDestinationPosition, $pSourceDimensions, $pSourcePosition, $bKeepTransparency);
            unset( $pSourceDimensions, $pSourcePosition, $pDestinationPosition );
        }
        unset($pNewDimensions);

        // Crop from center
        if( $this->_pResource->getWidth()  != $pFrame->getWidth()
         || $this->_pResource->getHeight() != $pFrame->getHeight() )
        {
            $this->cropFromCenter( $pFrame, $bKeepTransparency);
        }
    }

}
