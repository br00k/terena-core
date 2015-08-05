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
 * @revision   $Id: Events.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Events extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'events';

	protected $_primary = 'event_id';

	protected $_rowClass = 'Core_Resource_Event_Item';
	
	public function init() {}

	/**
	 * Gets deadline by primary key
	 * @return object Zend_Db_Table_Row
	 */
	public function getEventById($id)
	{
		return $this->find( (int)$id )->current();
	}

}