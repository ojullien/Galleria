<?php namespace Common\Controller;
/**
 * Controllers
 *
 * This file contains a class which extends Zend_Controller_Action and implements
 * usefull methods for controllers.
 *
 * @package Common\Controller
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class extends Zend_Controller_Action and implements usefull methods for
 * controllers.
 *  - Variables checks
 *  - Plugins
 *  - Session
 */
abstract class CControllerAbstract extends \Zend_Controller_Action
{
    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    protected $_sDebugID = '';

    /**
     * Application environment
     * @var string
     */
    protected $_sEnvironment = 'production';

    /**
     * Log Resource
     * @var \Zend_Log
     */
    protected $_pLog = NULL;

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset( $this->_pSession, $this->_pLog );
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') && !defined('COMMON_DEBUG_OFF') )
            \Common\Debug\CDebug::getInstance()->addMemoryDelete($this->_sDebugID);
        //@codeCoverageIgnoreEnd
    }

    /**
     * Initialize object
     */
    public function init()
    {
        // Usual check
        if( !isset($this->view) )
        {
            die('View is not initialized');
        }//if( !isset(...

        // Retrieve application environment
        $pBootstrap = $this->getInvokeArg('bootstrap');
        $this->_sEnvironment = $pBootstrap->getEnvironment();

        // Retrieve log resource
        if( $pBootstrap->hasResource('Log'))
        {
            $this->_pLog = $pBootstrap->getResource('Log');
        }
    }

    /*************************************************************************
     * Plugin methods
     *************************************************************************/

    /**
     * Register plugins
     */
    protected function registerPlugins()
    {
        // Register Info plugin
        $pFrontController = \Zend_Controller_Front::getInstance();
        $pInfo = new \Common\Controller\Plugin\CInfo();
        $pFrontController->registerPlugin($pInfo);
    }

    /*************************************************************************
     * Session methods
     *************************************************************************/

    /**
     * Session
     * @var \Common\Session\CSessionZend
     */
    protected $_pSession = NULL;

    /**
     * Initialize session
     * @throws \RuntimeException If the session was not successfully started.
     */
    protected function initSession()
    {
        $this->_pSession = new \Common\Session\CSessionZend();
        $this->_pSession->start( \Common\Session\CSessionZend::CSRF_NAMESPACE );
    }

    /*************************************************************************
     * Layout methods
     *************************************************************************/

    /**
     * Disable layout and view.
     */
    protected function disableLayoutAndView()
    {
        if( isset($this->_helper) )
        {
            if( isset($this->_helper->layout) )
            {
                $this->_helper->layout->disableLayout();
            }
            if( isset($this->_helper->viewRenderer) )
            {
                $this->_helper->viewRenderer->setNoRender(true);
            }
        }
    }

}
