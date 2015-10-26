<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hrayr
 * Date: 2/26/13
 * Time: 6:07 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Admin\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;

class AuthAdapter implements AdapterInterface
{
    private $authTable;
    private $username;
    private $password;

    public function setAuthTable($authTable) {
        $this->authTable = $authTable;
        return $this;
    }

    public function getAuthTable() {
        return $this->authTable;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    public function authenticate()
    {
        return $this->getAuthTable()->authenticate($this->getUsername(), $this->getPassword());
    }
}