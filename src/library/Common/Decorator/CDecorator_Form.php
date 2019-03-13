<?php namespace Common\Decorator;

class CDecorator_Form extends Zend_Form_Decorator_Abstract
{
    protected $m_sFormat = '<form id="%s" name="%s"
accept-charset="%s" action="%s">';

    public function render($content)
    {
        $element = $this->getElement();
        $id='coucou';
        $name='hibou';
        $acceptcharset='UTF-8';
        $action=$element->getAction();
        $markup = sprintf($this->m_sFormat, $id, $name, $acceptcharset, $action);
        $placement = $this->getPlacement();
        $separator = $this->getSeparator();
        switch ($placement)
        {
            case self::PREPEND:
                return $markup . $separator . $content . '</form>';
            case self::APPEND:
            default:
                return $content . $separator . $markup . '</form>';

        }
    }
}