<?php
namespace Admin\Controller;

use Admin\Form\AddBookForm;
use Admin\Form\EditBookForm;
use Application\Controller\RootController;
use Application\Model\Post;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Form\Element\Select;

class AdminActionController extends RootController
{
    public function saveBookAction()
    {
        if ($this->getAdmin()->getId()) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost()->toArray();
                if ($id = intval($this->params()->fromRoute("id", false))) {
                    $form = new EditBookForm();
                } else {
                    $form = new AddBookForm();
                }

                // get authors form DB
                $authors = array();
                foreach ($this->getAuthorTable()->fetchAll() as $author) {
                    $authors[$author->getId()] = $author->getName();
                }
                $select = new Select('book_author_id');
                $select->setOptions(array(
                    'options' => $authors
                ));
                $form->add($select);

                // get genres form DB
                $genres = array();
                foreach ($this->getGenreTable()->fetchAll() as $genre) {
                    $genres[$genre->getId()] = $genre->getName();
                }
                $select = new Select('book_genre_ids');
                $select->setAttribute("multiple", "multiple");
                $select->setOptions(array(
                    'options' => $genres,
                ));
                $form->add($select);
                if ($id) {
                    $book = $this->getBookTable()->find($id);
                } else {
                    $book = $this->getBookTable()->createNew();
                }
                $form->setInputFilter($book->getInputFilterBookSave(array_keys($authors)));
                $form->setData($data);
                if ($form->isValid()) {
                    $book->exchangeArray($data);
                    $book = $this->getBookTable()->save($book);
                    $result = array(
                        "redirectTo" => $this->url()->fromRoute("admin", array("action" => "edit-book", "id" => $book->getId()))
                    );
                    if ($id) {
                        $this->getBookTable()->removeGenresFromBook($book->getId());
                        $result = false;
                    }
                    foreach ($data['book_genre_ids'] as $genreId) {
                        $this->getBookTable()->addGenreToBook($genreId, $book->getId());
                    }
                    return new JsonModel(array(
                        "returnCode" => 101,
                        "result" => $result,
                        "msg" => "Book Has Been Saved.",
                    ));
                } else {
                    return new JsonModel(array("returnCode" => 202, "msg" => $form->getMessages()));
                }
            }
            return new JsonModel(array("returnCode" => 201, "msg" => "Wrong request."));
        } else {
            return new JsonModel(array("returnCode" => 201, "msg" => $this->getErrorMsgZendFormat("Your are logged in")));
        }
    }

    public function deleteBookAction()
    {
        if ($this->getAdmin()->getId()) {
            $book = $this->getBookTable()->find(intval($this->params()->fromRoute("id")));
            $this->getBookTable()->delete($book);
            return new JsonModel(array("returnCode" => 101, "msg" => "Book has been removed"));
        } else {
            return new JsonModel(array("returnCode" => 201, "msg" => $this->getErrorMsgZendFormat("Your are not logged in")));
        }
    }

    public function saveAuthorAction()
    {
        if ($this->getAdmin()->getId()) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost()->toArray();
                $id = intval($this->params()->fromRoute("id", false));
                if (!($name = $request->getPost("author_name", false))) {
                    return new JsonModel(array("returnCode" => 202, "msg" => $this->getErrorMsgZendFormat('Name is required')));
                }
                if ($id) {
                    $author = $this->getAuthorTable()->find($id);
                } else {
                    $author = $this->getAuthorTable()->createNew();
                }

                $author->exchangeArray($data);
                $author = $this->getAuthorTable()->save($author);
                $result = array(
                    "redirectTo" => $this->url()->fromRoute("admin", array("action" => "edit-author", "id" => $author->getId()))
                );
                return new JsonModel(array(
                    "returnCode" => 101,
                    "result" => $result,
                    "msg" => "Author Has Been Saved.",
                ));
            }
            return new JsonModel(array("returnCode" => 201, "msg" => "Wrong request."));
        } else {
            return new JsonModel(array("returnCode" => 201, "msg" => $this->getErrorMsgZendFormat("Your are logged in")));
        }
    }

    public function saveGenreAction()
    {
        if ($this->getAdmin()->getId()) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost()->toArray();
                $id = intval($this->params()->fromRoute("id", false));
                if (!($name = $request->getPost("genre_name", false))) {
                    return new JsonModel(array("returnCode" => 202, "msg" => $this->getErrorMsgZendFormat('Name is required')));
                }
                if ($id) {
                    $genre = $this->getGenreTable()->find($id);
                } else {
                    $genre = $this->getGenreTable()->createNew();
                }

                $genre->exchangeArray($data);
                $genre = $this->getGenreTable()->save($genre);
                $result = array(
                    "redirectTo" => $this->url()->fromRoute("admin", array("action" => "edit-genre", "id" => $genre->getId()))
                );
                return new JsonModel(array(
                    "returnCode" => 101,
                    "result" => $result,
                    "msg" => "Genre Has Been Saved.",
                ));
            }
            return new JsonModel(array("returnCode" => 201, "msg" => "Wrong request."));
        } else {
            return new JsonModel(array("returnCode" => 201, "msg" => $this->getErrorMsgZendFormat("Your are logged in")));
        }
    }

    public function deleteAuthorAction()
    {
        if ($this->getAdmin()->getId()) {
            $this->getAuthorTable()->delete(intval($this->params()->fromRoute("id")));
            return new JsonModel(array("returnCode" => 101, "msg" => "Author has been removed"));
        } else {
            return new JsonModel(array("returnCode" => 201, "msg" => $this->getErrorMsgZendFormat("Your are not logged in")));
        }
    }

    public function deleteGenreAction()
    {
        if ($this->getAdmin()->getId()) {
            $this->getGenreTable()->delete(intval($this->params()->fromRoute("id")));
            return new JsonModel(array("returnCode" => 101, "msg" => "Genre has been removed"));
        } else {
            return new JsonModel(array("returnCode" => 201, "msg" => $this->getErrorMsgZendFormat("Your are not logged in")));
        }
    }

}