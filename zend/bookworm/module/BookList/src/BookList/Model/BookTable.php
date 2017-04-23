<?php

namespace BookList\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use BookList\Model\Book;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;

class BookTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginated=false)
    {
        if($paginated)
        {
            $select = new Select('book');
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

    public function getBook($id)
    {
        $id = (int) $id;

        $rowset = $this->tableGateway->select(array('id' => $id));

        $row = $rowset->current();

        if(!$row)
        {
            throw new \Exception("L'élément de la recherche n'a pas été trouvé");
        }

        return $row;
    }

    public function saveBook(Book $book)
    {
        $data = array(
            'title' => $book->title,
            'author' => $book->author
        );

        $id = (int) $book->id;

        if($id == 0)
        {
            $this->tableGateway->insert($data);
        }else{
            if($this->getBook($id))
            {
                $this->tableGateway->update($data, array('id' => $id));
            }else{
                throw new \Exception("L'élément de la recherche n'a pas été trouvé");
            }
        }
    }

    public function deleteBook($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

}