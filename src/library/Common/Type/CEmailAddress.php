<?php namespace Common\Type;
/**
 * Email Address type class
 *
 * This file contains a class which enforces strong typing of the email address type.
 *
 * @package Common\Type
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class provides an email address filter.
 */
final class CEmailAddress extends \Common\Type\CTypeAbstract
{
     /** Constants */
    const DEFAULT_TAG = 'emailInput';

    /** Private variables
     ********************/

    /**
     * Raw
     * @var string
     */
    private $_sRaw = '';

    /**
     * Local part of the email address
     * @var \Common\Type\CString
     */
    private $_sPartLocal = self::DEFAULT_VALUE;

    /**
     * Domain part of the email address
     * @var \Common\Type\CString
     */
    private $_sPartDomain = self::DEFAULT_VALUE;

    /**
     * Tag. Usefull for Html form
     *
     * @var \Common\Type\CString
     */
    private $_sTag = self::DEFAULT_VALUE;

    /** Private methods
     ******************/

    /**
     * Writes data to local part variable.
     * Return TRUE if the value is valid.
     *
     * @param string $value
     * @param \Common\Filter\CPattern $pPatterns
     * @return boolean
     */
    private function setLocalPart( $value, \Common\Filter\CPattern $pPatterns )
    {
        // Initialize
        $this->_sPartLocal = self::DEFAULT_VALUE;
        $pValue = new \Common\Type\CString( $value );
        $sNeedle = new \Common\Type\CString( '..' );
        $bReturn = FALSE;

        // Validate
        if( ($pValue->getLength()<65) && !$pValue->contains($sNeedle) )
        {
            // Non Quoted
            $sPattern = $pPatterns->getEmailAddressLocalPartNoQuoted();
            $bReturn = $pValue->matches($sPattern);
            unset($sPattern);
            // Quoted
            if( $bReturn===FALSE )
            {
                $sPattern = $pPatterns->getEmailAddressLocalPartQuoted();
                $bReturn = $pValue->matches($sPattern);
            }
            unset( $sPattern );
        }
        unset($sNeedle);

        // Save
        if( $bReturn===TRUE )
        {
            // Local part is valid
            $this->_sPartLocal = $pValue;
        }
        else
        {
            // Local part is not valid
            unset($pValue);
        }//if(...

        return $bReturn;
    }

    /**
     * Writes data to domain part variable.
     * Return TRUE if the value is valid.
     *
     * @param string $value
     * @param \Common\Filter\CPattern $pPatterns
     * @return boolean
     */
    private function setDomainPart( $value, \Common\Filter\CPattern $pPatterns )
    {
        // Initialize
        $this->_sPartDomain = self::DEFAULT_VALUE;
        $pValue = new \Common\Type\CString( $value );
        $sNeedle = new \Common\Type\CString( '..' );
        $bReturn = FALSE;

        // Validate
        if( ($pValue->getLength()<256) && !$pValue->contains($sNeedle) )
        {
            // With TLD
            $sPattern = $pPatterns->getEmailAddressDomainPartTld();
            $bReturn = $pValue->matches($sPattern);
            unset( $sPattern );
        }
        unset($sNeedle);

        // Save
        if( $bReturn===TRUE )
        {
            // Domain part is valid
            $this->_sPartDomain = $pValue;
        }
        else
        {
            // Domain part is not valid
            unset($pValue);
        }//if(...

        return $bReturn;
    }

    /** Public methods
     *****************/

