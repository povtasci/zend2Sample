<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\View\Model\ViewModel;

class IndexController extends RootController
{
    public function indexAction()
    {
        $limit = 10;
        $books = $this->getBookTable()->findAll(array('book_status' => 'active'), $page = 1, $limit);

        return new ViewModel(array(
            "books" => is_array($books) ? $books['list'] : false,
            "count" => is_array($books) ? $books['count'] : false,
            "nextPage" => ($page * $limit < $books['count'] ? $page + 1: false),
        ));
    }

    public function genreAction()
    {
        if (!($id = $this->params()->fromRoute('id', false)) || !($genre = $this->getGenreTable()->find($id))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $limit = 10;
        $books = $this->getBookTable()->findAllByGenre($genre->getId(), array('book_status' => 'active'), $page = $this->params()->fromQuery('page', 1), $limit);

        $view =  new ViewModel(array(
            "genre" => $genre,
            "books" => is_array($books) ? $books['list'] : false,
            "count" => is_array($books) ? $books['count'] : false,
            "nextPage" => ($page * $limit < $books['count'] ? $page + 1: false),
        ));
        $view->setTemplate('application/index/index');
        return $view;
    }

    public function bookAction()
    {
        if (!($id = $this->params()->fromRoute('id', false)) || !($book = $this->getBookTable()->find($id))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel(array(
            "book" => $book,
        ));
    }
}
