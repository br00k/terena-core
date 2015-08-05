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
 * @revision   $Id: Reviewerssubmissions.php 41 2011-11-30 11:06:22Z gijtenbeek@terena.org $
 */

/**
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Reviewerssubmissions extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'reviewers_submissions';

	protected $_primary = 'reviewer_submission_id';

	protected $_rowClass = 'Core_Resource_Review_Submission_Item';

	public function init()
	{
		#$this->attachObserver(new Core_Model_Observer_Reviewsubmission());
	}

	/**
	 * Gets item by primary key
	 * @return object Zend_Db_Table_Row
	 */
	public function getItemById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * returns item based on id values
	 *
	 * @param	array	$data	Submission_id and User_id values
	 * @return	object	Zend_Db_Table_Row
	 */
	public function getItemByValues(array $data)
	{
		return $this->fetchRow(
					$this->select()
					->where('submission_id = ?', $data['submission_id'])
					->where('user_id = ?', $data['user_id'])
				);
	}

	/**
	 * Gets item by user id
	 *
	 * @param	integer		$id 	user id value
	 * @return	Zend_Db_Table_Row
	 */
	public function getItemByUserId($id)
	{
		return $this->fetchRow(
					$this->select()
					->where('user_id = ?', $id)
				);
	}

	/**
	 * Get all reviewers of papers submitted for current conference
	 *
	 * @todo: rename this to getReviewers()
	 * @return array
	 */
	public function getAllReviewers()
	{
		$query = "select u.fname, u.lname, u.email, u.user_id, rs.submission_id, rs.tiebreaker
		from " .$this->_name. " rs
		left join users u on (rs.user_id = u.user_id)
        left join submissions s on (rs.submission_id = s.submission_id)
        where s.conference_id=".$this->getConferenceId();
        return $this->getAdapter()->fetchAll($query);
	}

}