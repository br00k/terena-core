<?php
class Core_Form_Presentation extends TA_Form_Abstract
{

	public function init()
	{
	    $this->setAction('/core/presentation/new');

	    $title = new Zend_Form_Element_Text('title');
	    $title->setLabel('Title')
			  ->setRequired(true)
			  #->addValidator('regex', true, array(
			  #    'pattern' => '/^[a-zA-Z0-9\s]{2,100}$/',
			  #    'messages' => array(Zend_Validate_Regex::NOT_MATCH => 'Wrong format')
			  #))
			  ->addValidator(new Zend_Validate_Db_NoRecordExists(array(
	    		'table' => 'presentations',
	    	 	'field' => 'title'
	    	 )))
			  ->setAttrib('class', 'medium')
			  ->setDescription('Must be between 2 and 100 characters, only letters, numbers and spaces allowed')
			  ->setDecorators(array('Composite'));

	    $abstract = new Zend_Form_Element_Textarea('abstract');
	    $abstract->setLabel('Abstract')
			  	 ->setRequired(true)
				 ->setAttrib('class', 'medium')
				 ->setDecorators(array('Composite'));
				 
	    $authors = new Zend_Form_Element_Textarea('authors');
	    $authors->setLabel('Authors')
				->setAttrib('class', 'medium')
				->addFilter('Null')
				->addFilter('StringTrim')
				->setDescription('You can add multiple authors by putting each author on a new line')
				->setDecorators(array('Composite'));				 

		$submissionModel = new Core_Model_Submit();

	    $submission = new Zend_Form_Element_Select('submission_id');
	    $submission->setLabel('Submission')
				   ->addMultiOption('', '---')
				   ->addFilter('Null') // add this if you want to provide a blank value
				   ->addMultiOptions($submissionModel->getSubmissionsForSelect(null))
				   ->setAttrib('class', 'medium')
				   ->setDecorators(array('Composite'));
				   
	    $logo = new Zend_Form_Element_Select('image');
	    $logo->setLabel('Logo')
			 ->addMultiOption('', '---')
			 ->addFilter('Null') // add this if you want to provide a blank value
			 ->addMultiOptions(array('geant' => 'geant'))
			 ->setAttrib('class', 'medium')
			 ->setDecorators(array('Composite'));
				   
		$this->addElements(array(
			$title,
			$abstract,
			$authors
		));
		
		// @todo: by no means secure, you can still manually change the POST array 
		// to bypass this
		if (Zend_Auth::getInstance()->getIdentity()->isAdmin()) {
			$this->addElements(array($submission, $logo));			
		}

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));
	}

}