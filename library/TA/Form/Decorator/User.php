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
 * @revision   $Id: User.php 41 2011-11-30 11:06:22Z gijtenbeek@terena.org $
 */

/**
 * Custom User form element decorator
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 * @package TA_Form
 * @subpackage Decorator
 */
class TA_Form_Decorator_User extends Zend_Form_Decorator_Abstract
{

	/**
	 * view helper
	 */
	private $_view;

    public function render($content)
    {

        $element = $this->getElement();

        if (!$element instanceof TA_Form_Element_User) {
            return $content;
        }

		$this->_view = $view = $element->getView();
        if (!$view instanceof Zend_View_Interface) {
            // using view helpers, so do nothing if no view present
            return $content;
        }

        $output = null;

		if ( $linkedUsers = $element->getTaRow()->getUsers() ) {

			if (!$linkedUsers instanceof Core_Resource_User_Set) {
				throw new TA_Exception('Row element does not return Zend_Db_Table_Rowset');
			}
			
			// mapping of user_id to linked table primary key values
			$linkedIds = $element->getTaRow()->getManyToManyIds();

			$output = '<li><table class="grid" cellspacing="0"><tbody>';
			foreach ($linkedUsers as $linkedUser) {
				$custom = ($this->getOption('showCustomTaAction')) ?
					' | '. $this->getCustomTaAction($linkedUser, $linkedIds)
					: '';

				$output .= '<tr class="'. $view->cycle(array('odd', 'even'))->next() .'">'
				.'<td>'.$linkedUser->getFullName().'</td>'
				.'<td>'.$linkedUser->organisation.'</td>'
				.'<td style="text-align:right">'.$this->_getDeleteLink($linkedIds[$linkedUser->user_id])
				. $custom
				.'</td>'
				.'</tr>';
			}

			$output .= '</tbody></table></li>';
		}

		$placement = $this->getPlacement();
		$separator = $this->getSeparator();

		switch ($placement) {
		    case 'PREPEND':
		        return $output . $separator . $content;
		    case 'APPEND':
		    default:
		         return $content . $separator . $output;
		}

    }

	/**
	 * Helper method to build hyperlink to delete method
	 *
	 */
	private function _getDeleteLink($id)
	{
		return '<a title="remove user" href="'
    	    	.$this->_view->url(array(
		    		'controller' => $this->getElement()->getTaController(),
		    		'action' => 'deleteuserlink',
		    		'id' => $id,
		    	), 'main-module') .'">delete</a>';

	}

}