<?php
/**
 *  This file contains usefull function for the application.
 *
 * @package Galleria
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * Output a message.
 *
 * @param string  $value
 * @param boolean $bEOL if FALSE do not print end of line
 */
function display( $value, $bEOL=TRUE )
{
    if( !defined('STDIN') )
    {
        // Not Running from CLI
        if( $bEOL )
        {
            $value .= '</br>';
        }
    }
    if( $bEOL )
    {
        $value .= PHP_EOL;
    }
    echo $value;
}

/**
 * Print current progression.
 *
 * @param boolean $bSuccess If FALSE, print a failure
 * @param integer $iIndex   Current index
 * @param integer $iMax     Max count
 * @return boolean
 */
function printCount( $bSuccess, $iIndex, $iMax )
{
    if( $bSuccess===FALSE )
    {
        display( ' (F)', TRUE );
    }
    else
    {
        $iIndex = ($iIndex<0) ? 1: $iIndex;
        $iMax = ($iMax<1) ? 100: $iMax;
        if( ($iIndex % 30)==0 )
        {
            // End of line
            display( ' . (' . floor(100*$iIndex/$iMax) . '%)', TRUE );
        }
        else
        {
            // Add
            display( ' .', FALSE );
        }
        // End of count
        if( $iIndex>=$iMax )
        {
            display( ' (100%)', TRUE );
        }
    }
}
