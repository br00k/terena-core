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
 * @revision   $Id: Item.php 598 2011-09-15 20:55:32Z visser $
 */
/**
	* This class represents a single record
	* Methods in this class could include logic for a single record
	* for example sticking first_name and last_name together in a getName() method
	*/
class Core_Resource_Submission_Item extends TA_Model_Resource_Db_Table_Row_Abstract
{

	/**
	 * Get reviewers belonging to this submission
	 *
	 * @return array
	 */
	public function getReviewers()
	{
		$query = "select u.email, rs.reviewer_submission_id as id from reviewers_submissions rs
		left join users u on (rs.user_id = u.user_id)
		where rs.submission_id=:submission_id";

		return $this->getTable()->getAdapter()->query(
			$query, array(':submission_id' => $this->submission_id)
		)->fetchAll();
	}
	
	public function getSubmissionOneliner()
	{
		return $this->title;
	}
}