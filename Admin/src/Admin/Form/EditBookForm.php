<?php
namespace Admin\Form;

class EditBookForm extends AddBookForm
{
    public function __construct($name=null)
    {
        parent::__construct('add-book');

        $this->add(array(
            'name' => 'book_id',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
    }
}