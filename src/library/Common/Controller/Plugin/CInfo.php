<?php namespace Common\Controller\Plugin;
/**
 * Request and Response info plugin class.
 *
 * This file contains a plugin class which dump the current http request and
 * the current http response.
 *
 * @package Common\Controller\Plugin
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class dump the current http request and the current http response.
 */
class CInfo extends \Zend_Controller_Plugin_Abstract
{
    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    private $_sDebugID = '';

    /**
     * Destructor
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __destruct()
    {
        unset($this->_Value);
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') && !defined('COMMON_DEBUG_OFF') )
            \Common\Debug\CDebug::getInstance()->addMemoryDelete($this->_sDebugID);
    }

    /**
     * Constructor
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
     * Writing data to inaccessible properties is not allowed.
     *
     * @param string $name
     * @param mixed $value
     * @codeCoverageIgnore
     */
    public function __set($name, $value){}

    /**
     * Dump the current http request and the current http response in human readable format.
     * Called before the Controller exits its dispatch loop.
     */
    public function dispatchLoopShutdown()
    {
        // Initialize
        $sOutput = '<div><small>';

        // Request
        $pRequest = $this->getRequest();
        if( isset($pRequest) )
        {
            $sOutput .= '<p>Params: ' . PHP_EOL;
            $sOutput .= '<pre>' . htmlentities( print_r( $pRequest->getParams(), TRUE), ENT_QUOTES, 'UTF-8') . '</pre></p>' . PHP_EOL;
        }
        $sOutput .= '</small></div></body>';

        // Response
        $pResponse = $this->getResponse();

        // Display
        $pResponse->setBody( str_ireplace('</body>', $sOutput, $pResponse->getBody()) );
    }
}
