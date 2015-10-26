<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element\Textarea;
use Zend\Form\Element\Select;

class AddBookForm extends Form
{
    public function __construct($name=null)
    {
        parent::__construct('add-book');
        $this->setAttribute('method','post');

        $this->add(array(
            'name' => 'book_title',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));

        $this->add(array(
            'name' => 'book_description',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));

        $textarea = new Textarea('book_description');
        $textarea->setLabel('Short Description');

        $this->add($textarea);

        $this->add(array(
            'name' => 'book_image',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'book_pdf',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'book_status',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $select = new Select('book_status');
        $select->setLabel('Status');
        $select->setOptions(array(
            'options' => array(
                'active' => 'Active',
                'inactive' => 'Inactive',
                'pending' => 'Pending',
            )
        ));

        $this->add($select);

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
            ),
        ));

    }
}