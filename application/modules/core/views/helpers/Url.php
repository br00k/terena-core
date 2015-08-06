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
	 * @revision   $Id: Url.php 25 2011-10-04 20:46:05Z visser@terena.org $
	 */

/**
 * Override the standard Url View helper
 *
 * If the controller is not set in the $urlOptions then it is taken
 * from the request object and added to the $urlOptions array
 *
 * This is very useful for my _grid.phtml partial view
 * @package Core_View_Helper
 */
class Core_View_Helper_Url extends Zend_View_Helper_Url
{
	public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
	{

		if (!isset($urlOptions['controller'])) {
			$urlOptions['controller'] = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		}

		return parent::url($urlOptions, $name, $reset, $encode);
	}
}
