<?php namespace Common\File;
/**
 * Interface class for log implementation.
 *
 * This file contains an interface class for log implementation.
 *
 * @package Common\File
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * Interface class for log implementation.
 */
interface CLogInterface
{
    /**
     * Write to file.
     * Returns the number of bytes written, or FALSE on error.
     *
     * @param \Common\Type\CString       $sMessage     Message
     * @param \Common\Type\CEnumPriority $enumPriority Priority
     * @param \Common\Type\CString       $sUser        [Optional] User name
     * @return integer|boolean
     */
    public function write( \Common\Type\CString $sMessage,
                           \Common\Type\CEnumPriority $enumPriority,
                           \Common\Type\CString $sUser = NULL );

}
