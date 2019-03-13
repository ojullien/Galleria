<?php namespace Common\Debug;
/**
 * Time and memory usage usefull functions
 *
 * This file contains a class which implements usefull functions for
 * time and memory display usage
 *
 * @package Common\Debug
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class implements usefull functions for memory display usage.
 * @codeCoverageIgnore
 */
final class CDebug
{

    /** Private attributs
     ********************/

    /**
     * Singleton
     * @var \Common\Debug\CDebug
     */
    private static $_pInstance = NULL;

    /**
     * Beginning cookie state
     * @var array
     */
    private $_aCookie = array();

    /**
     * Beginning session state
     * @var array
     */
    private $_aSession = array();

    /**
     * Memory allocation stack
     * @var array
     */
    private $_aMemory = array();

    /** Private methods
     ******************/

    /**
     * Constructor.
     */
    private function __construct()
    {
        if( isset($_COOKIE) )
            $this->_aCookie  = $_COOKIE;
        if( isset($_SESSION) )
            $this->_aSession = $_SESSION;
    }

    /**
     * Compare two arrays.
     *
     * @param array $array1
     * @param array $array2
     * @return string
     */
    private function compareTwoArrays( array $array1, array $array2)
    {
        $sReturn = $sBuffer = '';
        foreach( $array1 as $key => $value )
        {
            // Key
            ////////////////////////////////////////////////////////////////////
            $sBuffer = '[' . htmlentities( $key, ENT_QUOTES, 'UTF-8') . '] => ';

            // Values
            ////////////////////////////////////////////////////////////////////
            if( isset($array2[$key]) || array_key_exists($key,$array2) )
            {
                // Still exists
                $sColor = 'black';

                // Compare
                if( is_array($value) && is_array($array2[$key]) )
                {
                    // Both are arrays
                    $sBuffer .= 'Array ( ';
                    $sBuffer .= $this->compareTwoArrays( $value, $array2[$key] );
                    $sBuffer .= ' )';
                }
                elseif( is_array($value) && !is_array($array2[$key]) )
                {
                    // Only one is array
                    $sBuffer .= print_r( $value, TRUE );
                    $sBuffer .= ' => '  . htmlentities( $array2[$key], ENT_QUOTES, 'UTF-8');
                }
                elseif( !is_array($value) && is_array($array2[$key]) )
                {
                    // Only one is array
                    $sBuffer .= htmlentities( $value, ENT_QUOTES, 'UTF-8');
                    $sBuffer .= ' => ' . print_r( $array2[$key], TRUE );
                }
                else
                {
                    // Both are scalar
                    $sBuffer .= htmlentities( (is_null($value)?'NULL':$value), ENT_QUOTES, 'UTF-8');
                    if( $value!==$array2[$key] )
                    {
                        // But not equals
                        $sBuffer .= ' => ' . htmlentities( (is_null($array2[$key])?'NULL':$array2[$key]), ENT_QUOTES, 'UTF-8');
                    }//if( $value===$array2[$key] )
                }//if( is_array(...
            }
            else
            {
                // Does not exist anymore
                $sColor = 'red';
                if( is_array($value) )
                {
                    // Was an array
                    $sBuffer .= print_r( $value, TRUE );
                }
                else
                {
                    // Was a scalar
                    $sBuffer .= htmlentities( (is_null($value)?'NULL':$value), ENT_QUOTES, 'UTF-8');
                }//if( is_array(...
            }//if( isset(...
            $sReturn .= '<pre style="color:' . $sColor . ';">' . $sBuffer . '</pre>';

        }//foreach(...

        // New one
        foreach( $array2 as $key => $value )
        {
            //if( !isset($array1[$key]) )
            if( !isset($array1[$key]) && !array_key_exists($key,$array1) )
            {
                $sReturn .= '<pre style="color:green;">[' . htmlentities( $key, ENT_QUOTES, 'UTF-8') . '] => ';
                if( is_array($value) )
                {
                    // Is an array
                    $sReturn .= print_r( $value, TRUE );
                }
                else
                {
                    // Is a scalar
                    $sReturn .= htmlentities( (is_null($value)?'NULL':$value), ENT_QUOTES, 'UTF-8');
                }//if( is_array(...
                $sReturn .= '</pre>';
            }//if( !isset($array1[$key]) )
        }//foreach(...

        return $sReturn;
    }

    /** Public methods
     *****************/

    /**
     * Destructor
     */
    public function __destruct(){}

    /**
     * Clone is not allowed
     * @codeCoverageIgnore
     */
    public function __clone()
    {
        throw new \BadMethodCallException( 'Cloning is not allowed.' );
    }

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
     * Retrieves the default class instance.
     * @return \Common\Debug\CDebug
     */
    public static function getInstance()
    {
        if( !isset(self::$_pInstance) )
        {
            self::$_pInstance = new \Common\Debug\CDebug();
        }
        return self::$_pInstance;
    }

