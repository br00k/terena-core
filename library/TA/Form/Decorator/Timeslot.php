<?php
/**
 * CORE Conference Manager
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.terena.org/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to webmaster@terena.org so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2011 TERENA (http://www.terena.org)
 * @license    http://www.terena.org/license/new-bsd     New BSD License
 * @revision   $Id: Timeslot.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

/**
 * Custom Composite Form Element decorator
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 * @package TA_Form
 * @subpackage Decorator
 */
class TA_Form_Decorator_Timeslot extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this->getElement();

        if (!$element instanceof TA_Form_Element_Timeslot) {
            // only want to render Timeslot elements
            return $content;
        }

        $view = $element->getView();
        if (!$view instanceof Zend_View_Interface) {
            // using view helpers, so do nothing if no view present
            return $content;
        }

		// get default values
		$start = $element->getStart();
		$end = $element->getEnd();
		$number = $element->getNumber();
		$type = $element->getType();
        $name  = $element->getFullyQualifiedName();
        $cls = $element->getAttrib('class');

        $params = array(
            'class' => ''
        );

        // @todo replace this by db call
        $options = array(
        	1 => 'presentation',
        	2 => 'coffee break',
        	3 => 'lunch'
        );

        $markup = '<li class="timeslot '. $cls.'">'
        		. '<span>' .$view->formText($name . '[tstart]', $start, $params)
                . '<label>From (dd/mm/yyyy h:m)</label></span><span>' .$view->formText($name . '[tend]', $end, $params)
                . '<label>To (dd/mm/yyyy h:m)</label></span><span>' .$view->formText($name . '[number]', $number, array('class'=>'tiny'))
                . '<label>Number</label></span><span>' .$view->formSelect($name . '[type]', $type, null,  $options)
                . '</span><span><a href="#" class="delete">delete</a></span></li>';

        switch ($this->getPlacement()) {
            case self::PREPEND:
                return $markup . $this->getSeparator() . $content;
            case self::APPEND:
            default:
                return $content . $this->getSeparator() . $markup;
        }
    }
}