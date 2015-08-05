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
 * @revision   $Id: User.php 35 2011-10-13 13:56:04Z gijtenbeek@terena.org $
 */

/**
 *
 * @package Core_Forms
 * @subpackage Core_Forms_Session
 */
class Core_Form_Session_User extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->setAction('/core/session/chairs');

	    $submissionId = new Zend_Form_Element_Hidden('session_id');
	    $submissionId->setRequired(true)
	    			 ->addValidators(
	    			 	array('Int')
	    			 )
	    			 ->setDecorators(array('Composite'));

		$users = new TA_Form_Element_User('user_id');
		$users->setTaController('session')
			  ->populateElement('chair')
			  ->setAttrib('onchange', "this.form.submit()");

	    $this->addElements(array(
	    	$submissionId,
	    	$users
	    ));

	}

	public function setDefaults(array $values)
	{
		parent::setDefaults($values);
	}

}