<?php namespace Common\Filter;
/**
 * Filters
 *
 * This file contains a class which provides a set of commonly needed
 * filters.
 *
 * @package Common\Filter
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class provides a set of commonly needed filters.
 */
final class CFilters
{

    /** Public methods
     *****************/

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    private $_sDebugID = '';

    /**
     * Constructor
     * @codeCoverageIgnore
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __construct()
    {
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, '' );
        }//if( defined(...
    }

    /**
     * Destructor
     * @codeCoverageIgnore
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __destruct()
    {
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') && !defined('COMMON_DEBUG_OFF') )
            \Common\Debug\CDebug::getInstance()->addMemoryDelete($this->_sDebugID);
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

    /**
     * Gets a specific variable by name and filters it.
     *
     * Returns the value of the requested variable on success, FALSE if the
     * filter fails, or NULL if the variable is not set.
     *
     * @param array $aData Input data.
     * @param \Common\Type\CString $sVariableName Name of the variable.
     * @param \Common\Type\CInt $iFilter The ID of the filter to apply.
     * @param array $aOptions OPTIONAL. Associative array of options or bitwise disjunction of flags. If filter accepts options, flags can be provided in "flags" field of array.
     * @return mixed
     */
    public function filter( array $aData, \Common\Type\CString $sVariableName, \Common\Type\CInt $iFilter, array $aOptions = array() )
    {
        // Initialize
        $mixedReturn = FALSE;
        if( !$iFilter->isValid() ) $iFilter->setValue(FILTER_DEFAULT);
        if( $sVariableName->isValid() && $iFilter->isValid() )
        {
            // Build definition
            $aFilterFlagAndOptions = array( 'filter' => $iFilter->getValue() );
            if( count($aOptions)>0 )
            {
                $aFilterFlagAndOptions = array_merge ( $aFilterFlagAndOptions, $aOptions );
            }//if( count(...
            $aDefinition = array( $sVariableName->getValue() => $aFilterFlagAndOptions );

            // Filter
            $aFiltered = filter_var_array( $aData, $aDefinition );

            // Analyse
            if( is_array($aFiltered) )
            {
                // $aFiltered is an array containing the value of the requested
                // variable on success, or FALSE on failure. An array value will
                // be FALSE if the filter fails, or NULL if the variable is not set.
                $mixedReturn = $aFiltered[$sVariableName->getValue()];
            }//if( is_array(...

        }
        return $mixedReturn;
    }

    /**
     * Gets a specific variable by name and filters it as an email address.
     *
     * Returns the value of the requested variable on success, FALSE if the
     * filter fails, or NULL if the variable is not set.
     * On success, the parameter $pEmailAddress will contain the filtered email address.
     *
     * @param array $aData Input data.
     * @param \Common\Type\CString $sVariableName Name of the variable.
     * @param \Common\Type\CEmailAddress $pEmailAddress Instance of the email address object.
     * @return mixed
     */
    public function filterEmail( array $aData, \Common\Type\CString $sVariableName, \Common\Type\CEmailAddress $pEmailAddress)
    {
        // Initialize
        $mixedReturn = FALSE;
        $pEmailAddress->setValue(NULL);
        if( $sVariableName->isValid() )
        {
            // Get the value
            $iFilter = new \Common\Type\CInt( FILTER_UNSAFE_RAW );
            $mixedReturn = $this->filter( $aData, $sVariableName, $iFilter);
            unset($iFilter);

            // Validate the value
            $pEmailAddress->setValue($mixedReturn);
            if( !$pEmailAddress->isValid() )
            {
                // The value is not valid
                $mixedReturn = FALSE;
            }
            else
            {
                $mixedReturn = $pEmailAddress->getValue();
            }//if(...
        }//if( $sVariableName->isValid() )
        return $mixedReturn;
    }

    /**
     * Gets a specific variable by name and filters it as a session token.
     *
     * Returns TRUE if the specific variable $value matches with the token
     * registered in the PHP session. Returns FALSE if the filter fails, or NULL
     *  if the variable is not set.
     *
     * @param array $aData Input data.
     * @param \Common\Type\CString $sVariableName Name of the variable.
     * @param \Common\Session\CSessionAbstract $pSession Instance of the session object.
     * @return boolean
     */
    public function filterSessionToken( array $aData, \Common\Type\CString $sVariableName, \Common\Session\CSessionAbstract $pSession)
    {
        // Initialize
        $mixedReturn = FALSE;
        if( $sVariableName->isValid() )
        {
            // Get the value
            $iFilter = new \Common\Type\CInt( FILTER_SANITIZE_SPECIAL_CHARS );
            $mixedReturn = $this->filter( $aData, $sVariableName, $iFilter);
            unset($iFilter);

            // Validate the value
            $mixedReturn = $pSession->validateToken( $mixedReturn );

        }//if( $sVariableName->isValid() )
        return $mixedReturn;
    }

}