    /**
     * Deletes instance
     */
    public static function deleteInstance()
    {
        if( isset(self::$_pInstance) )
        {
            $tmp=self::$_pInstance;
            self::$_pInstance=NULL;
            unset($tmp);
        }//if( isset(...
        if( !defined('COMMON_DEBUG_OFF') )
        {
            define('COMMON_DEBUG_OFF',1);
        }//if( !defined(...
    }

    /** Memory methods
     *****************/

    /**
     * Get and format the current memory usage
     *
     * @return string
     */
    private function getMemoryUsage()
    {
        $sBuffer = 'memory_get_usage is not available';
        if( function_exists('memory_get_usage') )
        {
            $pBytes = new \Common\Type\CByte( @memory_get_usage() );
            $sBuffer = round( $pBytes->convertToMByte(), 2) . ' MByte(s)';
            unset($pBytes);
        }
        return (string) $sBuffer;
    }

    /**
     * Get and format the peak of memory allocated by PHP.
     *
     * @return string
     */
    private function getMemoryPeakUsage()
    {
        $sBuffer = 'memory_get_peak_usage is not available';
        if( function_exists('memory_get_peak_usage') )
        {
            $pBytes = new \Common\Type\CByte( @memory_get_peak_usage() );
            $sBuffer = round( $pBytes->convertToMByte(), 2) . ' MByte(s)';
            unset($pBytes);
        }
        return (string) $sBuffer;
    }

    /**
     * Add a memory allocation trace
     */
    public function addMemoryNew( $sId, $sClass, $pParam)
    {
        $fTime = microtime(true);
        if( is_string($sId) && is_string($sClass))
        {
            if( isset($this->_aMemory[$sId]) )
            {
                throw new \InvalidArgumentException('Id already exists');
            }
            if( $pParam instanceof \Common\Type\CTypeAbstract )
            {
                $sClass .= '(' . (string)$pParam . ')';
            }
            else if( is_scalar($pParam) )
            {
                $sClass .= '(' . $pParam . ')';
            }
            else
            {
                $sClass .= '(' . gettype($pParam) . ')';
            }//if( is_scalar(...
            $this->_aMemory[$sId] = array( 'name' => $sClass, 'start' => $fTime, 'end' => 0.0);
        }
        else
        {
            throw new \InvalidArgumentException( __METHOD__ . ' Invalid arguments');
        }
    }

    /**
     * Add a memory disalloction trace
     */
    public function addMemoryDelete( $sId )
    {
        $fTime = microtime(true);
        if( is_string($sId) )
        {
            if( !isset($this->_aMemory[$sId]) )
            {
                $sDebugID = uniqid(rand());
                $this->_aMemory[$sDebugID] = array('name'  => 'UNKNOWN',
                                                   'start' => $fTime,
                                                   'end'   => $fTime);
            }
            else
            {
                $this->_aMemory[$sId]['end'] = $fTime;
            }
        }
        else
        {
            throw new \InvalidArgumentException( __METHOD__ . ' Invalid argument');
        }
    }

    /** Time methods
     ***************/

    /**
     * This method computes elapsed time with's millisecond's precision.
     *
     * @param type $fTimeStart Start time
     * @param type $fTimeEnd End time
     * @return float
     */
    public function getElapsedTime( $fTimeStart=NULL, $fTimeEnd=NULL)
    {
        // Initialize start time
        if( !isset($fTimeStart) )
        {
            $sKey = APPLICATION_NAME . '_START_TIME';
            $fTimeStart = (isset($_SERVER[$sKey])) ? (float)$_SERVER[$sKey]
                                                   : (float)$_SERVER['REQUEST_TIME'];
        }//if( !isset(...
        // Initialize end time
        if( !isset($fTimeEnd) )
        {
            $fTimeEnd = microtime(true);
        }//if( !isset(...
        return round( ($fTimeEnd - $fTimeStart)*1000);
    }

     /** Render methods
     ******************/

    /**
     * Returns the script execution time and the memory usage in human readable
     * format.
     *
     * @return string
     */
    private function renderUsage()
    {
        $sReturn  = 'Script';
        $sReturn .= ' Duration: ' . $this->getElapsedTime() .' ms';
        $sReturn .= ' Memory Peak Usage: ' . $this->getMemoryPeakUsage();
        $sReturn .= ' Current Memory Usage: ' . $this->getMemoryUsage();
        return $sReturn;
    }

    /**
     * Returns data from cookie.
     *
     * @return string
     */
    private function renderCookie()
    {
        $sReturn = 'Cookie:' . PHP_EOL;
        if( isset($_COOKIE) )
            $sReturn .= $this->compareTwoArrays( $this->_aCookie, $_COOKIE );
        return $sReturn;
    }

    /**
     * Returns data from session.
     *
     * @return string
     */
    private function renderSession()
    {
        $sReturn  = 'Session:' . PHP_EOL;
        if( isset($_SESSION) )
            $sReturn .= $this->compareTwoArrays( $this->_aSession, $_SESSION );
        return $sReturn;
    }

