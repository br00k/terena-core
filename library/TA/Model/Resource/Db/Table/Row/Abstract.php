<?php

class TA_Model_Resource_Db_Table_Row_Abstract extends Zend_Db_Table_Row_Abstract implements TA_Model_Observed_Interface {

	/**
	 * Observer stack
	 *
	 * @var array
	 */
	protected static $_observers = array();

	protected static $_count = array();


	/**
	 * Return column/value pairs with date values
	 * transformed to Zend_Date objects
	 *
	 * @param string $dateFormat format to output the date to
	 * @return array
	 */
	public function toMagicArray($dateFormat = null)
	{
		$metadata = $this->getTable()->info('metadata');
		foreach ($metadata as $column => $metadata) {
			if ($metadata['DATA_TYPE'] == 'timestamptz') {
				if ($this->$column) {
					$this->$column = $this->_isoToNormalDate($this->$column, $dateFormat);
				}
			}
		}
		return $this->toArray();
	}

	/**
	 * Transforms DB timestamp to normal date
	 *
	 * @param string $value timestamp to transform
	 * @param string $dateFormat format to transform the timestamp to
	 *
	 * @return object Zend_Date
	 * @todo Made this a seperate method so I can call this from parent class
	 */
	protected function _isoToNormalDate($value, $dateFormat = null)
	{
		$zendDate = new Zend_Date(
		    $value,
		    Zend_Date::ISO_8601,
		    Zend_Registry::get('Zend_Locale') //@todo: it seems I can remove this, because it gets it automatically
		);
		if ($dateFormat) {
			return $zendDate->get($dateFormat);
		}

		return $zendDate;
	}

	/**
	 * Allow pre-insert logic to be applied to row
	 *
	 */
	 public function _insert()
	 {
	 	$this->notifyObservers(__FUNCTION__);
	 }

	/**
	 * Allows post-insert logic to be applied to row.
	 *
	 */
	public function _postInsert()
	{
		$this->notifyObservers(__FUNCTION__);
	}

	/**
	 * Allow pre-update logic to be applied to row
	 *
	 */
	 public function _update()
	 {
	 	$this->notifyObservers(__FUNCTION__);
	 }

	/**
	 * Allows post-update logic to be applied to row.
	 *
	 */
	public function _postUpdate()
	{
		$this->notifyObservers(__FUNCTION__);
	}

	/**
	 * Allow pre-delete logic to be applied to row
	 *
	 */
	 public function _delete()
	 {
	 	$this->notifyObservers(__FUNCTION__);
	 }

	/**
	 * Allows post-delete logic to be applied to row.
	 *
	 */
	public function _postDelete()
	{
		$this->notifyObservers(__FUNCTION__);
	}

	public static function getObservers()
	{
		$observers = self::$_observers;
		return $observers;
	}

	/**
	* Add a static Observer object to the class
	*
	* @param object $o Observer that implements the iObserver interface
	* @retunn void
	*/
	public static function attachStaticObserver(TA_Model_Observer_Interface $o)
	{
		array_push(self::$_observers, $o);
	}

	/**
	* Remove a static observer object from the class
	*
	* @param	object	$o	Observer that implements the iObserver interface
	* @return	boolean	True on success
	*/
	public static function detachStaticObserver(TA_Model_Observer_Interface $o)
	{
		foreach (self::$_observers as $key => $observer) {
			if ($observer == $o) {
				self::$_observers[$key] = null;
				return true;
			}
		}
		return false;
	}

	/**
	* Notify any observers
	* use this if something happens the observers might be interested in
	*
	* @param	string	$method		Method to call on observer
	* @param	string	$msg 		The message you want to send to the observer
	* @return 	void
	*/
	public function notifyObservers($method, $msg = null)
	{
		$observers = self::$_observers;

		// loop through observers and notify each available observer method
		foreach ($observers as $obs) {
			if (is_callable(array($obs, $method))) {
				#$ob = spl_object_hash($obs);
				#if (isset(self::$_count[$ob])) {
				#	self::$_count[$ob]++;
				#} else {
				#	self::$_count[$ob] = 1;
				#}
				$obs->$method($this, $msg);
			}
		}
	}

}

