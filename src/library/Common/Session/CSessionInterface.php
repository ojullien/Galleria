<?php namespace Common\Session;
/**
 * Interface class for session implementation.
 *
 * This file contains an interface class for session implementation.
 *
 * @package Common\Session
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * Interface class for session implementation.
 */
interface CSessionInterface
{
    /**
     * Initializes and starts session.
     *
     * @param string $namespace - programmatic name of the requested namespace
     * @throws \RuntimeException If the session was not successfully started.
     */
    public function start( $namespace = self::DEFAULT_NAMESPACE );

    /**
     * Write session data and end session.
     */
    public function close();

    /**
     * Free and destroy all session variables and data registered to a session.
     */
    public function free();

    /**************************************************************************
     * TOKEN
     **************************************************************************/

    /**
     * Writes current token value into the PHP session.
     *
     * @throws \UnexpectedValueException If the value of the token is not valid.
     * @throws \RuntimeException If the session was not successfully started.
     */
    public function writeToken();

    /**
     * Returns TRUE if the token's $value matches with the one registered in the PHP session.
     *
     * @param string $value
     * @return boolean
     */
    function validateToken( $value );

}
