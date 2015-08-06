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
 * @revision   $Id: Edit.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

/** 
 *
 * @package Core_Forms
 * @subpackage Core_Forms_Review
 */
class Core_Form_Review_Edit extends Core_Form_Review
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/review/edit');

		$reviewId = new Zend_Form_Element_Hidden('review_id');
		$reviewId->setRequired(true)
				 ->setLabel('review_id')
				 ->addValidators(
					array('Int')
				 )
				 ->setDecorators(array('Composite'));
	    
		$this->addElement($reviewId);    
	    
	}


}