<?php
namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Book implements InputFilterAwareInterface
{
    protected $inputFilter;
    protected $book_id;
    protected $book_title;
    protected $book_description;
    protected $book_author_id;
    protected $book_image;
    protected $book_pdf;
    protected $book_status = 'pending';
    protected $book_created_at;
    protected $genres = array(); // Array of book's genres
    protected $author; // Author of book

    public function __construct(array $data = array())
    {
        $this->exchangeArray($data);
    }

    public function exchangeArray(array $data)
    {
        foreach ($data as $k => $v) {
            if (property_exists($this, $k)) {
                $this->__set($k, $v);
            }
        }
    }

    public function __set($key, $val)
    {
        $this->$key = $val;
    }

    public function getArrayCopy()
    {
        $arr = array(
            'book_title' => $this->getTitle(),
            'book_description' => $this->getDescription(),
            'book_author_id' => $this->getAuthorId(),
            'book_image' => $this->getImage(),
            'book_pdf' => $this->getPdf(),
            'book_status' => $this->getStatus(),
            'book_created_at' => $this->getCreatedAt(),
        );
        if ($this->getId()) {
            $arr['book_id'] = $this->getId();
        }

        return $arr;
    }

    public function getArrayCopyForInsert()
    {
        $arr = $this->getArrayCopy();
        if (array_key_exists("book_id", $arr)) unset($arr['book_id']);
        if (array_key_exists("book_created_at", $arr)) unset($arr['book_created_at']);

        return $arr;
    }

    public function getId()
    {
        return $this->book_id;
    }

    public function setId($book_id)
    {
        $this->book_id = $book_id;
        return $this->book_id;
    }

    public function getTitle()
    {
        return $this->book_title;
    }

    public function setTitle($book_title)
    {
        $this->book_title = $book_title;
        return $this;
    }

    public function getDescription()
    {
        return $this->book_description;
    }

    public function setDescription($book_description)
    {
        $this->book_description = $book_description;
        return $this;
    }

    public function getAuthorId()
    {
        return $this->book_author_id;
    }

    public function setAuthorId($book_author_id)
    {
        $this->book_author_id = $book_author_id;
        return $this;
    }

    public function getImage()
    {
        return $this->book_image;
    }

    public function setImage($book_image)
    {
        $this->book_image = $book_image;
        return $this;
    }

    public function getPdf()
    {
        return $this->book_pdf;
    }

    public function setPdf($book_pdf)
    {
        $this->book_pdf = $book_pdf;
        return $this;
    }

    public function getStatus()
    {
        return $this->book_status;
    }

    public function setStatus($book_status)
    {
        $this->book_status = $book_status;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->book_created_at;
    }

    public function setCreatedAt($book_created_at)
    {
        $this->book_created_at = $book_created_at;
        return $this;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        throw new \Exception("Not used");
    }

    public function getInputFilterBookSave($authorsIds)
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name' => 'book_title',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => "Title is required",
                            ),
                        ),
                    ),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name' => 'book_description',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => "Description is required",
                            ),
                        ),
                    ),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name' => 'book_status',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => "Status is required",
                            ),
                        ),
                    ),
                    array(
                        'name' => 'InArray',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\InArray::NOT_IN_ARRAY => "Status is required",
                            ),
                            'haystack' => array(
                                'active',
                                'inactive',
                                'pending',
                            )
                        ),
                    ),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name' => 'book_author_id',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => "Author is required",
                            ),
                        ),
                    ),
                    array(
                        'name' => 'InArray',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\InArray::NOT_IN_ARRAY => "Author is required",
                            ),
                            'haystack' => $authorsIds
                        ),
                    ),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name' => 'book_image',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name' => 'book_pdf',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name' => 'book_genre_ids',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => "Genre is required",
                            ),
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function setAuthor(Author $author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    public function addGenre(Genre $genre)
    {
        if (!$this->hasGenre($genre)) {
            $this->genres[] = $genre;
        }
        return $this;
    }

    public function getGenres()
    {
        return count($this->genres) ? $this->genres : false;
    }

    public function removeGenre(Genre $genre)
    {
        if ($this->hasGenre($genre)) {
            $this->genres = array_diff($this->genres, array($genre));
        }
        return $this;
    }

    public function hasGenre(Genre $genre)
    {
        if (in_array($genre, $this->genres)) {
            return true;
        }
        return false;
    }
}