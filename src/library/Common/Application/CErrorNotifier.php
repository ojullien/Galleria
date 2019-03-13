<?php namespace Common\Application;
/**
 * Error notifier.
 *
 * This file contains a class which provides an extensive report providing
 * helpful details in what state the application was when an exception occurred.
 *
 * @package Common\Application
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class provides an extensive report providing helpful details in what
 * state the application was when an exception occurred.
 */
final class CErrorNotifier
{
    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    private $_sDebugID = '';

    /**
     * Application environment
     * @var string
     */
    private $_sEnvironment = 'production';

    /**
     * error_handler object
     * @var ArrayObject
     */
    private $_pError = NULL;

    /**
     * Server
     * @var array
     */
    private $_aServer = array();

    /**
     * Session
     * @var array
     */
    private $_aSession = array();

    /**
     * Mailer object
     * @var \Zend_Mail
     */
    private $_pMailer = NULL;

    /**
     * Database profiler
     * @var \Zend_Db_Profiler
     */
    private $_pProfiler = NULL;

    /**
     * Http Response Code
     * @var integer
     */
    private $_iHttpResponseCode = 500;

    /**
     * Priority
     * @var integer
     */
    private $_iPriority = \Common\Type\CEnumPriority::CRIT;

    /**
     * Title
     * @var string
     */
    private $_sTitle = 'Error';

    /**
     * Description
     * @var string
     */
    private $_sDescription = ' Unexpected error.';

    /**
     * Keywords
     * @var string
     */
    private $_sKeywords = ',error';

    /**
     * Short message
     * @var string
     */
    private $_sMessage = 'An unexpected error occurred.';

    /**
     * Display exception
     * @var boolean
     */
    private $_bDisplayExceptions = FALSE;

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
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __destruct()
    {
        $this->_pError = NULL;
        $this->_aSession = array();
        unset($this->_pMailer);
        unset($this->_pProfiler);
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') && !defined('COMMON_DEBUG_OFF') )
            \Common\Debug\CDebug::getInstance()->addMemoryDelete($this->_sDebugID);
        //@codeCoverageIgnoreEnd
    }

