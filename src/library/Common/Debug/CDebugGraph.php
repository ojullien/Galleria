<?php namespace Common\Debug;
/**
 * Debug graph
 *
 * This file contains a class which implements tools for drawing google chart
 *
 * @package Common\Debug
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class implements usefull functions for memory display usage
 * @codeCoverageIgnore
 */
final class CDebugGraph extends \Common\File\CResourceAbstract
{
    /** Private variables
     ********************/

    /**
     * Head of the page
     * @var string
     */
    private $_sHead =
'<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<title>DebugGraph</title>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">google.load(\'visualization\', \'1\', {packages: [\'corechart\']});</script>
<script type="text/javascript">function drawVisualization(){var data=google.visualization.arrayToDataTable([';

    /**
     * Footer of the file
     * @var string
     */
    private $_sFooter =
'], true);var options={legend:\'none\'};var chart=new google.visualization.CandlestickChart(document.getElementById(\'chart_div\'));chart.draw(data, options);}google.setOnLoadCallback(drawVisualization);</script>
</head>
<body><div id="chart_div" style="width: 900px; height: 500px;"></div></body></html>';

    /** Private methods
     ******************/

    /** Protected methods
     ********************/

    /**
     * Manages directory.
     * Returns TRUE on success or FALSE on failure.
     *
     * @return boolean
     * @throws \InvalidArgumentException If the name of the directory is not valid
     */
    protected function manageDirectory()
    {
        return TRUE;
    }

    /**
     * Manages file.
     * Returns TRUE on success or FALSE on failure.
     *
     * @return boolean
     */
    protected function manageFile()
    {
        $this->_pFileOpened = $this->openFile('w+b');
        return TRUE;
    }

    /** Public methods
     *****************/

    /**
    * Write to file.
    * Returns the number of bytes written, or FALSE on error.
    *
    * @param array $aValues the data be written to the file.
    * @return integer|boolean
    */
    public function write( array $aValues )
    {
        $iWritten = NULL;
        if( isset($this->_pFileOpened) && (count($aValues)>0) )
        {
            $sBuffer = $this->_sHead . PHP_EOL;
            $sData = '';
            foreach( $aValues as $value )
            {
                if( !empty($sData) )
                {
                    $sData = ',';
                }
                $sData .= "['" . $value['name'] ."'";
                if( strpos( $value['end'], '*' )===FALSE )
                {
                    $sData .= "," . $value['start'] . "," . $value['start']
                            . "," . $value['end']   . "," . $value['end'] . "]";
                }
                else
                {
                    $value['end'] = doubleval($value['end']);
                    $sData .= "," . $value['end'] . "," . $value['end']
                            . "," . $value['start']   . "," . $value['start'] . "]";
                }
                $sBuffer .= $sData;
            }
            $sBuffer .= PHP_EOL . $this->_sFooter;
            $iWritten = $this->_pFileOpened->fwrite( $sBuffer );
        }//if( isset(...
        if( is_null($iWritten) )
        {
            $iWritten = FALSE;
        }
        else
        {
            $this->_pFileOpened->fflush();
        }
        return $iWritten;
    }

}
