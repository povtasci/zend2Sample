<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;


class AuthorTable
{
    protected $tableGateway;
    protected $_primary = 'author_id';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->adapter = new Adapter($this->tableGateway->getAdapter()->getDriver());
    }
    public function createNew()
    {
        return new Author();
    }

    public function save(Author $obj)
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

    public function fetchAll($paginated = false, $orderField = "author_id", $orderValue = "DESC")
    {
        if ($paginated) {
            $select = new Select('authors');
            $select->order("{$orderField} {$orderValue}");
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Author());
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

    public function find($id)
    {
        $resultSet = $this->tableGateway->select(array($this->_primary => $id))->current();
        return $resultSet;
    }

    public function delete($id)
    {
        $this->adapter->query("DELETE FROM books WHERE book_author_id = '{$id}'")->execute();
        $this->tableGateway->delete(array($this->_primary => $id));
        return true;
    }

}