    /**
     * Returns data from POST or GET.
     *
     * @return string
     */
    private function renderQuery( array $query, $sName )
    {
        $sReturn  = $sName . ':' . PHP_EOL;
        foreach( $query as $key => $value )
        {
            $sReturn .= '<pre>[' . htmlentities( $key, ENT_QUOTES, 'UTF-8') . '] => ';
            $sReturn .= htmlentities( $value, ENT_QUOTES, 'UTF-8') . '</pre>';
        }
        return $sReturn;
    }

    /**
     * Returns data from memory state.
     *
     * @return string
     */
    private function renderMemoryAlloc()
    {
        $sReturn  = 'Alloc:' . PHP_EOL;
        foreach( $this->_aMemory as $value )
        {
            $sColor = ($value['end']==0)?'red':'black';
            $sReturn .= '<pre style="color:' . $sColor . ';">[';
            $sReturn .= htmlentities( $value['name'], ENT_QUOTES, 'UTF-8') . '] => ';
            $sReturn .= 'Created at (' . $this->getElapsedTime(NULL,$value['start']) . 'ms) and ';
            if($value['end']!=0)
            {
                $sReturn .= 'deleted at ' . $this->getElapsedTime(NULL,$value['end']) . 'ms. ';
                $sReturn .= 'Live for ~' . $this->getElapsedTime($value['start'],$value['end']) . 'ms';
            }
            else
            {
                $sReturn .= 'never deleted';
            }
            $sReturn .= '</pre>';
        }
        return $sReturn;
    }

    /**
     * Creates memory allocation chart.
     *
     * @return string
     */
    private function renderMemoryAllocChart()
    {
        // Initialize
        $aValues = array();
        $bDone = FALSE;

        // Create data
        foreach( $this->_aMemory as $value )
        {
            $sBuffer = str_replace( 'Common\\Application\\','' , $value['name'] );
            $sBuffer = str_replace( 'Common\\Filter\\',''      , $sBuffer );
            $sBuffer = str_replace( 'Common\\Debug\\',''       , $sBuffer );
            $sBuffer = str_replace( 'Common\\Resource\\',''    , $sBuffer );
            $sBuffer = str_replace( 'Common\\Type\\',''        , $sBuffer );
            $fEnd = ($value['end']!=0) ? $this->getElapsedTime(NULL,$value['end']) : $this->getElapsedTime() . '*';
            $aValues[] = array( 'name'=>htmlentities( $sBuffer, ENT_QUOTES, 'UTF-8'),
                                'start'=>$this->getElapsedTime(NULL, $value['start']),
                                  'end'=>$fEnd );
        }

        // Create path
        $sPath = new \Common\Type\CPath( PROJECT_PATH . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'graph.html' );

        // Create graph
        try
        {
            $pChart = new \Common\Debug\CDebugGraph( $sPath );

            if( $pChart->open() )
            {
                $bDone = $pChart->write( $aValues );
            }
        }
        catch( Exception $e )
        {
            echo '<ul>',PHP_EOL;
            do
            {
                printf('<li>[%s : %d] %s (%s : %d)</li>', get_class($e), $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
            }
            while( $e = $e->getPrevious() );
            echo '</ul>', PHP_EOL;
        }

        if( $bDone===FALSE )
        {
            $sReturn = '<span style="color:red;">No Memory allocation Graph</span>';
        }
        else
        {
            $sReturn = '<a href="file://localhost/' . strtr( (string)$sPath, '\\', '/') . '" target="blank">Memory allocation Graph</a>';
        }
        unset( $pChart, $sPath );

        return $sReturn;
    }

    /**
     * Returns debug information in human readable format.
     *
     * @return string
     */
    public function render()
    {
        $sReturn  = '<div id="debug" style="clear:both;font-size:80%;">';
        $sReturn .= '<p><small>' . $this->renderUsage()               . '</small></p>'  . PHP_EOL;
        $sReturn .= '<p><small>' . $this->renderQuery($_POST, 'Post') . ' </small></p>' . PHP_EOL;
        $sReturn .= '<p><small>' . $this->renderQuery($_GET, 'Get')   . '</small></p>'  . PHP_EOL;
        $sReturn .= '<p><small>' . $this->renderCookie()              . '</small></p>'  . PHP_EOL;
        $sReturn .= '<p><small>' . $this->renderSession()             . '</small></p>'  . PHP_EOL;
        $sReturn .= '<p><small>' . $this->renderMemoryAlloc()         . '</small></p>'  . PHP_EOL;
        $sReturn .= '<p><small>' . $this->renderMemoryAllocChart()    . '</small></p>'  . PHP_EOL;
        $sReturn .= '</div>';
        return $sReturn;
    }

}
define( 'COMMON_DEBUG', 1 );
define( 'COMMON_DEBUG_MEMORY_ALLOC', 1 );
\Common\Debug\CDebug::getInstance();