    /**
     * Constructor
     * @param string            $sEnvironment        Application environment
     * @param \ArrayObject      $pError              Error handler object
     * @param boolean           $bDisplayExceptions  Exceptions
     * @param array             $aServer             Server data
     * @param array             $aSession            Session data
     * @param \Zend_Mail        $pMailer             OPTIONAL - Mailer
     * @param \Zend_Db_Profiler $pProfiler           OPTIONAL - Database profiler
     */
    public function __construct( $sEnvironment,
                                 $pError,
                                 $bDisplayExceptions = FALSE,
                                 array $aServer = array(),
                                 array $aSession = array(),
                                 $pMailer = NULL,
                                 $pProfiler = NULL)
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, '' );
        }//if( defined(...
        //@codeCoverageIgnoreEnd

        // Set
        if( is_string($sEnvironment) )
        {
            $this->_sEnvironment = trim($sEnvironment);
        }//if( is_string(...
        $this->_pError             = $pError;
        $this->_bDisplayExceptions = $bDisplayExceptions;
        $this->_aServer            = $aServer;
        $this->_aSession           = $aSession;
        $this->_pMailer            = $pMailer;
        $this->_pProfiler          = $pProfiler;

        // Analyse
        if( isset($this->_pError) && ($this->_pError instanceof \ArrayObject) )
        {
            switch( $this->_pError->type )
            {
                case \Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
                case \Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
                case \Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                    // 404 error -- route, controller or action not found
                    $this->_iHttpResponseCode = 404;
                    $this->_iPriority = \Common\Type\CEnumPriority::NOTICE;
                    $this->_sTitle = 'Error 404';
                    $this->_sDescription = ' Page not found.';
                    $this->_sKeywords = ',error 404,page not found';
                    $this->_sMessage = 'Page not found';
                    break;

                default:
                    // application error
                    // Keep the default values
                    break;
            }//switch( ...
        }//if( isset(...
    }

    /**
     * Returns application environment
     *
     * @return string
     */
    public function getEnvironment(){return $this->_sEnvironment;}

    /**
     * Returns the Http Response Code
     *
     * @return integer
     */
    public function getHttpResponseCode(){return $this->_iHttpResponseCode;}

    /**
     * Returns the priority
     *
     * @return \Common\Type\CEnumPriority
     */
    public function getPriority(){return new \Common\Type\CEnumPriority($this->_iPriority);}

    /**
     * Returns the title
     *
     * @return string
     */
    public function getTitle(){return $this->_sTitle;}

    /**
     * Returns the description
     *
     * @return string
     */
    public function getDescription(){return $this->_sDescription;}

    /**
     * Returns the keywords
     *
     * @return string
     */
    public function getKeywords(){return $this->_sKeywords;}

    /**
     * Returns short message
     *
     * @return \Common\Type\CString
     */
    public function getMessageShort()
    {
        $sReturn = $this->_sMessage;
        if( isset($this->_pError) && isset($this->_pError->exception) )
        {
            $sReturn .= ' - Message: ' . $this->_pError->exception->getMessage();
        }
        if( isset($this->_pError) && isset($this->_pError->request) )
        {
            $sReturn .= ' - Request URI: ' . $this->_pError->request->getRequestUri();
        }//if( isset(...
        return new \Common\Type\CString($sReturn);
    }

    /**
     * Returns full message. If $bEscape is TRUE, the message is sanitazed for
     * html output.
     *
     * @param boolean $bEscape
     * @return string
     */
    public function getMessageFull( $bEscape=TRUE )
    {
        // Time
        $sReturn = 'Server time: ' . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

        // Exception
        if( $this->_bDisplayExceptions && isset($this->_pError) && isset($this->_pError->exception) )
        {
            $sReturn .= 'Message: ' . $this->_pError->exception->getMessage() . PHP_EOL . PHP_EOL;
            $sReturn .= 'Stack trace: ' . PHP_EOL;
            $sReturn .= $this->_pError->exception->getTraceAsString();
        }
        else
        {
            $sReturn .= 'Message: Unexpected error';
        }
        $sReturn .= PHP_EOL . PHP_EOL;

        // Request
        $sReturn .= "Request Parameters: ";
        if( isset($this->_pError) && isset($this->_pError->request) )
            $sReturn .= print_r($this->_pError->request->getParams(), TRUE);
        else
            $sReturn .= 'EMPTY';
        $sReturn .= PHP_EOL;

        $sReturn .= 'User agent: ';
        if( isset($this->_aServer['HTTP_USER_AGENT']) || array_key_exists('HTTP_USER_AGENT',$this->_aServer) )
        {
            $sReturn .= $this->_aServer['HTTP_USER_AGENT'];
        }
        else
        {
            $sReturn .= 'EMPTY';
        }
        $sReturn .= PHP_EOL . PHP_EOL;

        $sReturn .= 'Request type: ';
        if( isset($this->_aServer['HTTP_X_REQUESTED_WITH']) || array_key_exists('HTTP_X_REQUESTED_WITH',$this->_aServer) )
        {
            $sReturn .= $this->_aServer['HTTP_X_REQUESTED_WITH'];
        }
        else
        {
            $sReturn .= 'EMPTY';
        }
        $sReturn .= PHP_EOL . PHP_EOL;

        $sReturn .= 'Request URI: ';
        if( isset($this->_pError) && isset($this->_pError->request) )
            $sReturn .= $this->_pError->request->getRequestUri() . PHP_EOL;
        else
            $sReturn .= 'EMPTY';
        $sReturn .= PHP_EOL;

        $sReturn .= 'Referer: ';
        if( isset($this->_aServer['HTTP_REFERER']) || array_key_exists('HTTP_REFERER',$this->_aServer) )
        {
            $sReturn .= $this->_aServer['HTTP_REFERER'];
        }
        else
        {
            $sReturn .= 'EMPTY';
        }
        $sReturn .= PHP_EOL . PHP_EOL;

        // Session
        $sReturn .= 'Session data: ';
        if( count($this->_aSession)>0 )
        {
            $sReturn .= print_r($this->_aSession, TRUE);
        }
        else
        {
            $sReturn .= 'EMPTY';
        }
        $sReturn .= PHP_EOL;

//        $query = $this->_profiler->getLastQueryProfile()->getQuery();
//        $queryParams = $this->_profiler->getLastQueryProfile()->getQueryParams();
//
//        $message .= "Last database query: " . $query . "\n\n";
//        $message .= "Last database query params: " . var_export($queryParams, true) . "\n\n";

        if( $bEscape )
        {
            $sReturn = htmlentities( $sReturn, ENT_QUOTES, 'UTF-8');
        }
        return $sReturn;
    }

    /**
     * Sends email.
     *
     * @return boolean
     * @todo WRITE IMPLEMENTATION
     */
    public function notify()
    {
        $bReturn = FALSE;
        if( isset($this->_pMailer) && ($this->_sEnvironment=='production') )
        {
            $this->_pMailer->setFrom('do-not-reply@paintballbet.com');
            $this->_pMailer->setSubject("Exception on Application");
            $this->_pMailer->setBodyText( '@TODO' );
            $this->_pMailer->addTo('webmaster@paintballbet.com');
            $bReturn = $this->_pMailer->send();
        }
        return $bReturn;
    }

}
