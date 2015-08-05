<?php

class Core_Service_Authentication {

	protected $_userModel;

	protected $_auth;

	protected $_type;

	protected $_invite;

	/**
	 * Constructor, loads user model
	 *
	 * @param	string	$inviteHash		Invite hash
	 * @return void
	 */
	public function __construct($invite = null)
	{
		$this->_userModel = new Core_Model_User();
		$this->_invite = $invite;
	}

	public function authenticate($values)
	{
		$adapter = $this->_getAuthAdapter($values);

		$auth = $this->getAuth();

		$result = $auth->authenticate($adapter);

		if ($result->isValid()) {
			// persistent storage
			$storage = $auth->getStorage();

			// set custom user attributes
			if ( !$user = $this->_userModel->getUserBySmartId( $result->getIdentity() ) ) {
				if ($this->_invite) {
					// update user
					$user = $this->_userModel->saveUserFromFederatedIdentity(
						$result->getIdentityAttributes(),
						$this->_invite
					);
				} else {
					// insert user
					$user = $this->_userModel->saveUserFromFederatedIdentity( $result->getIdentityAttributes() );
				}
			}

			$user->updateAttributes();

			if ($this->_getAuthType() === 'federated') {
				// do some stuff with the federated attributes you can get with: $result->getIdentityAttributes()
			}

			$storage->write($user);

			return true;
		} else {
			return $result->getMessages();
		}

	}

	public function getAuth()
	{
		if (!$this->_auth) {
			return Zend_Auth::getInstance();
		}
	}

	protected function _getAuthType()
	{
		return $this->_type;
	}

	/**
	 * Get different authentication adapaters based on parameter
	 *
	 * @return Zend_Auth_Adapter_Interface
	 */
	protected function _getAuthAdapter($values)
	{
		if ( isset($values['authsource']) ) {
			$this->_type = 'federated';
			return $this->_getAuthAdapterFederated($values);
		}
		if ( isset($values['password']) ) {
			$this->_type = 'advanced';
			return $this->_getAuthAdapterAdvanced($values);
		} elseif ( isset($values['organisation']) ) {
			$this->_type = 'basic';
			return $this->_getAuthAdapterBasic($values);
		}
		throw new Exception('No valid authentication adapter found');
	}

	protected function _getAuthAdapterFederated($values)
	{
		return new TA_Auth_Adapter_Federated($values['authsource']);
	}

	protected function _getAuthAdapterBasic($values)
	{
		$adapter = new Zend_Auth_Adapter_DbTable(
			Zend_Db_Table_Abstract::getDefaultAdapter(),
			'users',
	    	'email',
	    	'organisation'
		);

		$adapter->setIdentity($values['email'])
				->setCredential($values['organisation'])
				->setCredentialTreatment("lower(?)");

		// only select active users
		$select = $adapter->getDbSelect();
		$select->where('active = true');
		// only select users who can *not* login with password. They would need the advanced adapater
		$select->where('password IS NULL');

		return $adapter;
	}

	protected function _getAuthAdapterAdvanced($values)
	{
		$adapter = new Zend_Auth_Adapter_DbTable(
			Zend_Db_Table_Abstract::getDefaultAdapter(),
			'users',
	    	'email',
	    	'password'
		);

		// salt with static *and* dynamic salt. Dynamic salt is stored in database and static salt in config
		$adapter->setIdentity($values['email'])
				->setCredential($values['password'])
				// concatentate strings in the right order
				->setCredentialTreatment( "md5('". Zend_Registry::get('config')->_staticSalt . "' || ? || password_salt)" );

    	// only select active users
		$select = $adapter->getDbSelect();
		$select->where('active = true');

		return $adapter;
	}

}
