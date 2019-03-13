<?php namespace Common\Form;
/**
 * Forms
 *
 * This file contains a class which implements usefull methods for forms.
 *
 * @package Common\Form
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class implements usefull methods for forms.
 */
abstract class CFormAbstract
{
    /** Constants */
    const DEFAULT_VALUE = NULL;

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    protected $_sDebugID = '';

    /**
     * Constructor
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __construct()
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, '' );
        }
        //@codeCoverageIgnoreEnd
        $this->init();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->_errorMessages = array();
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') && !defined('COMMON_DEBUG_OFF') )
            \Common\Debug\CDebug::getInstance()->addMemoryDelete($this->_sDebugID);
        //@codeCoverageIgnoreEnd
    }

    /**
     * Writing data to inaccessible properties is not allowed.
     *
     * @param string $name
     * @param mixed $value
     * @codeCoverageIgnore
     */
    final public function __set($name, $value)
    {
        throw new \BadMethodCallException( 'Writing data to inaccessible properties is not allowed.' );
    }

    /**
     * Initialize form.
     */
    abstract protected function init();

    /*************************************************************************
     * Error section
     *************************************************************************/

    /**
     * Custom form-level error messages
     * @var array
     */
    protected $_errorMessages = array();

    /**
     * Returns TRUE if the datas are not valid.
     *
     * @return boolean
     */
    final public function isErrors()
    {
        return ((count($this->_errorMessages)>0)?TRUE:FALSE);
    }

    /**
     * Add a custom error message to return in the event of failed validation
     *
     * @param string $sMessage
     */
    final protected function addErrorMessage($sMessage)
    {
        $sMessage = new \Common\Type\CString($sMessage);
        if( $sMessage->isValid() )
        {
            $this->_errorMessages[] = $sMessage;
        }//if( is_scalar(...
    }

    /**
     * Retrieve custom error messages
     *
     * @return array
     */
    final public function getErrorMessages()
    {
        return $this->_errorMessages;
    }

    /**
     * Clear custom error messages stack
     */
    final public function clearErrorMessages()
    {
        $iCount = count($this->_errorMessages);
        for($iIndex = 0; $iIndex < $iCount; $iIndex++)
        {
            unset($this->_errorMessages[$iIndex]);
        }//for
        $this->_errorMessages = array();
    }

    /*************************************************************************
     * Validation methods
     *************************************************************************/

    /**
     * Validation state. TRUE if the validation process is started
     * @var boolean
     */
    protected $_isValidated = FALSE;

    /**
     * Returns TRUE if the form is validated.
     *
     * @return boolean
     */
    final public function isValidated()
    {
        return $this->_isValidated;
    }

}
