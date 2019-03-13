<?php namespace Common\Gd;
/**
 * Image resource.
 *
 * This file contains a class which enforces strong typing of the image resource
 * type.
 *
 * @package Common\Gd
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class enforces strong typing of the image resource type.
 */
final class CResource
{
    /** Constants */
    const DEFAULT_VALUE = NULL;

    /** Private variables
     ********************/

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    private $_sDebugID = '';

    /**
     * Resource identifier
     * @var resource
     */
    private $_rResource = self::DEFAULT_VALUE;

    /**
     * IMAGETYPE_XXX constants indicating the type of the image
     * @var integer
     */
    private $_iType = FALSE;

    /**
     * MIME type of the image. This information can be used to deliver
     * images with the correct HTTP Content-type header.
     * @var string
     */
    private $_sMime = FALSE;

    /**
     * Alpha channel: will be 3 for RGB pictures and 4 for CMYK pictures.
     * @var integer
     */
    private $_iChannels = FALSE;

    /**
     * Number of bits for each color
     * @var integer
     */
    private $_iBits = FALSE;

    /** Class methods
     ****************/

    /**
     * Destructor
     */
    public function __destruct()
    {
        if( is_resource( $this->_rResource ) )
        {
            imagedestroy( $this->_rResource );
            $this->_rResource = self::DEFAULT_VALUE;
        }//if( is_resource(...
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') && !defined('COMMON_DEBUG_OFF') )
            \Common\Debug\CDebug::getInstance()->addMemoryDelete($this->_sDebugID);
        //@codeCoverageIgnoreEnd
    }

    /**
     * Constructor.
     *
     * @param mixed $resource Resource From an image resource, returned by one of the image creation functions, such as imagecreatetruecolor()
     *                                 or from a \Common\Gd\CResource object
     *                                 or filename (\Common\Type\CPath)
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
        $this->setResource($resource);
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return serialize( array( 'width'=>imagesx($this->_rResource),
                                'height'=>imagesy($this->_rResource),
                                  'type'=>$this->_iType,
                                   'tag'=>'width="' . imagesx($this->_rResource) . '" height="' . imagesy($this->_rResource) . '"',
                                  'mime'=>$this->_sMime,
                              'channels'=>$this->_iChannels,
                                  'bits'=>$this->_iBits));
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

    /** open resource methods
     ************************/

    /**
     * Sets the resource from a file.
     *
     * @param \Common\Type\CPath $filename
     */
    private function open( \Common\Type\CPath $filename )
    {
        if( $filename->isValid() )
        {
            // Case: image file
            $aImageInfo = getimagesize( $filename->getValue() );
            if( is_array($aImageInfo) )
            {
                switch( $aImageInfo[2] )
                {
                    case IMAGETYPE_JPEG:
                        $this->_rResource = imagecreatefromjpeg( $filename->getValue() );
                        break;
                    case IMAGETYPE_PNG:
                        $this->_rResource = imagecreatefrompng( $filename->getValue() );
                        break;
                    default:
                        $this->_rResource = self::DEFAULT_VALUE;
                        break;
                }//switch(...
            }//if( is_array(...
            $this->_iType     = (isset($aImageInfo[2]))?$aImageInfo[2]:FALSE;
            $this->_sMime     = (isset($aImageInfo['mime']))?$aImageInfo['mime']:FALSE;
            $this->_iChannels = (isset($aImageInfo['channels']))?$aImageInfo['channels']:FALSE;
            $this->_iBits     = (isset($aImageInfo['bits']))?$aImageInfo['bits']:FALSE;
        }
    }

    /**
     * Sets the resource from a \Common\Gd\CResource object.
     *
     * @param  \Common\Gd\CResource $resource
     */
    private function reference( \Common\Gd\CResource $resource )
    {
        $this->_rResource = $resource->getResource();
        $this->_iType     = $resource->getType();
        $this->_sMime     = $resource->getMime();
        $this->_iChannels = $resource->getChannels();
        $this->_iBits     = $resource->getBits();
    }

    /**
     * Sets the resource from an image resource, returned by one of the image
     * creation functions, such as imagecreatetruecolor().
     *
     * @param resource $resource
     */
    private function replace( $resource )
    {
        if( is_resource($resource) && ( strcasecmp(get_resource_type($resource),'gd')==0) )
        {
            $this->_rResource = $resource;
            $this->_iChannels = FALSE;
            $this->_iBits     = FALSE;
        }
    }

    /** Getters
     **********/

