<?php
/**
 *  This file contains usefull functions for directory management.
 *
 * @package Galleria
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

require_once('Common/File/CDirectory.php');

/**
 * Checks if a directory is readable and writable. Creates the directory if not
 * exists.
 *
 * @param \Common\Type\CPath $pPath Path of the directory
 * @return boolean FALSE on error.
 */
function verifyDirectory( \Common\Type\CPath $pPath )
{
    // Initialize
    $bReturn = FALSE;
    // Verify
    if( $pPath->isValid() )
    {
        $pDirectory = new \Common\File\CDirectory( $pPath );
        if( !$pDirectory->exists() )
        {
            $bReturn = $pDirectory->createDirectory();
        }
        $bReturn = $pDirectory->isDir() && $pDirectory->isWritable();
        unset($pDirectory);
    }
    return $bReturn;
}

/**
 * Checks if the sub directories of the gallery are readable and writable.
 * Creates the directories if do not exist.
 *
 * @param \DirectoryIterator $pGallery Gallery
 * @return boolean FALSE on error.
 */
function verifyDirectories( \DirectoryIterator $pGallery )
{
    // Initialize
    $bReturn = FALSE;
    // Verify thumb directory
    $pPath = new \Common\Type\CPath( $pGallery->getRealPath() . DIRECTORY_SEPARATOR . EV_GALTHUM  );
    $bReturn = verifyDirectory( $pPath );
    // Verify slides directory
    $pPath->setValue( $pGallery->getRealPath() . DIRECTORY_SEPARATOR . EV_GALSLID );
    $bReturn = $bReturn && verifyDirectory( $pPath );
    // Verify hi-res directory
    $pPath->setValue( $pGallery->getRealPath() . DIRECTORY_SEPARATOR . EV_GALHRES );
    $bReturn = $bReturn && verifyDirectory( $pPath );
    unset($pPath);
    return $bReturn;
}
