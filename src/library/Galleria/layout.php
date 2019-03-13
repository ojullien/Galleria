<?php
/**
 *  This file contains usefull function html building.
 *
 * @package Galleria
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

define('EV_DIRTEMP', realpath( PROJECT_PATH . DIRECTORY_SEPARATOR . 'temp') );

/**
 * Build an image link
 *
 * @param \DirectoryIterator $pGallery
 * @param \Common\Type\CPath $pImage
 * @param type $iIndex
 * @return string
 */
function getLine( \DirectoryIterator $pGallery, \Common\Type\CPath $pImage, $iIndex )
{
    $sGalleryBasename = $pGallery->getBasename();
    $sImageBasename = $pImage->getBasename();
    $sTitle = 'Photo #' . $iIndex;
    $sSlide = 'data/gallerie/'. $sGalleryBasename . '/' . EV_GALSLID . '/' . $sImageBasename;
    $sThumb = 'data/gallerie/'. $sGalleryBasename . '/' . EV_GALTHUM . '/' . $sImageBasename;
    $s = '<li><a class="thumb" href="' . $sSlide . '" title="' . $sTitle . '">';
    $s .= '<img src="' . $sThumb . '" alt="' . $sTitle . '" width="' . EV_THUMBW . '" height="' . EV_THUMBH . '" />';
    $s .= '</a><div class="caption"><div class="download">';
    $s .= '<a href="hires.php?' . EV_TAGGALL . '=' . $sGalleryBasename . '&amp;' . EV_TAGIMAG . '=' . $sImageBasename .'">';
    $s .= 'T&#233;l&#233;charger la photo originale</a></div><div class="image-title">' . $sTitle . '</div>';
    $s .= '<div class="image-desc">&#160;</div></div></li>';
    return $s;
}

function build( \DirectoryIterator $pGallery, array $aLines )
{
    $iProgress=0;
    $iMax = count($aLines);
    $path = EV_DIRTEMP . DIRECTORY_SEPARATOR . $pGallery->getBasename() . '.html';
    $file = new \SplFileInfo($path);
    $open = $file->openFile('w+b');
    foreach( $aLines as $value )
    {
        $open->fwrite($value."\n");
        printCount( TRUE, ++$iProgress, $iMax);
    }
    $open->fflush();
    $open = NULL;
    unset($open,$file);
}
