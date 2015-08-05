<?php
class Core_Form_Review extends TA_Form_Abstract
{
	public function init()
	{
	    $this->setAction('/core/review/new');

	    $this->addElement('hidden', 'submission_id', array(
			'required' => true,
			'decorators' => $this->_hiddenElementDecorator
	    ));

	    $suitability = new Zend_Form_Element_Select('suitability_conf');
	    $suitability->setLabel('Suitability Conference')
	   		 		->setAttrib('class', 'medium')
					->setMultiOptions($this->_getFieldValues('suitability_conf', 'review'))
					->setDecorators(array('Composite'));

	    $quality = new Zend_Form_Element_Select('quality');
	    $quality->setLabel('Quality')
	   		 	->setAttrib('class', 'medium')
				->setMultiOptions($this->_getFieldValues('quality', 'review'))
				->setDecorators(array('Composite'));

	    $importance = new Zend_Form_Element_Select('importance');
	    $importance->setLabel('Importance')
	   		 	   ->setAttrib('class', 'medium')
				   ->setMultiOptions($this->_getFieldValues('importance', 'review'))
				   ->setDecorators(array('Composite'));

	    $rating = new Zend_Form_Element_Select('rating');
	    $rating->setLabel('Overall recommendation')
	   		   ->setAttrib('class', 'medium')
			   ->setMultiOptions($this->_getFieldValues('rating', 'review'))
			   ->setDecorators(array('Composite'));


	    $assess = new Zend_Form_Element_Select('self_assessment');
	    $assess->setLabel('Self assessment reviewer')
	   		   ->setAttrib('class', 'medium')
			   ->setMultiOptions($this->_getFieldValues('self_assessment', 'review'))
			   ->setDecorators(array('Composite'));

	    $comPresentation = new Zend_Form_Element_Textarea('comments_presentation');
	    $comPresentation->setLabel('Comments on the language / presentation skills')
	    				->setAttrib('class', 'small')
	    				->setDescription('Assess the potential presentation quality based upon the language used in the abstract and prior knowledge of the speaker')
	    				->setRequired(false)
						->setDecorators(array('Composite'));

	    $comPc = new Zend_Form_Element_Textarea('comments_pc');
	    $comPc->setLabel('Comments to the PC')
	    	  ->setAttrib('class', 'small')
	    	  ->setDescription('These comments will be read only by the programme committee')
	    	  ->setRequired(false)
			  ->setDecorators(array('Composite'));

	    $comAuthors = new Zend_Form_Element_Textarea('comments_authors');
	    $comAuthors->setLabel('Comments to the authors')
	    		   ->setAttrib('class', 'small')
	    		   ->setDescription('These comments may be edited and will be sent to the authors')
	    		   ->setRequired(false)
				   ->setDecorators(array('Composite'));

		$this->addElements(array(
			$suitability,
			$quality,
			$importance,
			$rating,
			$assess,
			$comPresentation,
			$comPc,
			$comAuthors
		));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));
	}

}