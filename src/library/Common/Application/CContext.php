<?php
namespace Common\Application;

/**
 * Initialize application context
 *
 * This file contains a class which implements usefull functions to initialize
 * application context
 *
 * @package Common\Application
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class implements usefull functions for application initialization
 */
final class CContext
{
    /**
     * Environment context
     */
    const ERRORREPORTLEVELDEV = 32767;
    const ERRORREPORTLEVELPROD = 30711;
    const PHPVERSIONREQUIRED = '5.3.3';
    const DEFAULTTIMEZONE = 'Europe/Paris';

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    private $_sDebugID = '';

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
     * Constructor
     * @codeCoverageIgnore
     * @param mixed $value
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
     * Set PHP error reporting level
     * Return the old error_reporting level or FALSE if an error occures.
     *
     * @param  integer         $value Environment level (ERRORREPORTLEVELDEV or ERRORREPORTLEVELPROD)
     * @return integer|boolean FALSE on error
     */
    public function setErrorReporting( $value )
    {
        // Initialize
        $iReturn = FALSE;
        $pLevel = new \Common\Type\CInt( $value, 0 );
        // Set
        if( $pLevel->isValid() )
        {
            switch( $pLevel->getValue() )
            {
                case self::ERRORREPORTLEVELDEV:
                    // Development level
                    $iReturn = error_reporting();
                    error_reporting(E_ALL | E_STRICT);
                    break;
                case self::ERRORREPORTLEVELPROD:
                    // Production level
                    $iReturn = error_reporting();
                    error_reporting(E_ALL ^ E_NOTICE);
                    break;
                default:
                    $iReturn = FALSE;
            }//switch...
        }//if...
        unset($pLevel);
        return $iReturn;
    }

    /**
     * Updates memory limit.
     * Returns FALSE if an error occures.
     *
     * @param \Common\Type\CByte $pNew Memory limit
     * @return boolean
     */
    public function setMemoryLimit( \Common\Type\CByte $pNew )
    {
        $bReturn = FALSE;
        // Maybe this action is not allowed
        if( $pNew->isValid() && function_exists('memory_get_usage') )
        {
            // Set the new memory value
            @ini_set('memory_limit', $pNew->getValue() );
            // Get the updated memory value
            $pUpdated = new \Common\Type\CByte( @ini_get('memory_limit') );
            // Check if updated
            $bReturn = $pUpdated->isEqual($pNew);
            unset($pUpdated);
        }//if(...
        return $bReturn;
    }

    /**
     * Test PHP version
     *
     * @return boolean FALSE if version not valid
     */
    public function isPHPVersionValid()
    {
        $bReturn = FALSE;
        // Get current PHP version
        $sPHPVersion = phpversion();
        $sPHPVersionRequired = self::PHPVERSIONREQUIRED;
        if( version_compare( $sPHPVersion, $sPHPVersionRequired, '>=') )
        {
            $bReturn = TRUE;
        }//if...
        return $bReturn;
    }

    /**
     * Returns TRUE if unicode is enabled
     * @return boolean
     */
    public function unicodeEnabled()
    {
        return (@preg_match('/\pL/u', 'a')) ? TRUE : FALSE;
    }

    /**
     * Unset global register
     *
     * @deprecated deprecated since PHP version 5.3
     * @return boolean FALSE if an error occures
     * @codeCoverageIgnore
     */
    public function unsetGlobalRegister()
    {
        $bReturn = TRUE;
        $sBuffer = @ini_get('register_globals');
        if( isset($sBuffer)
                && ($sBuffer != FALSE)
                && (strlen($sBuffer) >  0)
                && ($sBuffer != '0') )
        {
            // register globals is ON
            die('ABORT because register globals is ON!');
        }//if...
        return $bReturn;
    }

    /**
     * Disable magic quotes
     *
     * @deprecated deprecated since PHP version 5.3
     * @return boolean FALSE if an error occures
     * @codeCoverageIgnore
     */
    public function disableMagicQuotes()
    {
        $bReturn = TRUE;
        if( function_exists('get_magic_quotes_runtime') )
        {
            if( get_magic_quotes_runtime()==1 )
            {
                die('ABORT because magic_quotes_runtime is ON!');
            }//if...
        }//if...
        if( function_exists('get_magic_quotes_gpc') )
        {
            if( get_magic_quotes_gpc()==1 )
            {
                die('ABORT because get_magic_quotes_gpc is ON!');
            }//if...
        }//if...
        return $bReturn;
    }

    /**
     * Deep stripslashes
     *
     * Navigates through an array and removes slashes from the values.
     * If an array is passed, the array_map() function causes a callback to pass
     * the value back to the function. The slashes from this value will removed.
     *
     * @static
     * @param string|array $sValue The array or string to be striped
     * @return string|array Stripped array (or string in the callback)
     */
    public static function stripSlashesDeep($sValue)
    {
        if( isset($sValue) && !is_object($sValue) && !is_resource($sValue) )
        {
            $sValue = is_array($sValue)
                ? array_map('\Common\Application\CContext::stripSlashesDeep', $sValue)
                : stripslashes($sValue);
        }
        else
        {
            $sValue = '';
        }//if...
        return $sValue;
    }

    /**
     * Strip slashes from GET/POST/COOKIE
     *
     * @deprecated deprecated since PHP version 5.3
     * @return boolean FALSE if an error occures
     * @codeCoverageIgnore
     */
    public function stripSlashesRegisters()
    {
        $bReturn = TRUE;
        if( function_exists('get_magic_quotes_gpc') )
        {
            if( get_magic_quotes_gpc()==1 )
            {
                $_GET    = \Common\Application\CContext::stripSlashesDeep($_GET   );
                $_POST   = \Common\Application\CContext::stripSlashesDeep($_POST  );
                $_COOKIE = \Common\Application\CContext::stripSlashesDeep($_COOKIE);
            }//if...
        }//if...
        return $bReturn;
    }

    /**
     * Test operating system PHP is running on.
     * Returns TRUE if PHP is running on windows system.
     *
     * @return boolean
     * @codeCoverageIgnore
     */
    public function runOnWin()
    {
        $bReturn = FALSE;
        $sOS = php_uname('s');
        if( strtoupper( substr($sOS, 0, 3)) === 'WIN')
        {
            $bReturn = TRUE;
        }
        return $bReturn;
    }

    /**
     * Sets the default timezone used by all date/time functions in the
     * application.
     *
     * This function returns FALSE if the timezone_identifier isn't valid, or
     * TRUE otherwise.
     *
     * Instead of using this function to set the default timezone in the
     * application, you can also use the INI setting date.timezone to set the
     * default timezone.
     *
     * @param \Common\Type\CString $timezone_identifier
     * @return boolean
     */
    public function setDefaultTimeZone( \Common\Type\CString $timezone_identifier )
    {
        $bReturn = FALSE;
        if( $timezone_identifier->getLength()>0 )
        {
            $bReturn = date_default_timezone_set( (string)$timezone_identifier );
        }
        return $bReturn;
    }

}
