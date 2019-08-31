<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class ClientesController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for clientes
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Clientes', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $clientes = Clientes::find($parameters);
        if (count($clientes) == 0) {
            $this->flash->notice("La busqueda no pudo encontrar ningun cliente");

            $this->dispatcher->forward([
                "controller" => "clientes",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $clientes,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a cliente
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $cliente = Clientes::findFirstByid($id);
            if (!$cliente) {
                $this->flash->error("cliente was not found");

                $this->dispatcher->forward([
                    'controller' => "clientes",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $cliente->id;

            $this->tag->setDefault("id", $cliente->id);
            $this->tag->setDefault("name", $cliente->name);
            $this->tag->setDefault("emailid", $cliente->emailid);
            $this->tag->setDefault("aporte", $cliente->aporte);
            $this->tag->setDefault("mes", $cliente->mes);
            
        }
    }

    /**
     * Creates a new cliente
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "clientes",
                'action' => 'index'
            ]);

            return;
        }

        $cliente = new Clientes();
        $cliente->name = $this->request->getPost("name");
        $cliente->emailid = $this->request->getPost("emailid");
        $cliente->aporte = $this->request->getPost("aporte");
        $cliente->mes = $this->request->getPost("mes");
        

        if (!$cliente->save()) {
            foreach ($cliente->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "clientes",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("Cliente agregado exitosamente");

        $this->dispatcher->forward([
            'controller' => "clientes",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a cliente edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "clientes",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $cliente = Clientes::findFirstByid($id);

        if (!$cliente) {
            $this->flash->error("Este cliente no existe " . $id);

            $this->dispatcher->forward([
                'controller' => "clientes",
                'action' => 'index'
            ]);

            return;
        }

        $cliente->name = $this->request->getPost("name");
        $cliente->emailid = $this->request->getPost("emailid");
        $cliente->aporte = $this->request->getPost("aporte");
        $cliente->mes = $this->request->getPost("mes");
        

        if (!$cliente->save()) {

            foreach ($cliente->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "clientes",
                'action' => 'edit',
                'params' => [$cliente->id]
            ]);

            return;
        }

        $this->flash->success("Datos del cliente actualizados correctamente");

        $this->dispatcher->forward([
            'controller' => "clientes",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a cliente
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $cliente = Clientes::findFirstByid($id);
        if (!$cliente) {

            $this->flash->error("El cliente no fue encontrado");
            $this->dispatcher->forward([
                'controller' => "clientes",
                'action' => 'index'
            ]);

            return;
        }

        if (!$cliente->delete()) {

            foreach ($cliente->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "clientes",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("Cliente eliminado satisfactoriamente");

        $this->dispatcher->forward([
            'controller' => "clientes",
            'action' => "index"
        ]);
    }

}
