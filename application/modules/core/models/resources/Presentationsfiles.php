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
 * @revision   $Id: Presentationsfiles.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Presentationsfiles extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'presentations_files';

	protected $_primary = 'presentation_file_id';

	public function init() {}

	/**
	 * Gets item by primary key
	 * @return object Zend_Db_Table_Row
	 */
	public function getItemById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * Gets linked files
	 *
	 * @param	integer		$id		presentation_id
	 * @param	boolean		$allData	Returns array with current core_filetype => file_id pairs
	 * @return	Array		array of file_id's
	 */
	public function getFilesByPresentationId($id, $allData = false)
	{
		if ($allData) {
			return $this->getAdapter()->fetchPairs(
				"select f.core_filetype, f.file_id from presentations_files pf
				left join vw_files f on (pf.file_id = f.file_id) where presentation_id=".$id
			);	
		}
		
		return $this->getAdapter()->fetchCol(
		   "select file_id from presentations_files where presentation_id=:presentation_id",
		   array(
		   	'presentation_id' => $id,
		   )
		);
	}

	/**
	 * returns item based on id values
	 *
	 * @param	array	$data	Presentation_id and File_id values
	 * @return	object	Zend_Db_Table_Row
	 */
	public function getItemByValues(array $data)
	{
		return $this->fetchRow(
					$this->select()
					->where('presentation_id = ?', $data['presentation_id'])
					->where('file_id = ?', $data['file_id'])
				);
	}

	/**
	 * Save rows to the database. (insert or update)
	 *
	 * @param array $values
	 * @return	boolean
	 */
	public function saveRows($values)
	{

		$presentationId = (int) $values['presentation_id'];
		if ($presentationId === 0 ) {
			throw new TA_Model_Resource_Db_Table_Exception('presentation_id not present');
		}

		$db = $this->getAdapter();

		// array with current core_filetype => file_id pairs
		$currentValues = $db->fetchPairs(
			"select f.core_filetype, f.file_id from presentations_files pf
			left join vw_files f on (pf.file_id = f.file_id) where presentation_id=".$presentationId
		);
		// @todo: test this:
		// $currentValues = $this->getFilesByPresentationId($presentationId, true);

		// loop through successfully uploaded files
		foreach ($values['file_id'] as $fileType => $fileId) {

			$value['presentation_id'] = $presentationId;
			$value['file_id'] = $fileId;

			// Update if core_filetype already has a file associated with it
			if ( isset($currentValues[$fileType]) ) {
				// do update
				$query = "UPDATE " . $this->_name . " SET
				file_id=:file_id, presentation_id=:presentation_id
				WHERE file_id=:file_id_old";
				$value['file_id_old'] = $currentValues[$fileType];
			} else {
				// do insert
				$query = "INSERT INTO " . $this->_name . "(file_id, presentation_id)
				VALUES (:file_id, :presentation_id)";
			}

			$db->query($query, $value);
		}

		return true;
	}


}