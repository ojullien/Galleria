<?php namespace Common\Type;
/**
 * String type class.
 *
 * This file contains a class which enforces strong typing of the string type
 *
 * @package Common\Type
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class enforces strong typing of the string type.
 */
final class CString extends \Common\Type\CTypeAbstract
{
    /**
     * Constructor
     *
     * @param string $value
     * @param \Common\Type\CString $sFilter Regex pattern
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __construct( $value, \Common\Type\CString $sFilter = NULL)
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, $value );
        }//if( defined(...
        //@codeCoverageIgnoreEnd
        $this->setValue( $value, $sFilter );
    }

    /**
     * Writes data to variable.
     *
     * @param string $value
     * @param \Common\Type\CString $sFilter Regex pattern.
     * @return mixed
     */
    public function setValue( $value, \Common\Type\CString $sFilter = NULL )
    {
        $this->_Value = self::DEFAULT_VALUE;
        if( $value instanceof \Common\Type\CTypeAbstract )
        {
            $value = $value->getValue();
        }

        if( is_scalar($value) )
        {
            // Should be scalar. Cast to string
            $value = trim($value);
            if( strlen($value)>0 )
            {
                // Should be not empty
                $this->_Value = $value;
                // Filter
                if( !is_null($sFilter) && !$this->matches( $sFilter ) )
                {
                    $this->_Value = self::DEFAULT_VALUE;
                }//if( !is_null(...
            }//if( strlen(...
        }//if( is_scalar(...
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
     * Searches variable for a match to the regular expression given in pattern.
     *
     * Returns true if the variable matches the pattern.
     * If $aMatches is provided,then it is filled with the results of search.
     * $aMatches[0] will contain the text that matched the full pattern,
     * $aMatches[1] will have the text that matched the first captured
     * parenthesized subpattern, and so on.
     *
     * @param \Common\Type\CString $sPattern The pattern to search for.
     * @param array $aMatches OPTIONAL. Results of search.
     * @return boolean
     */
    public function matches( \Common\Type\CString $sPattern, &$aMatches=NULL )
    {
        $bReturn = FALSE;
        if( isset($this->_Value) && $sPattern->isValid()
         && (is_null($aMatches) || is_array($aMatches)) )
        {
            if( preg_match( $sPattern->getValue(), $this->_Value, $aMatches )>0 )
            {
                $bReturn = TRUE;
            }//if( preg_match(...
        }//if...
        return $bReturn;
    }

    /**
     * Removes characters from the end of the variable.
     *
     * @param string $sCharlist Characters to remove
     */
    public function trimFromEnd( $sCharlist )
    {
        if( isset($this->_Value) && is_string($sCharlist) )
        {
            $this->_Value = rtrim( $this->_Value, $sCharlist );
        }//if( isset(...
    }

    /**
     * Returns TRUE if the $sNeedle was found. FALSE otherwise.
     *
     * @param \Common\Type\CString $sNeedle  The string to search in.
     * @return integer
     */
    public function contains( \Common\Type\CString $sNeedle )
    {
        $bReturn = FALSE;
        if( isset($this->_Value) && $sNeedle->isValid() )
        {
            if( strpos( $this->_Value, $sNeedle->getValue() )!==FALSE )
                $bReturn = TRUE;
        }//if(...
        return $bReturn;
    }

}
