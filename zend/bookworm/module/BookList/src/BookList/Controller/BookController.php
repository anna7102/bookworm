<?php
namespace BookList\Controller;

use BookList\Form\BookForm;
use BookList\Model\Book;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail;

class BookController extends AbstractActionController {

    protected $bookTable;

    public function indexAction()
    {
        $paginator = $this->getBookTable()->fetchAll(true);

        $paginator->setCurrentPageNumber(
            (int) $this->params()->fromQuery('page', 1)
        );

        $paginator->setItemCountPerPage(5);

        return new ViewModel(array(
            'paginator' => $paginator
        ));
    }

    public function addAction()
    {
        $form = new BookForm();

        $form->get('submit')->setValue('Ajouter');
        $request = $this->getRequest();

        if($request->isPost())
        {
            $book = new Book();
            $form->setInputFilter($book->getInputFilter());
            $form->setData($request->getPost());

            if($form->isValid())
            {
                $book->exchangeArray($form->getData());
                $this->getBookTable()->saveBook($book);

                //email
              /*  $mail = new Mail\Message();

                $mail->setBody('Nouveau livre ajouté :' . $book->title);

                $mail->setFrom('apptest1720@gmail.com', 'Ania');
                $mail->addTo('empresswu89@gmail.com', 'Ania');
                $mail->setSubject('Nouveau livre');

                $transport =new Mail\Transport\Sendmail();

                $transport->send($mail);*/

            }

            return $this->redirect()->toRoute('book');
        }
        return array(
            'form' => $form
        );
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if(!$id)
        {
            return $this->redirect()->toRoute('book');
        }
        $book = $this->getBookTable()->getBook($id);
        $form = new BookForm();

        $form->bind($book);

        $form->get('submit')->setAttribute('value','Modifier');
        $request = $this->getRequest();

        if($request->isPost())
        {
            $form->setInputFilter($book->getInputFilter());
            $form->setData($request->getPost());

            if($form->isValid())
            {
                $this->getBookTable()->saveBook($book);
            }

            return $this->redirect()->toRoute('book');
        }
        return array(
            'id' => $id,
            'form' => $form
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $book = $this->getBookTable()->getBook($id);

        if(!$id)
        {
            return $this->redirect()->toRoute('book');
        }
        $request = $this->getRequest();

        if($request->isPost())
        {
            $del = $request->getPost('del', 'Non');

            if($del == 'Oui')
            {
                $id = (int) $request->getPost('id');
                $this->getBookTable()->deleteBook($id);
            }

            return $this->redirect()->toRoute('book');
        }
        return array(
            'id' => $id,
            'book' => $book
        );
    }

    public function getBookTable()
    {
        if(!$this->bookTable)
        {
            $sm = $this->getServiceLocator();
            $this->bookTable = $sm->get('BookList\Model\BookTable');
        }

        return $this->bookTable;
    }


}