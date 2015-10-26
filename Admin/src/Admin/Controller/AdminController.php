<?php
namespace Admin\Controller;

use Admin\Model\AdminAuth;
use Application\Controller\RootController;
use Application\Model\Post;
use Application\Model\Banner;
use Application\Model\Media;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;

class AdminController extends RootController
{
    public function indexAction()
    {
        if (!$this->getAdmin()->getId()) {
            $this->setLastUrl();
            return $this->redirect()->toRoute("admin", array("action" => "login"));
        }

        $paginator = $this->getBookTable()->fetchAll(true);
        $currentPage = (int) $this->params()->fromQuery('page', 1);
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage(10);
        return array(
            'activeTab' => 'books',
            'paginator' => $paginator,
            'currentPage' => $currentPage
        );
    }

    public function addBookAction()
    {
        if (!$this->getAdmin()->getId()) {
            $this->setLastUrl();
            return $this->redirect()->toRoute("admin", array("action" => "login"));
        }

        $genres = $this->getGenreTable()->fetchAll();
        $authors = $this->getAuthorTable()->fetchAll();

        return array(
            'activeTab' => "books",
            'genres' => $genres,
            'authors' => $authors,
        );
    }

    public function addAuthorAction()
    {
        if (!$this->getAdmin()->getId()) {
            $this->setLastUrl();
            return $this->redirect()->toRoute("admin", array("action" => "login"));
        }

        return array(
            'activeTab' => "authors",
        );
    }

    public function addGenreAction()
    {
        if (!$this->getAdmin()->getId()) {
            $this->setLastUrl();
            return $this->redirect()->toRoute("admin", array("action" => "login"));
        }

        return array(
            'activeTab' => "genres",
        );
    }

    public function editBookAction()
    {
        if (!$this->getAdmin()->getId()) {
            $this->setLastUrl();
            return $this->redirect()->toRoute("admin", array("action" => "login"));
        }

        if (!($id = $this->params()->fromRoute('id')) || !($book = $this->getBookTable()->find($id))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $genres = $this->getGenreTable()->fetchAll();
        $authors = $this->getAuthorTable()->fetchAll();
        return array(
            'activeTab' => "books",
            'book' => $book,
            'genres' => $genres,
            'authors' => $authors,
        );
    }

    public function editAuthorAction()
    {
        if (!$this->getAdmin()->getId()) {
            $this->setLastUrl();
            return $this->redirect()->toRoute("admin", array("action" => "login"));
        }

        if (!($id = $this->params()->fromRoute('id')) || !($author = $this->getAuthorTable()->find($id))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return array(
            'activeTab' => "authors",
            'author' => $author,
        );
    }

    public function editGenreAction()
    {
        if (!$this->getAdmin()->getId()) {
            $this->setLastUrl();
            return $this->redirect()->toRoute("admin", array("action" => "login"));
        }

        if (!($id = $this->params()->fromRoute('id')) || !($genre = $this->getGenreTable()->find($id))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return array(
            'activeTab' => "genres",
            'genre' => $genre,
        );
    }

    public function authorsAction()
    {
        if (!$this->getAdmin()->getId()) {
            $this->setLastUrl();
            return $this->redirect()->toRoute("admin", array("action" => "login"));
        }

        $paginator = $this->getAuthorTable()->fetchAll(true);
        $currentPage = (int) $this->params()->fromQuery('page', 1);
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage(10);

        return array(
            'activeTab' => 'authors',
            'paginator' => $paginator,
            'currentPage' => $currentPage
        );
    }

    public function genresAction()
    {
        if (!$this->getAdmin()->getId()) {
            $this->setLastUrl();
            return $this->redirect()->toRoute("admin", array("action" => "login"));
        }

        $paginator = $this->getGenreTable()->fetchAll(true);
        $currentPage = (int) $this->params()->fromQuery('page', 1);
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage(10);

        return array(
            'activeTab' => 'genres',
            'paginator' => $paginator,
            'currentPage' => $currentPage
        );
    }

    public function loginAction()
    {
        $form = new \Admin\Form\AdminAuthLoginForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $adapter = $this->getAuthAdapter()
                    ->setUsername($request->getPost('username'))
                    ->setPassword($request->getPost('password'));

                $result = $this->getAdminAuthService()->authenticate($adapter);
                foreach ($result->getMessages() as $message) {
                    $this->flashmessenger()->addMessage($message);
                }

                if ($result->isValid()) {
                    $client = $result->getIdentity();

                    $this->getAdminSessionStorage()->setRememberMe(1);
                    $this->getAdminAuthService()->setStorage($this->getAdminSessionStorage());
                    $this->getAdminAuthService()->getStorage()->write($client);

                    if ($this->getLastUrl()) {
                        return $this->redirect()->toUrl($this->getLastUrl());
                    }
                    return $this->redirect()->toUrl('/admin');
                }
            }
        }
        return array(
            'activeTab' => "login",
        );
    }

    public function logoutAction()
    {
        if (!$this->getAdmin()->getId()) {
            return $this->redirect()->toRoute("admin", array("action" => "login"));
        }

        $this->getAdminSessionStorage()->forgetMe();
        $this->getAdminAuthService()->clearIdentity();

        return $this->redirect()->toRoute("admin", array("action" => "login"));
    }

}