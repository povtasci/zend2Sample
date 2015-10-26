<?php
namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class AdminAuth implements InputFilterAwareInterface
{
    protected $admin_id;
    protected $username;
    protected $password;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        foreach($data as $k => $v) {
            if(property_exists($this,$k)) {
                switch ($k) {
                    case 'admin_id':
                        $this->setId($v);
                        break;
                    case 'username':
                        $this->setUsername($v);
                        break;
                    case 'password':
                        $this->setPassword($v);
                        break;
                }
            }
        }
    }

    public function getArrayCopy()
    {
        $arr = array(
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
        );
        if($this->getId()) {
            $arr['admin_id'] = $this->getId();
        }
        return $arr;
    }

    public function getArrayCopyForInsert()
    {
        $arr = array(
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
        );
        return $arr;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

        }
        return $this->inputFilter;
    }

    public function getId()
    {
        return $this->admin_id;
    }

    public function setId($id)
    {
        $this->admin_id = $id;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($name)
    {
        $this->username = $name;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

}