<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Admin\Adapter\AuthAdapter;
use Admin\Model\AdminAuth;
use Admin\Model\AdminAuthTable;
use Application\Model\BookTable;
use Application\Model\GenreTable;
use Application\Model\AuthorTable;

class RootController extends AbstractActionController
{
    private $adminAuthTable;
    private $authAdapter;
    private $adminAuthService;
    private $adminAuthStorage;
    private $bookTable;
    private $genreTable;
    private $authorTable;
    private $admin;
    private $config;
    private $lastUrl;

    /**
     * @return AdminAuthTable
     */
    public function getAdminAuthTable()
    {
        if (!$this->adminAuthTable) {
            $sm = $this->getServiceLocator();
            $this->adminAuthTable = $sm->get('Admin\Model\AdminAuthTable');
        }
        return $this->adminAuthTable;
    }

    /**
     * @return BookTable
     */
    public function getBookTable()
    {
        if (!$this->bookTable) {
            $sm = $this->getServiceLocator();
            $this->bookTable = $sm->get('Application\Model\BookTable');
        }
        return $this->bookTable;
    }

    /**
     * @return GenreTable
     */
    public function getGenreTable()
    {
        if (!$this->genreTable) {
            $sm = $this->getServiceLocator();
            $this->genreTable = $sm->get('Application\Model\GenreTable');
        }
        return $this->genreTable;
    }

    /**
     * @return AuthorTable
     */
    public function getAuthorTable()
    {
        if (!$this->authorTable) {
            $sm = $this->getServiceLocator();
            $this->authorTable = $sm->get('Application\Model\AuthorTable');
        }
        return $this->authorTable;
    }

    /**
     * @return AuthAdapter
     */
    public function getAuthAdapter()
    {
        if (!$this->authAdapter) {
            $adapter = new AuthAdapter();
            $this->authAdapter = $adapter->setAuthTable($this->getAdminAuthTable());
        }
        return $this->authAdapter;
    }

    public function getAdminAuthService()
    {
        if (!$this->adminAuthService) {
            $this->adminAuthService = $this->getServiceLocator()
                ->get('AdminAuthService');
        }

        return $this->adminAuthService;
    }

    /**
     * @return AdminAuthStorage
     */
    public function getAdminSessionStorage()
    {
        if (!$this->adminAuthStorage) {
            $this->adminAuthStorage = $this->getServiceLocator()->get('Admin\Model\AdminAuthStorage');
        }
        return $this->adminAuthStorage;
    }

    public function getAdmin()
    {
        if (!$this->admin) {
            if (($this->admin = $this->getServiceLocator()->get('AdminAuthService')->getStorage()->read()) &&
                is_a($this->admin, "Admin\Model\AdminAuth")
            ) {
            } else {
                $this->admin = new AdminAuth();
            }
        }

        return $this->admin;
    }

    public function getConfig() {
        if(!$this->config) {
            $config = $this->getServiceLocator()->get('Config');
            $this->config = (object) $config;
        }
        return $this->config;
    }

    public function getLastUrl() {
        if(!$this->lastUrl) {
            $session = new Container('url');
            $this->lastUrl = $session->lastUrl;
            $session->lastUrl = false;
        }
        return $this->lastUrl;
    }

    public function setLastUrl() {
        $session = new Container('url');
        $session->lastUrl = $this->getRequest()->getRequestUri();
    }

    public function getErrorMsgZendFormat($msg)
    {
        return array(
            "error_msg" => array(
                $msg,
            ),
        );
    }
}
