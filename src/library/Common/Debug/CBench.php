<?php namespace Common\Debug;
/**
 * Benchmark class.
 *
 * This file contains a class which implements benchmarks methods
 *
 * @package Common\Debug
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class implements benchmarks methods.
 * @codeCoverageIgnore
 */
class CBench
{

    /** Constants */
    const DEFAULT_MAXTEST = 1000;

    /** protected variables
     **********************/

    /**
     * Name of the email address variable
     * @var \Common\Type\CString
     */
    protected $_aTimes = array();

    /** Private methods
     ******************/

    /**
     * Initialize data
     */
    final protected function compute( $fTimeStart, $fTimeStop)
    {
        if( is_float($fTimeStart) && is_float($fTimeStop) )
        {
            $fDiff = $fTimeStop - $fTimeStart;
            $this->_aTimes['all'][] = round($fDiff*1000);
            if( is_null($this->_aTimes['min']) ) $this->_aTimes['min'] = $fDiff;
            if( $fDiff>$this->_aTimes['max'] ) $this->_aTimes['max'] = $fDiff;
            if( $fDiff<$this->_aTimes['min'] ) $this->_aTimes['min'] = $fDiff;
            $this->_aTimes['sum'] += $fDiff;
        }//if( is_float(...
    }

    /**
     * Render the results
     */
    final protected function render()
    {
        $sContent  = '<ul>' . $this->_aTimes['name'] . PHP_EOL;
        if( $this->_aTimes['count']>0 )
        {
            $this->_aTimes['moy'] = $this->_aTimes['sum']/$this->_aTimes['count'];
            $fDiff = $this->_aTimes['end'] - $this->_aTimes['start'];
            $sContent .= '<li>Bench <spam>' . round( $fDiff *1000 ) .' ms</spam></li>' . PHP_EOL;
            $sContent .= '<li>Total <spam>' . round( $this->_aTimes['sum']  *1000 ) .' ms</spam></li>' . PHP_EOL;
            $sContent .= '<li>Min <spam>'   . round( $this->_aTimes['min']  *1000 ) .' ms</spam></li>' . PHP_EOL;
            $sContent .= '<li>Max <spam>'   . round( $this->_aTimes['max']  *1000 ) .' ms</spam></li>' . PHP_EOL;
            $sContent .= '<li>Avg <spam>'   . round( $this->_aTimes['moy']  *1000 ) .' ms</spam></li>' . PHP_EOL;
//            $sContent .= '<pre>' . print_r($this->_aTimes['all'],TRUE) .'</pre>' . PHP_EOL;
        }
        $sContent .= '</ul>' . PHP_EOL;
        return $sContent;
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
    * Constructor
    */
    public function __construct()
    {
        $this->init();
    }

    /**
    * Initialize data
    */
    protected function init()
    {
        $this->_aTimes = array(
            'name' => '',                       // Name of the bench
           'count' => self::DEFAULT_MAXTEST,    // Test count
           'start' => 0.0,                      // Bench start time
            'all' => array(),                   // Elapse time of all test
            'sum' => 0.0,                       // Sum of each elapse times
            'min' => NULL,                      // Min elapse time
            'max' => 0.0,                       // Max elapse time
            'end' => 0.0,                       // Bench end time
            'moy' => 0.0 );                     // Average of elapse time
    }

    /**
    * Gets the class methods' names.
    *
    * @return array
    */
    public function getMethods()
    {
        return array('email','fcall');
    }

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

    /**
     * Bench 001.
     *
     * @param  string $sEmail Email to test
     * @param  integer $iTest OPTIONAL. Number of tests
     * @return string
     */
     public function bench001( $sEmail, $iTest=NULL  )
     {
        $this->init();
        $this->_aTimes['name'] = __METHOD__;
        if( is_int($iTest) )
        {
            $this->_aTimes['count'] = $iTest;
        }//if( is_int(...

        $this->_aTimes['start'] = microtime(true);
        for( $iIndex=0; $iIndex<$this->_aTimes['count']; $iIndex++)
        {
            $fTimeStart = microtime(true);
            $pValidator = new \Zend_Validate_EmailAddress();
            if( !$pValidator->isValid($sEmail) )
            {
                var_dump('bench001 error');
                break;
            }
            unset($pValidator);
            $fTimeStop = microtime(true);
            $this->compute($fTimeStart, $fTimeStop);
        }//for...
        $this->_aTimes['end'] = microtime(true);
        return $this->render();
     }

    /**
     * Bench 002.
     *
     * @param  string $sEmail Email to test
     * @param  integer $iTest OPTIONAL. Number of tests
     * @return string
     */
     public function bench002( $sEmail, $iTest=NULL  )
     {
        $this->init();
        $this->_aTimes['name'] = __METHOD__;
        if( is_int($iTest) )
        {
            $this->_aTimes['count'] = $iTest;
        }//if( is_int(...

        $this->_aTimes['start'] = microtime(true);
        for( $iIndex=0; $iIndex<$this->_aTimes['count']; $iIndex++)
        {
            $fTimeStart = microtime(true);
            $pValidator = new \Common\Type\CEmailAddress($sEmail);
            if( $pValidator->isValid() )
            {
                var_dump('bench002 error');
                break;
            }
            unset($pValidator);
            $fTimeStop = microtime(true);
            $this->compute($fTimeStart, $fTimeStop);
        }//for...
        $this->_aTimes['end'] = microtime(true);
        return $this->render();
     }

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
     private $_bench003_1 = 1;
     private $_bench003_2 = null;

     private function bench003_004()
     {
        $bReturn = FALSE;
        if( ($this->_bench003_1===1) && isset($this->_bench003_2) )
        {
            $bReturn=TRUE;
        }
        return $bReturn;
     }

    /**
     * Bench 003.
     *
     * @param  integer $iTest OPTIONAL. Number of tests
     * @return string
     */
     public function bench003( $iTest=NULL  )
     {
        $this->init();
        $this->_aTimes['name'] = __METHOD__;
        if( is_int($iTest) )
        {
            $this->_aTimes['count'] = $iTest;
        }//if( is_int(...

        $this->_aTimes['start'] = microtime(true);
        for( $iIndex=0; $iIndex<$this->_aTimes['count']; $iIndex++)
        {
            $fTimeStart = microtime(true);

            if( ($this->_bench003_1===1) && isset($this->_bench003_2) )
            {
                $bReturn=TRUE;
            }

            $fTimeStop = microtime(true);
            $this->compute($fTimeStart, $fTimeStop);
        }//for...
        $this->_aTimes['end'] = microtime(true);
        return $this->render();
     }

    /**
     * Bench 003.
     *
     * @param  integer $iTest OPTIONAL. Number of tests
     * @return string
     */
     public function bench004( $iTest=NULL  )
     {
        $this->init();
        $this->_aTimes['name'] = __METHOD__;
        if( is_int($iTest) )
        {
            $this->_aTimes['count'] = $iTest;
        }//if( is_int(...

        $this->_aTimes['start'] = microtime(true);
        for( $iIndex=0; $iIndex<$this->_aTimes['count']; $iIndex++)
        {
            $fTimeStart = microtime(true);

            $bReturn = $this->bench003_004();

            $fTimeStop = microtime(true);
            $this->compute($fTimeStart, $fTimeStop);
        }//for...
        $this->_aTimes['end'] = microtime(true);
        return $this->render();
     }

}
