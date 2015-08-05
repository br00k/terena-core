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
 * @revision   $Id: Feedbackparticipant.php 72 2012-06-12 12:49:55Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Feedbackparticipant extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'feedback.participant';

	protected $_primary = 'id';

	protected $_rowClass = 'Core_Resource_Feedback_Item';

	public function init() {}

	/**
	 * Gets feedback by feedback id
	 * @return object Zend_Db_Table_Row
	 */
	public function getFeedbackById($id)
	{
		return $this->fetchRow($this->select()
			->where("id = ?", $id)
		);
	}

	/**
	 * Gets list of general
	 *
	 * @return array
	 */
	public function getFeedbackparticipant()
	{
		$select = $this->select();

		$rowset = $this->fetchAll($select)->toArray();
		$return = array();

		foreach ($rowset as $key => $row) {
			if (!isset($headers)) {
				$headers = array_keys($row);
			}
			foreach ($row as $column => $value) {
				$data = @unserialize($value);
				if ($value === 'b:0;' || $data !== false) {
				    $return[$key][$column] = implode('|', $data);
				} else {
					$return[$key][$column] = $value;
				}
			}
		}

		array_unshift($return, $headers);
		return $return;
	}	
}