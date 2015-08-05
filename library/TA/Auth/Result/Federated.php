<?php
class TA_Auth_Result_Federated extends Zend_Auth_Result
{

	/**
	 * Attributes of current user
	 *
	 * @var array
	 */
	private $_attributes = array();

    /**
     * Sets the result code, identity, and failure messages
     *
     * @param  int     $code
     * @param  mixed   $identity
     * @param  array   $messages
     * @return void
     */
    public function __construct($code, $identity, array $messages = array())
    {
        parent::__construct($code, $identity, $messages);
    }

	/**
	 * The identity is the attribute value of 'saml_uid_attribute'
	 * see application.ini
	 *
	 * @return	saml_uid_attribute
	 */
    public function getIdentity()
    {
    	$config = Zend_Registry::get('config');
    	$samlUidAttribute = $config->simplesaml->saml_uid_attribute;
    	
        $this->_attributes = parent::getIdentity();
        $mail = $this->_attributes[$samlUidAttribute];

        $log = Zend_Registry::get('log');
        $log->emerg(var_export($this->_attributes, true));
        return $this->_attributes[$samlUidAttribute];
    }

	/**
	 * Retrieve the attributes of the current user.
	 * If the user isn't authenticated, an empty array will be returned.
	 *
	 * @return array
	 */
    public function getIdentityAttributes()
    {
    	return $this->_attributes;
    }

	/**
	 * Get IdP from attributes
	 *
	 */
    public function getIdp()
    {
		if (preg_match("/^(facebook|twitter|windowslive|myspace|linkedin)_targetedID:(.*)\!(.*)$/", $this->_attributes['smart_id'], $matches)) {
			return $matches[2];
		} elseif (preg_match('/.*!(.*)/', $this->_attributes['smart_id'], $matches)) {
			return $matches[1];
		}
	}

}
