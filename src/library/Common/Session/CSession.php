<?php namespace Common\Session;
/**
 * Session class.
 *
 * This file contains a class which implements main methods for session management.
 *
 * @package Common\Session
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class implements main methods for session management.
 */
final class CSession extends \Common\Session\CSessionAbstract
{
    /**
     * Whether or not CSession is being used with unit tests
     *
     * @internal
     * @var bool
     */
    public $_unitTestEnabled = false;

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
     * Initializes and starts session.
     *
     * @param string $namespace - programmatic name of the requested namespace
     * @throws \RuntimeException If the session was not successfully started or already started.
     */
    public function start( $namespace = self::DEFAULT_NAMESPACE )
    {
        if( !$this->_unitTestEnabled && defined('SID') )
        {
            throw new \RuntimeException( 'Session has already been started by session.auto-start or session_start().' );
        }
        if( !session_start() )
        {
            // @codeCoverageIgnoreStart
            $aErrorDescription = error_get_last();
            throw new \RuntimeException( $aErrorDescription['message'], $aErrorDescription['type'] );
            // @codeCoverageIgnoreEnd
        }
        return TRUE;
    }

    /**
     * Write session data and end session.
     */
    public function close()
    {
        session_write_close();
    }

    /**
     * Free and destroy all session variables and data registered to a session.
     */
    public function free()
    {
        if( session_id() != "" )
        {
            // Unset
            session_unset();
            // Destroy
            session_destroy();
        }
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
        if( isset($_SESSION) )
        {
            $_SESSION[self::CSRF_NAMESPACE][self::CSRF_TAG] = (string)$this->_sTokenValue;
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
        if( $pInput->isValid() && isset($_SESSION)
    && (isset($_SESSION[self::CSRF_NAMESPACE]) || array_key_exists(self::CSRF_NAMESPACE,$_SESSION) )
    && (isset($_SESSION[self::CSRF_NAMESPACE][self::CSRF_TAG]) || array_key_exists(self::CSRF_TAG,$_SESSION[self::CSRF_NAMESPACE]) )
          )
        {
            $pSession = new \Common\Type\CString( $_SESSION[self::CSRF_NAMESPACE][self::CSRF_TAG], $pPattern );
            if( $pSession->isValid() )
            {
                // Validate
                $bReturn = $pInput->isEqual( $pSession );
            }//if(...
            unset($pSession);
        }//if(...
        unset( $pInput, $pPattern, $pPatterns );
        return $bReturn;
    }

}
