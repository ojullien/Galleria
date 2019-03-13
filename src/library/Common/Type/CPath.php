<?php namespace Common\Type;
/**
 * Path type class.
 *
 * This file contains a class which enforces strong typing of the path type
 *
 * @package Common\Type
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class enforces strong typing of the path type.
 */
final class CPath extends \Common\Type\CTypeAbstract
{
    /**
     * Constructor
     *
     * @param string $value
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __construct( $value )
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, $value );
        }//if( defined(...
        //@codeCoverageIgnoreEnd
        $this->setValue( $value );
    }

    /**
     * Writes data to variable.
     *
     * @param string $value
     * @return mixed
     */
    public function setValue( $value )
    {
        // Initialize
        $this->_Value = self::DEFAULT_VALUE;

        // Check argument type
        if( $value instanceof \Common\Type\CString )
        {
            $sInput = (string)$value;
        }
        elseif( is_string($value) )
        {
            $sInput = trim($value);
        }
        else
        {
            $sInput = '';
        }

        // Check argument value
        if( strlen($sInput)>0 )
        {
            // Split
            $sInput = rtrim( $sInput, '/\\');
            $aSplit = mb_split( '[:]', $sInput);
            if( strlen($aSplit[0])>0 )
            {
                // Only one ':' is allowed
                $bIsValid = FALSE;
                $iCount = count($aSplit);
                if( $iCount==1 )
                {
                    $bIsValid = TRUE;
                }
                elseif( $iCount==2 )
                {
                    // Only one ':' should be located in the first part of the path
                    if( (strpos($aSplit[0], '/')===FALSE) && (strpos($aSplit[0], '\\')===FALSE) )
                    {
                        $bIsValid = TRUE;
                    }//if( (strpos(...
                }//if( $iCount==...
                // Check the path
                if( $bIsValid && (preg_match( "/^[\\p{Nd}\\p{L}\\\\\\/\\._\\-\\:]+$/u", $sInput )>0) )
                {
                    $sRealPath = realpath($sInput);
                    if( $sRealPath!==FALSE )
                    {
                        $this->_Value = $sRealPath;
                    }
                    else
                    {
                        $this->_Value = $sInput;
                    }
                }//if( $bIsValid )
            }//if( strlen(...
        }//if( strlen(...
        return $value;
    }

    /**
     * Reads data from variable.
     *
     * @return string|NULL
     */
    public function getValue()
    {
        $sReturn = self::DEFAULT_VALUE;
        if( isset($this->_Value) )
        {
            $sReturn = $this->_Value;
        }//if(...
        return $sReturn;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        $sReturn = '';
        if( isset($this->_Value) )
        {
            $sReturn = (string)$this->_Value;
        }//if(...
        return $sReturn;
    }

    /**
     * Returns the base name of the file, directory, or link without path info.
     *
     * @return string|NULL
     */
    public function getBasename()
    {
        $sReturn = self::DEFAULT_VALUE;
        if( isset($this->_Value) )
        {
            $info = new \SplFileInfo($this->_Value);
            $sReturn = $info->getBasename();
            unset($info);
        }//if(...
        return $sReturn;
    }

    /**
     * Gets absolute path to file. This method expands all symbolic links,
     * resolves relative references and returns the real path to the file.
     *
     * @return string|NULL
     */
    public function getRealPath()
    {
        $sReturn = self::DEFAULT_VALUE;
        if( isset($this->_Value) )
        {
            $info = new \SplFileInfo($this->_Value);
            $sReturn = $info->getRealPath();
            unset($info);
        }//if(...
        return $sReturn;
    }

}
