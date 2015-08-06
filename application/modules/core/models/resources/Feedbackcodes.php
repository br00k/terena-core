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
 * @revision   $Id: Feedbackcodes.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Feedbackcodes extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'feedback.codes';

	protected $_primary = 'code_id';

	public function init() { }

	/**
	 * Gets feedback by UUID
	 *
	 * @return	object	Zend_Db_Table_Row
	 */
	public function getFeedbackByUuid($uuid)
	{
		return $this->fetchRow($this->select()
			->where("uuid = ?", $uuid)
		);
	}

	/*
	 * This will create a number ($i) of feedbackcodes
	 *
	 * @param	integer 	$i			Number of codes to create
	 * @param	boolean		$delete		Delete all entries before inserting
	 * @return	array		Associative array feedback id => feedback code
	 */
	public function createFeedbackCodes($i, $delete = false)
	{
		$uuid = new TA_Uuid();

		if ($delete) {
			$this->delete('1=1'); // uh?
		}

		$return = array();

		for ($j = 0; $j < $i; $j++) {
			$code = $uuid->get();
			$id = $this->insert(array('uuid' => $code));
			$return[$id] = $code;
		}

		return $return;
	}
}