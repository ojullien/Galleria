<?php namespace Common\Session;
/**
 * Parent Class for all session.
 *
 * This file contains a class which implements default constant and methods
 * for sessions.
 *
 * @package Common\Session
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * Parent class for sessions
 */
abstract class CSessionAbstract implements \Common\Session\CSessionInterface
{
    /** Constants */
    const SESSION_NAME      = 'SESSID';
    const DEFAULT_NAMESPACE = 'default';
    const CSRF_TAG          = 'token';
    const CSRF_NAMESPACE    = 'csrf';

    /** Protected attributs
     **********************/

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    protected $_sDebugID = '';

    /**
     * Token value
     * @var \Common\Type\CString
     */
    protected $_sTokenValue = '';

    /** Public methods
     *****************/

    /**
     * Constructor.
     * Generate a new value for the token
     */
    public function __construct()
    {
        // Set the current session name
        session_name( APPLICATION_NAME . self::SESSION_NAME );
        // Generate new token value
        $this->_sTokenValue = new \Common\Type\CString( md5(uniqid(rand())) );
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset( $this->_sTokenValue );
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') && !defined('COMMON_DEBUG_OFF') )
            \Common\Debug\CDebug::getInstance()->addMemoryDelete( $this->_sDebugID );
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

    /**************************************************************************
     * TOKEN
     **************************************************************************/

    /**
     * Reads current token value.
     *
     * @return \Common\Type\CString
     */
    final public function getToken()
    {
        return $this->_sTokenValue;
    }

}
