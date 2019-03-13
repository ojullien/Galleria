<?php
/**
 *  This file contains .
 *
 * @package Galleria
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

require_once( 'Common/Type/CFloat.php');
require_once( 'Common/Gd/CColor.php');
require_once( 'Common/Gd/CDimensions.php');
require_once( 'Common/Gd/CCoordinates.php');
require_once( 'Common/Gd/CResource.php');
require_once( 'Common/Gd/CGDAbstract.php');
require_once( 'Common/Gd/CImage.php');

/**
 * Create a slide image.
 *
 * @param \Common\Gd\CImage $pImage
 * @param \Common\Gd\CDimensions $pDimensions
 * @param \Common\Type\CPath $pSave
 * @return boolean
 */
function createSlide( \Common\Gd\CImage $pImage, \Common\Gd\CDimensions $pDimensions, \Common\Type\CPath $pSave )
{
    $bReturn = FALSE;
    if( $pSave->isValid() )
    {
        if( !file_exists( $pSave->getValue() ) )
        {
            // Resize
            $pImage->resizeByAbsolute( $pDimensions);

            // Save
            $bReturn = $pImage->save($pSave);
        }
        else
        {
            $bReturn = TRUE;
        }
    }
    return $bReturn;
}

/**
 * Create a thumb image.
 *
 * @param \Common\Gd\CImage $pImage
 * @param \Common\Gd\CDimensions $pDimensions
 * @param \Common\Type\CPath $pSave
 * @return boolean
 */
function createThumb(\Common\Gd\CImage $pImage, \Common\Gd\CDimensions $pDimensions, \Common\Type\CPath $pSave )
{
    $bReturn = FALSE;
    if( $pSave->isValid() )
    {
        if( !file_exists( $pSave->getValue() ) )
        {
            // Resize
            $pImage->resizeAdaptive( $pDimensions);

            // Save
            $bReturn = $pImage->save($pSave);
        }
        else
        {
            $bReturn = TRUE;
        }
    }
    return $bReturn;
}
