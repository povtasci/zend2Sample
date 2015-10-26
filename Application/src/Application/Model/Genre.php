<?php
namespace Application\Model;

class Genre
{
    protected $inputFilter;
    protected $genre_id;
    protected $genre_name;

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
            'genre_name' => $this->getName(),
        );
        if ($this->getId()) {
            $arr['genre_id'] = $this->getId();
        }

        return $arr;
    }

    public function getArrayCopyForInsert()
    {
        $arr = $this->getArrayCopy();
        unset($arr['genre_id']);

        return $arr;
    }

    public function getId()
    {
        return $this->genre_id;
    }

    public function setId($genre_id)
    {
        $this->genre_id = $genre_id;
        return $this->genre_id;
    }

    public function getName()
    {
        return $this->genre_name;
    }

    public function setName($genre_name)
    {
        $this->genre_name = $genre_name;
        return $this;
    }
}