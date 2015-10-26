<?php
namespace Application\Model;

class Author
{
    protected $inputFilter;
    protected $author_id;
    protected $author_name;

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
            'author_name' => $this->getName(),
        );
        if ($this->getId()) {
            $arr['author_id'] = $this->getId();
        }

        return $arr;
    }

    public function getArrayCopyForInsert()
    {
        $arr = $this->getArrayCopy();
        unset($arr['author_id']);

        return $arr;
    }

    public function getId()
    {
        return $this->author_id;
    }

    public function setId($author_id)
    {
        $this->author_id = $author_id;
        return $this->author_id;
    }

    public function getName()
    {
        return $this->author_name;
    }

    public function setName($author_name)
    {
        $this->author_name = $author_name;
        return $this;
    }
}