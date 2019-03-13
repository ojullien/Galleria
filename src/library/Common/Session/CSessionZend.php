<?php namespace Common\Session;
/**
 * Zend Session class.
 *
 * This file contains a class which implements the Zend session management module.
 *
 * @package Common\Session
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class implements the Zend session management module.
 */
final class CSessionZend extends \Common\Session\CSessionAbstract
{
    /** Private attributs
     ********************/

    /**
     * Zend Session namespace for csrf
     * @var \Zend_Session_Namespace
     */
    private $_pNamespaceCSRF = NULL;

    /** Public methods
     *****************/

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid( rand() );
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, '' );
        }//if( defined(...
        //@codeCoverageIgnoreEnd
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset( $this->_pNamespaceCSRF );
        parent::__destruct();
    }

    /**
     * Initializes and starts session for CSRF namespace.
     * @throws \RuntimeException If the session was not successfully started.
     */
    private function startNamespaceCSRF()
    {
        if( !isset($this->_pNamespaceCSRF) )
        {
            try
            {
                $this->_pNamespaceCSRF = new \Zend_Session_Namespace( self::CSRF_NAMESPACE );
            }
            catch( \Exception $e)
            {
                // @codeCoverageIgnoreStart
                throw new \RuntimeException( $e->getMessage(), $e->getCode() );
                // @codeCoverageIgnoreEnd
            }//try...
        }//if( !isset(...
    }

    /**
     * Initializes and starts session.
     *
     * @param string $namespace - programmatic name of the requested namespace
     * @throws \RuntimeException If the session was not successfully started.
     */
    public function start( $namespace = self::DEFAULT_NAMESPACE )
    {
        if( $namespace===self::CSRF_NAMESPACE )
        {
            $this->startNamespaceCSRF();
        }
        else
        {
            throw new \RuntimeException( 'Session namespace is not valid.' );
        }
        return TRUE;
    }

    /**
     * Write session data and end session.
     */
    public function close()
    {
        \Zend_Session::writeClose(TRUE);
    }

    /**
     * Free and destroy all session variables and data registered to a session.
     */
    public function free()
    {
        try
        {
            if( isset($this->_pNamespaceCSRF) )
            {
                $this->_pNamespaceCSRF->unsetAll();
            }//if( isset(...
            if( \Zend_Session::isStarted() && !\Zend_Session::isDestroyed() )
            {
                \Zend_Session::destroy();
            }
        }
        catch( \Exception $e )
        {
            // Do nothing
        }//try...
    }

    /**************************************************************************
     * TOKEN
     **************************************************************************/

    /**
     * Writes current token value into the PHP session.
     *
     * @throws \UnexpectedValueException If the value of the token is not valid.
     * @throws \RuntimeException If the session was not successfully started.
     */
    public function writeToken()
    {
        // @codeCoverageIgnoreStart
        if( !$this->_sTokenValue->isValid() )
        {
            throw new \UnexpectedValueException( 'Token value is not valid.' );
        }//if( empty(...
        // @codeCoverageIgnoreEnd
        if( isset( $this->_pNamespaceCSRF ) )
        {
            try
            {
                $this->_pNamespaceCSRF->token = (string)$this->_sTokenValue;
            }
            catch( \Exception $e)
            {
                // @codeCoverageIgnoreStart
                throw new \RuntimeException( $e->getMessage(), $e->getCode() );
                // @codeCoverageIgnoreEnd
            }//try...
        }
        else
        {
            throw new \RuntimeException( 'The session was not successfully started.' );
        }//if( isset(...
        return TRUE;
   }

    /**
     * Returns TRUE if the token's $value matches with the one registered in the PHP session.
     *
     * @param string $value
     * @return boolean
     */
    public function validateToken( $value )
    {
        // Initialize
        $bReturn = FALSE;
        $pPatterns = new \Common\Filter\CPattern();
        $pPattern = $pPatterns->getSession();

        // Filter input value
        $pInput = new \Common\Type\CString( $value, $pPattern );

        // Filter session value
        if( $pInput->isValid() && isset( $this->_pNamespaceCSRF ) )
        {
            try
            {
                $pSession = new \Common\Type\CString( $this->_pNamespaceCSRF->token, $pPattern );
                if( $pSession->isValid() )
                {
                    // Validate
                    $bReturn = $pInput->isEqual( $pSession );
                }//if(...
            }
            catch( \Exception $e)
            {
                // @codeCoverageIgnoreStart
                $bReturn = FALSE;
                // @codeCoverageIgnoreEnd
            }//try...
            unset($pSession);
        }//if(...
        unset( $pInput, $pPattern, $pPatterns );
        return $bReturn;
    }

}
