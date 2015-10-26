<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Application\Model\Book;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class BookTable
{
    protected $tableGateway;
    protected $adapter;
    protected $_primary = "book_id";

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->adapter = new Adapter($this->tableGateway->getAdapter()->getDriver());
    }

    public function fetchAll($paginated = false, $orderField = "book_id", $orderValue = "DESC")
    {
        if ($paginated) {
            $select = new Select('books');
            $select->order("{$orderField} {$orderValue}");
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Book());
            $paginatorAdapter = new DbSelect(
                $select,
                $this->tableGateway->getAdapter(),
                $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function findAll($where = array(), $page = false, $limit = 10)
    {
        $whereString = "";
        if ($where) {
            foreach ($where as $key => $val) {
                $whereString .= ($whereString ? ", " : "WHERE ") . "`{$key}` = '{$val}'";
            }
        }
        $limitString = "";
        if ($page) {
            $limitString = " LIMIT " . (($page - 1) * $limit) . ", " . $limit;
        }

        $resultSet = $this->adapter->query("SELECT books.*, authors.* FROM books
                                                LEFT JOIN authors ON authors.author_id = books.book_author_id
                                                $whereString $limitString")->execute();
        if ($resultSet->count() == 0) {
            return false;
        }

        $books = array();
        while ($row = $resultSet->current()) {
            $books[$row['book_id']] = new Book($row);
            $books[$row['book_id']]->setAuthor(new Author($row));
            $resultSet->next();
        }

        foreach ($books as $book) {
            if ($genres = $this->getGenresByBookId($book->getId())) {
                foreach ($genres as $genre) {
                    $books[$book->getId()]->addGenre($genre);
                }
            }
        }

        $resultSet = $this->adapter->query("SELECT count(*) AS c FROM books $whereString")->execute();
        $row = $resultSet->current();
        return array(
            "list" => $books,
            "count" => $row['c'],
        );
    }

    public function findAllByGenre($genreId, $where = array(), $page = false, $limit = 10)
    {
        $whereString = "";
        if ($where) {
            foreach ($where as $key => $val) {
                $whereString .= ($whereString ? ", " : "WHERE ") . "`{$key}` = '{$val}'";
            }
        }
        $limitString = "";
        if ($page) {
            $limitString = " LIMIT " . (($page - 1) * $limit) . ", " . $limit;
        }

        $resultSet = $this->adapter->query("SELECT books.*, authors.* FROM books
                                                INNER JOIN rel_book_genre AS rel ON rel.rbg_book_id = books.book_id AND rel.rbg_genre_id = '{$genreId}'
                                                LEFT JOIN authors ON authors.author_id = books.book_author_id
                                                $whereString $limitString")->execute();
        if ($resultSet->count() == 0) {
            return false;
        }

        $books = array();
        while ($row = $resultSet->current()) {
            $books[$row['book_id']] = new Book($row);
            $books[$row['book_id']]->setAuthor(new Author($row));
            $resultSet->next();
        }

        foreach ($books as $book) {
            if ($genres = $this->getGenresByBookId($book->getId())) {
                foreach ($genres as $genre) {
                    $books[$book->getId()]->addGenre($genre);
                }
            }
        }

        $resultSet = $this->adapter->query("SELECT count(*) AS c FROM books
                                                INNER JOIN rel_book_genre AS rel ON rel.rbg_book_id = books.book_id AND rel.rbg_genre_id = '{$genreId}'
                                                $whereString")->execute();
        $row = $resultSet->current();
        return array(
            "list" => $books,
            "count" => $row['c'],
        );
    }

    public function save(Book $obj)
    {
        $data = $obj->getArrayCopyForInsert();
        $id = (int) $obj->getId();
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $res = $this->find($this->tableGateway->lastInsertValue);
        } else {
            if ($this->find($id)) {
                $this->tableGateway->update($data, array($this->_primary => $id));
                $res = $obj;
            } else {
                throw new \Exception('Form id does not exist');
            }
        }

        return $res;
    }

    public function createNew()
    {
        return new Book();
    }

    public function addGenreToBook($genreId, $bookId)
    {
        $this->adapter->query("INSERT IGNORE INTO rel_book_genre SET `rbg_genre_id` = '{$genreId}', `rbg_book_id` = '{$bookId}'")->execute();
        return true;
    }

    public function removeGenresFromBook($bookId)
    {
        $this->adapter->query("DELETE FROM rel_book_genre WHERE `rbg_book_id` = '{$bookId}'")->execute();
        return true;
    }

    public function find($id) {
        $resultSet = $this->adapter->query("SELECT books.*, authors.*, genres.* FROM books
                                                LEFT JOIN authors ON authors.author_id = books.book_author_id
                                                LEFT JOIN rel_book_genre AS rel ON rel.rbg_book_id = books.book_id
                                                LEFT JOIN genres ON genres.genre_id = rel.rbg_genre_id
                                                WHERE books.book_id = '{$id}'")->execute();
        if ($resultSet->count() == 0) {
            return false;
        }

        $book = new Book();
        while ($row = $resultSet->current()) {
            if (!$book->getId()) {
                $book->exchangeArray($row);
                $book->setAuthor(new Author($row));
            }
            if ($row['genre_id']) {
                $book->addGenre(new Genre($row));
            }
            $resultSet->next();
        }

        return $book;
    }

    public function delete(Book $book)
    {
        if ($book->getGenres()) {
            $this->removeGenresFromBook($book->getId());
        }
        $this->tableGateway->delete(array($this->_primary => $book->getId()));

        return true;
    }

    public function getGenresByBookId($bookId)
    {
        $resultSet = $this->adapter->query("SELECT genres.* FROM rel_book_genre AS rel
                                                LEFT JOIN genres ON genres.genre_id = rel.rbg_genre_id
                                                WHERE rel.rbg_book_id = '{$bookId}'")->execute();
        if ($resultSet->count() == 0) {
            return false;
        }
        $genres = array();
        while ($row = $resultSet->current()) {
            $genres[] = new Genre($row);
            $resultSet->next();
        }

        return $genres;
    }

}