<?php namespace Common\Debug;
/**
 * PHP Backtrace
 *
 * This file contains a class which implements usefull functions for
 * PHP backtrace
 *
 * @package Common\Debug
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class implements usefull functions for memory display usage
 * @codeCoverageIgnore
 */
final class CTrace
{
    /** Private attributs
     ********************/

    /**
     * Singleton
     * @var CTrace
     */
    private static $m_pInstance = NULL;

    /**
     * File descriptor
     * @var integer
     */
    protected $m_pFile = NULL;

    /**
     * Last backtrace
     * @var array
     */
    protected $m_aLastBackTrace = array();

    /** Private methods
     ******************/

    /**
     * Constructor.
     */
    private function __construct(){}

    /**
     * Returns TRUE if the file is open
     * @return boolean
     */
    private function isOpen()
    {
        $bReturn = FALSE;
        if( isset($this->m_pFile) && is_resource($this->m_pFile) )
        {
            $bReturn=TRUE;
        }//if( isset(...
        return $bReturn;
    }

    /**
     * Close the file.
     * Returns FALSE if an error occures.
     * @return boolean
     */
    private function close()
    {
        $bReturn = TRUE;
        if( $this->isOpen()===TRUE )
        {
            $bReturn = fclose( $this->m_pFile );
            $this->m_pFile = NULL;
        }//if( $this->IsOpen(...
        return $bReturn;
    }

    /**
     * Writes data to the file
     * Returns number of bytes written or FALSE if an error occures.
     * @param string $sBuffer
     * @return integer|FALSE
     */
    private function write( $sBuffer )
    {
        $iReturn = FALSE;
        if( is_string($sBuffer) && $this->isOpen() )
        {
            // Write
            $iReturn = fwrite( $this->m_pFile, $sBuffer."\n" );
            // Flush
            if( $iReturn!==FALSE )
            {
                fflush( $this->m_pFile );
            }//if( $iReturn!==FALSE )
        }//if(...
        return $iReturn;
    }

    /**
     * Analyses and build the line to write.
     *
     * @param array $aValue
     * @return string
     */
    private function build(array $aValue )
    {
        $sLine = 'unknown backtrace';
        if( array_key_exists('function',$aValue) && array_key_exists('file',$aValue) && array_key_exists('line',$aValue) )
        {
            if( $aValue['function'] == "include" || $aValue['function'] == "include_once" || $aValue['function'] == "require_once" || $aValue['function'] == "require")
            {
                $sLine = $aValue['function'] . '(' . $aValue['args'][0] . ')';
            }
            else
            {
                if( array_key_exists( 'class', $aValue ) )
                {
                    // methods
                    $sLine = $aValue['class'] . $aValue['type'] . $aValue['function'];
                }
                else
                {
                    // function
                    $sLine = $aValue['function'];
                }//if( array_key_exists(...
                $sLine .= '()';
            }//if(...
            $sLine .= ' [' . $aValue['file'] . ':' . $aValue['line'] .']';
        }//if( array_key_exists(...
        return $sLine;
    }

    /**
     * Compares with lastbacktrace.
     * Returns TRUE if they are equals, FALSE otherwise.
     *
     * @param string  $sLine
     * @param integer $iIndex
     * @return boolean
     */
    private function same( $sLine, $iIndex )
    {
        $bReturn = FALSE;
        if( is_string($sLine) && is_int($iIndex) && is_array($this->m_aLastBackTrace) )
        {
            // Compare with last backtrace
            if( isset($this->m_aLastBackTrace[$iIndex]) && (strcmp($this->m_aLastBackTrace[$iIndex],$sLine)==0) )
            {
                $bReturn = TRUE;
            }//if( isset(...
        }//if( is_string(...
        return $bReturn;
    }

    /** Public methods
     *****************/

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
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Clone is not allowed
     */
    public function __clone()
    {
        throw new \BadMethodCallException( 'Cloning data to inaccessible properties is not allowed.' );
    }

    /**
     * Retrieves the default class instance.
     * @return CTrace
     */
    public static function getInstance()
    {
        if( !isset(self::$m_pInstance) )
        {
            self::$m_pInstance = new \Common\Debug\CTrace();
        }
        return self::$m_pInstance;
    }

    /**
     * Deletes instance
     */
    public static function deleteInstance()
    {
        if( isset(self::$m_pInstance) )
        {
            self::$m_pInstance->close();
            $tmp=self::$m_pInstance;
            self::$m_pInstance=NULL;
            unset($tmp);
        }
    }

    public function open( $sFile )
    {
        // Initialize
        $bReturn = FALSE;
        // Cast to string
        if( is_scalar($sFile) )
        {
            $sFile = trim($sFile);
        }
        // Open it
        if( is_string($sFile) )
        {
            // Clears file status cache
            clearstatcache();
            // Check the file
            if( is_file( $sFile ) )
            {
                // File already exists - rename it
                $sFileNew = $sFile . '_' . date('Ymd_His');
                rename( $sFile, $sFileNew );
            }
            // Create and open a new one
            $this->m_pFile = fopen( $sFile, 'w+b');
            $bReturn = $this->isOpen();
        }//if( is_string(...
        return $bReturn;
    }

    /**
     * Analyses and writes the backtrace.
     *
     * @param array $aCurrentBackTrace
     */
    public function trace( array $aCurrentBackTrace )
    {
        // Reverse the array
        $aReverseBackTrace = array_reverse( $aCurrentBackTrace );
        $aCurrentBackTrace = array();
        $sTab = '';
        $iIndex = 0;
        foreach( $aReverseBackTrace as $key=>$value )
        {
            $sLine = $this->build($value);

            // Push in array
            $aCurrentBackTrace[] = $sLine;

            // Compare with last backtrace
            if( !$this->same($sLine, $iIndex) )
            {
                $this->write( $sTab . $sLine );
            }//if(...
            // tabulation
            $sTab .= '    ';
            $iIndex += 1;
        }//foreach(...
        // Save the backtrace
        $this->m_aLastBackTrace = $aCurrentBackTrace;
    }

}
define('COMMON_DEBUG_TRACE', '1.0.0');