    /**
     * Destructor
     *
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __destruct()
    {
        unset( $this->_sPartDomain, $this->_sPartLocal, $this->_sTag );
        parent::__destruct();
    }

    /**
     * Constructor
     *
     * @param mixed $value
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    public function __construct($value)
    {
        //@codeCoverageIgnoreStart
        if( defined('COMMON_DEBUG_MEMORY_ALLOC') )
        {
            $this->_sDebugID = uniqid(rand());
            \Common\Debug\CDebug::getInstance()->addMemoryNew( $this->_sDebugID, __CLASS__, $value );
        }//if( defined(...
        //@codeCoverageIgnoreEnd
        $this->setHtmlTag( self::DEFAULT_TAG );
        $this->setValue($value);
    }

    /**
     * Writes data to variables.
     *
     * @param string $value
     * @return mixed
     */
    public function setValue($value)
    {
        // Initialize
        $bReturn = FALSE;
        $this->_Value = self::DEFAULT_VALUE;
        $this->_sRaw = '';
        $this->_sPartLocal = self::DEFAULT_VALUE;
        $this->_sPartDomain = self::DEFAULT_VALUE;
        if( $value instanceof \Common\Type\CTypeAbstract )
        {
            $value = $value->getValue();
        }
        $pValue = new \Common\Type\CString( $value );

        // Raw value
        if( is_scalar($value) )
        {
            $this->_sRaw = trim($value);
        }//if( is_scalar(...

        // Split and validate
        if( $pValue->getLength()<256 )
        {
            // Load patterns
            $pPatterns = new \Common\Filter\CPattern();

            // Split
            $aMatches = array();
            $sPattern = $pPatterns->getEmailAddress();
            $bReturn = $pValue->matches($sPattern, $aMatches);
            unset( $sPattern );

            // Validate local part
            if( $bReturn && is_array($aMatches) && isset($aMatches[1]) )
                $bReturn = $this->setLocalPart( $aMatches[1], $pPatterns );

            // Validate domain part
            if( $bReturn && is_array($aMatches) && isset($aMatches[2]) )
                $bReturn = $this->setDomainPart( $aMatches[2], $pPatterns );
            unset( $pPatterns );
        }//if( $pValue->isValid() && ($pValue->getLength()<256) )
        unset($pValue);
        return $value;
    }

    /**
     * Determines if the variables are set and are not NULL.
     * Returns TRUE if the variables have values other than NULL, FALSE otherwise.
     *
     * @return boolean
     */
    public function isValid()
    {
        return ( isset($this->_sPartLocal) && isset($this->_sPartDomain) &&
                 $this->_sPartLocal->isValid() && $this->_sPartDomain->isValid() );
    }

    /**
     * Reads data from variable.
     *
     * @return string|NULL
     */
    public function getValue()
    {
        $sReturn = self::DEFAULT_VALUE;
        if( isset($this->_sPartLocal) && isset($this->_sPartDomain) &&
                 $this->_sPartLocal->isValid() && $this->_sPartDomain->isValid() )
        {
            $sReturn = $this->_sPartLocal->getValue();
            $sReturn .= '@';
            $sReturn .= $this->_sPartDomain->getValue();
        }//if(...
        return $sReturn;
    }

    /**
     * Converts to string
     *
     * @return string
     */
    public function __toString()
    {
        $sReturn = '';
        if( isset($this->_sPartLocal) && isset($this->_sPartDomain) )
        {
            $sReturn = (string)$this->_sPartLocal;
            $sReturn .= '@';
            $sReturn .= (string)$this->_sPartDomain;
        }//if(...
        return $sReturn;
    }

    /**
     * Returns raw value of the email address
     *
     * @return string
     */
    public function getRaw()
    {
        return (string)$this->_sRaw;
    }

    /**
     * Writes tag to variable.
     * Returns FALSE if the tag value is not valid.
     *
     * @param string
     * @return boolean
     */
    public function setHtmlTag( $value )
    {
        $this->_sTag = self::DEFAULT_VALUE;
        $pPatterns = new \Common\Filter\CPattern();
        $sPattern = $pPatterns->getHtmlTag();
        $this->_sTag = new \Common\Type\CString( $value, $sPattern );
        $bReturn = $this->_sTag->isValid();
        if( !$bReturn )
        {
            $this->_sTag->setValue(self::DEFAULT_TAG);
        }
        unset($sPattern,$pPatterns);
        return $bReturn;
    }

    /**
     * Returns html tag value
     *
     * @return \Common\Type\CString
     */
    public function getHtmlTag()
    {
        return $this->_sTag;
    }

}