    /**
     * Set the resource
     * @param mixed $resource
     * @throws \InvalidArgumentException if the resource is not valid.
     */
    public function setResource( $resource )
    {
        // Destroy old resource
        if( is_resource( $this->_rResource ) )
        {
            imagedestroy( $this->_rResource );
            $this->_rResource = self::DEFAULT_VALUE;
        }

        // Check parameter
        if( $resource instanceof \Common\Type\CPath )
        {
            $this->open($resource);
        }
        elseif( $resource instanceof \Common\Gd\CResource )
        {
            $this->reference($resource);
        }
        else
        {
            $this->replace($resource);
        }

        // Check error
        if( !is_resource($this->_rResource) )
        {
            throw new \InvalidArgumentException('Invalid resource.');
        }
    }

    /**
     * Get the resource
     * @return resource or NULL if no relevant
     */
    public function getResource(){return $this->_rResource;}

    /**
     * Get the image width
     * @return integer or FALSE if no relevant
     */
    public function getWidth()
    {
        $iReturn = FALSE;
        if( is_resource( $this->_rResource ) )
        {
            $iReturn = imagesx($this->_rResource);
        }
        return $iReturn;
    }

    /**
     * Get the image height
     * @return integer or FALSE if no relevant
     */
    public function getHeight()
    {
        $iReturn = FALSE;
        if( is_resource( $this->_rResource ) )
        {
            $iReturn = imagesy($this->_rResource);
        }
        return $iReturn;
    }

    /**
     * Get the IMAGETYPE_XXX constants indicating the type of the image.
     * @return integer or FALSE if no relevant
     */
    public function getType(){return $this->_iType;}

    /**
     * Get text string with the correct height="yyy" width="xxx" string that can
     * be used directly in an IMG tag.
     * @return string or FALSE if no relevant
     */
    public function getTag(){return 'width="' . imagesx($this->_rResource)
                                  . '" height="' . imagesy($this->_rResource) . '"';}
    /**
     * Get the MIME type of the image. This information can be used to deliver
     * images with the correct HTTP Content-type header.
     * @return string or FALSE if no relevant
     */
    public function getMime(){return $this->_sMime;}

    /**
     * Get the alpha channel: will be 3 for RGB pictures and 4 for CMYK pictures.
     * @return integer or FALSE if no relevant
     */
    public function getChannels(){return $this->_iChannels;}

    /**
     * Get the number of bits for each color
     * @return integer or FALSE if no relevant
     */
    public function getBits(){return $this->_iBits;}

    /** Save methods
     ***************/

    /**
     * Creates a JPEG file from the given image.
     *
     * @param \Common\Type\CPath $sFilename The path to save the file to.
     * @param integer            $iQuality  Compression level, optional. From 0 (worst quality, smaller file) to 100 (best quality, biggest file).
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    private function saveJPEG( \Common\Type\CPath $sFilename, $iQuality = NULL )
    {
        $bReturn = FALSE;
        // Quality
        $pQuality = new \Common\Type\CInt( $iQuality, 0, 100 );
        if( !$pQuality->isValid() )
        {
            // Parameters is not valid, set the default
            $pQuality->setValue(75);
        }
        // Create the file
        if( $sFilename->isValid()  && is_resource($this->_rResource) )
        {
            $bReturn = imagejpeg( $this->_rResource, $sFilename->getValue(), $pQuality->getValue() );
        }
        unset($pQuality);
        return $bReturn;
    }

    /**
     * Creates a PNG file from the given image.
     *
     * @param \Common\Type\CPath $sFilename The path to save the file to.
     * @param integer            $iQuality  Compression level, optional .From 0 (no compression) to 9.
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    private function savePNG( \Common\Type\CPath $sFilename, $iQuality = NULL )
    {
        $bReturn = FALSE;
        // Quality
        $pQuality = new \Common\Type\CInt( $iQuality, 0, 9 );
        if( !$pQuality->isValid() )
        {
            // Parameters is not valid, set the default
            $pQuality->setValue(9);
        }
        // Create the file
        if( $sFilename->isValid() && is_resource($this->_rResource) )
        {
            $bReturn = imagepng( $this->_rResource, $sFilename->getValue(), $pQuality->getValue() );
        }
        unset($pQuality);
        return $bReturn;
    }

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
        if( $this->_iType===IMAGETYPE_JPEG )
        {
            $bReturn = $this->saveJPEG( $sFilename, $iQuality );
        }
        else
        {
            //case IMAGETYPE_PNG:
            $bReturn = $this->savePNG( $sFilename, $iQuality );
        }
        return $bReturn;
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
        switch( $iImageType )
        {
            case IMAGETYPE_JPEG:
                $bReturn = $this->saveJPEG( $sFilename, $iQuality );
                break;
            case IMAGETYPE_PNG:
                $bReturn = $this->savePNG( $sFilename, $iQuality );
                break;
            default:
                $bReturn = FALSE;
                break;
        }
        return $bReturn;
    }

}
