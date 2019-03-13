<?php namespace Common\Filter;
/**
 * Patterns
 *
 * This file contains a class which provides a set of commonly needed
 * regex patterns.
 *
 * @package Common\Filter
 */
if( !defined('APPLICATION_VERSION') )
    die('-1');

/**
 * This class provides a set of commonly needed regex patterns.
 */
final class CPattern
{

    /**
     * Class unique ID
     * @var string
     * @todo DEBUG MEMORY DUMP. SHALL BE DELETED
     */
    private $_sDebugID = '';

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
     * Returns the html tag regex pattern
     * @return \Common\Type\CString
     */
    public function getHtmlTag()
    {
        return new \Common\Type\CString( '/^[a-z][a-z0-9' . preg_quote('_-','/') . ']*$/i' );
    }

    /**
     * Returns the session regex pattern
     * @return \Common\Type\CString
     */
    public function getSession()
    {
        return new \Common\Type\CString( '/^[a-z0-9]{32}$/i' );
    }

    /**
     * Returns the name regex pattern
     * @return \Common\Type\CString
     */
    public function getName()
    {
        return new \Common\Type\CString( '/^[\p{Nd}\p{L}' . preg_quote('._-','/') . ']+$/u' );
    }

    /**
     * Returns the directory regex pattern
     * @return \Common\Type\CString
     */
    public function getDirectory()
    {
        return new \Common\Type\CString( '/^[\p{Nd}\p{L}' . preg_quote('\/._-:','/') . ']+$/u' );
    }

    /**
     * Returns the email address regex pattern
     * @return \Common\Type\CString
     */
    public function getEmailAddress()
    {
        return new \Common\Type\CString( '/^(.+)@([^@]+)$/' );
    }

    /**
     * Returns the regex pattern for a non quoted local part of an email address.
     * Extended version of the RFC2822.
     * @return \Common\Type\CString
     */
    public function getEmailAddressLocalPartNoQuoted()
    {
        $sSubPattern = 'a-zA-Z0-9' . preg_quote('!#$%&\'*+-/=?^_`{|}~','/');
        return new \Common\Type\CString( '/^[' . $sSubPattern . ']+(\x2e+[' . $sSubPattern . ']+)*$/' );
    }

    /**
     * Returns the regex pattern for a quoted local part of an email address.
     * Extended version of the RFC2822.
     * @return \Common\Type\CString
     */
    public function getEmailAddressLocalPartQuoted()
    {
        $sSubPattern = '\sa-zA-Z0-9' . preg_quote('\!#$%&\'*+-/=?^_`{|}~[]@','/');
        return new \Common\Type\CString( '/^\x22[' . $sSubPattern . ']+(\x2e+[' . $sSubPattern . ']+)*\x22$/' );
    }

    /**
     * Returns the regex pattern for a domain part of an email address with TLD.
     * Unicode extended version of the RFC2822.
     *
     * @return \Common\Type\CString
     */
    public function getEmailAddressDomainPartTld()
    {
        $sSubPatternStart = '\p{L}\p{Nd}' . preg_quote('-','/');
        $sSubPatternMiddle = $sSubPatternStart . preg_quote('.','/');
        $sSubPatternEnd = preg_quote('.','/') . '[\p{L}][\p{L}]+';
        return new \Common\Type\CString( '/^[' . $sSubPatternStart . ']+[' . $sSubPatternMiddle . ']*' . $sSubPatternEnd . '$/u' );
    }

    /**
     * Returns the regex pattern for a domain part of an email address without TLD.
     * Unicode extended version of the RFC2822.
     *
     * @return \Common\Type\CString
     */
    public function getEmailAddressDomainPartNoTld()
    {
        $sSubPattern = '\p{L}\p{Nd}' . preg_quote('-','/');
        return new \Common\Type\CString( '/^[' . $sSubPattern . ']+$/u' );
    }

}
