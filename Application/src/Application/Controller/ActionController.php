<?php
namespace Application\Controller;

use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ActionController extends RootController
{
    public function getBooksAction()
    {
        $genre = false;
        $author = false;
        if (($id = $this->params()->fromPost('genre', 0)) && ($genre = $this->getGenreTable()->find($id))) {
            $books = $this->getBookTable()->findAllByGenre($genre->getId(), array('book_status' => 'active'), $page = $this->params()->fromPost('page', 1), 10);
        } elseif (($id = $this->params()->fromPost('author', 0)) && ($author = $this->getGenreTable()->find($id))) {
            $books = $this->getBookTable()->findAllByGenre($genre->getId(), array('book_status' => 'active'), $page = $this->params()->fromPost('page', 1), 10);
        } else {
            $books = $this->getBookTable()->findAll(array('book_status' => 'active'), $page = $this->params()->fromPost('page', 1), 10);
        }
        $htmlViewPart = new ViewModel();
        $htmlViewPart->setTerminal(true)
            ->setTemplate('application/index/index')
            ->setVariables(array(
                "books" => is_array($books) ? $books['list'] : false,
                "count" => is_array($books) ? $books['count'] : false,
                "nextPage" => ($page * 10 < $books['count'] ? $page + 1: false),
                "genre" => $genre,
                "author" => $author,
            ));

        $htmlOutput = $this->getServiceLocator()
            ->get('viewrenderer')
            ->render($htmlViewPart);

        return new JsonModel(array(
            'returnCode' => 101,
            'result' => array(
                'html' => $htmlOutput
            ),
        ));
    }
}
