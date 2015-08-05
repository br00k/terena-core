<?php

class Core_UserController extends Zend_Controller_Action implements Zend_Acl_Resource_Interface
{

	private $_userModel;

	public function init()
	{
		$this->_userModel = new Core_Model_User();
		$this->view->Stylesheet('advform.css');
		$this->view->messages = $this->_helper->flashMessenger->getMessages();

		// Set navigation to active for all actions within this controller
		$page = $this->view->navigation()->findOneByController( $this->getRequest()->getControllerName() );
		if ($page) {
			$page->setActive();
		}

		//$this->_helper->cache(array('speakers'), array('speakersaction'));
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function getResourceId()
	{
		return 'User';
	}

	private function getLoginForm()
	{
		return $this->_userModel->getForm('login');
	}

	private function displayForm()
	{
		$this->view->userForm = $this->_userModel->getForm('user');
		return $this->render('formUserAdd');
	}


	public function indexAction()
	{
		return $this->_forward('list');
	}

	public function listAction()
	{
		$this->view->headScript()->appendFile('/js/jquery-ui/js/jquery-ui.min.js');
		$this->view->headLink()->appendStylesheet('/js/jquery-ui/css/ui-lightness/jquery-ui.css');
		$this->view->headScript()->appendFile('/js/users.js');

		$this->view->grid = $this->_userModel->getUsers(
			$this->_getParam('page', 1),
			array($this->_getParam('order', null), $this->_getParam('dir', 'asc'))
		);

		$this->view->grid['params']['order'] = $this->_getParam('order');
		$this->view->grid['params']['dir'] = $this->_getParam('dir');
		$this->view->grid['params']['controller'] = $this->getRequest()->getControllerName();
	}

	public function speakerAction()
	{
    	$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$cache = $bootstrap->getResource('cachemanager')
						   ->getCache('apc');

		$this->view->stylesheet('schedule.css');
		$this->view->threeColumnLayout = true;

		#if( ($this->view->grid = $cache->load('speakerlist') === false ) ) {
			$this->view->grid = $this->_userModel->getUsersWithRole(
				null,
				array($this->_getParam('order', null), $this->_getParam('dir', 'asc')),
				'presenter'
			);
			#$cache->save($this->view->grid, 'speakerlist');
		#}
	}

	/**
	 * @todo: Add 'last login' date to success message
	 */
	public function loginAction()
	{
    	$auth = new Core_Service_Authentication( $this->getRequest()->getParam('id', null) );

		// @todo authsource should be configurable
		$authresult = $auth->authenticate(array('authsource'=>'default-sp'));

		if ($authresult  === true) {
			$this->_helper->flashMessenger('Successful login');
			$this->_redirect('/');
		} else {
		   // failed login
		   return $this->render('login');
		}

	}

	/**
	 * Show one presentation based on presentation_id
	 *
	 */
	public function showAction()
	{
		$request = $this->getRequest();

		$this->view->user_id = $id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('user_id');

		$this->view->user = $user = $this->_userModel->getUserById($id);

		$this->view->sessions = $user->getSessions();
		$this->view->presentations = $user->getPresentations();

		$this->_helper->actionStack('speaker');
		return $this->render('show');
	}


	public function editAction()
	{
		$request = $this->getRequest();

		$this->view->id = $request->getParam('id', $request->getParam('user_id'));
		// No post; display form
		if ( !$request->isPost() )  {
			$this->view->userForm = $this->_userModel->getForm('userEdit');
			// populate form with defaults
			$this->view->userForm->setDefaults(
				$userDefaults = $this->_userModel->getUserById(
					$this->getRequest()->getParam('id', Zend_Auth::getInstance()->getIdentity()->user_id)
				)->toArray()
			);

			// if user has a picture, add it to the MagicFile form element
			if (isset($userDefaults['file_id'])) {
				$fileModel = new Core_Model_File();
				$this->view->userForm->file->setTaFile(
					$fileModel->getFileById($userDefaults['file_id'])
				);
			}

			return $this->render('formUserEdit');
		}

		// try to save user to database
		if ( $this->_userModel->saveUser($request->getPost(), 'edit') === false ) {
			$this->view->userForm = $this->_userModel->getForm('userEdit');
			return $this->render('formUserEdit');
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Successfully edited record');
		return $this->_helper->lastRequest();
	}

	public function deleteAction()
	{
		if ( false === $this->_userModel->delete($this->_getParam('id')) ) {
			throw new Core_Model_Exception('Something went wrong with deleting the user');
		}
		return $this->_helper->redirector->gotoRoute(array('controller'=>'user', 'action'=>'list'), 'grid');
	}

	/**
	 * Show/save roles linked to this user
	 *
	 */
	public function rolesAction()
	{
		$request = $this->getRequest();
		$id = $this->view->id = $request->getParam('id', $request->getParam('user_id'));

		// No post; display form
		if ( !$request->isPost() )  {
			$form = $this->view->userRoleForm = $this->_userModel->getForm('userRole');
			$this->view->roles = $this->_userModel->getUserById($id)->getRoles();
			$form->setDefaults(array(
			   	'user_id' => $id
			));

			return $this->render('roles');
		}

		// persist user/presentation mapping
		if ( $this->_userModel->saveRoles($request->getPost()) === false ) {
			$this->_helper->lastRequest();
		}

		// everything went OK
		return $this->_helper->lastRequest();
	}

	/**
	 * Remove role from user
	 */
	public function deleteroleAction()
	{
		$this->_userModel->deleteRole($this->_getParam('id'));
		return $this->_helper->lastRequest();
	}

	/**
	 * Add a new account, used when inviting users
	 *
	 */
	public function newAction()
	{
		$request = $this->getRequest();

		// No post; display form
		if ( !$request->isPost() )  {
			$this->view->userForm = $this->_userModel->getForm('userInvite');
			$this->view->userForm->setDefaults(
				$request->getParams()
			);
			return $this->render('formUserAdd');
		}

		// try to persist user
		if ( $this->_userModel->saveUser($request->getPost()) === false ) {
			$this->view->userForm = $this->_userModel->getForm('userInvite');
			return $this->render('formUserAdd');
		}

		// send email to invitee
		$post = $request->getPost();
		Zend_Controller_Action_HelperBroker::addHelper(new TA_Controller_Action_Helper_SendEmail());
		$emailHelper = $this->_helper->sendEmail;

		$template = $this->_getTemplate($request->getPost('role_id'));

		$conference = Zend_Registry::get('conference');
		$emailHelper->sendEmail(array(
		    'template' => $template,
		    'subject' => 'Activate your CORE account',
			'html' => true,
		    'to_email' => $post['email'],
			'to_name' => $post['fname'].' '.$post['lname']
		), $post);

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Successfully added new record and send invitation to '.$post['email']);
		return $this->_helper->redirector->gotoRoute(array('controller'=>'user', 'action'=>'list'), 'grid');
	}

	/**
	 * @todo whoa is this really still needed??
	 *
	 */
	private function _getTemplate($roleId)
	{
		if (!$roleId) {
			$roleId = 2;
		}
		return 'user/invite_role_id_'.$roleId;
	}

	public function logoutAction()
	{
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
	}

}
