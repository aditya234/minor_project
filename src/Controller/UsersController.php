<?php
namespace App\Controller;

use App\Controller\AppController;
use Aura\Intl\Exception;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[] paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Types']
        ];
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Types']
        ]);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The {0} has been saved.', 'User'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'User'));
            }
        }
        $types = $this->Users->Types->find('list', ['limit' => 200]);
        $this->set(compact('user', 'types'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The {0} has been saved.', 'User'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'User'));
            }
        }
        $types = $this->Users->Types->find('list', ['limit' => 200]);
        $this->set(compact('user', 'types'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The {0} has been deleted.', 'User'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'User'));
        }
        return $this->redirect(['action' => 'index']);
    }
    public function login(){
        $this->viewBuilder()->setLayout('');
        if($this->request->is('post'))
        {
            $user=$this->Auth->identify();
            $menus=$this->filterMenus($user['type_id']);
            $user['menus']=$menus;
//            debug($user);
//            die();
            if($user)
            {
                $this->Auth->setUser($user);
//                debug($user);
//                die();
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error('You have entered wrong credentials');
        }
    }
    protected function filterMenus($typeID){
        $user_menus=[];
        $m=$this->loadModel('Menus');
        $menus=$m->find('threaded')->contain('Types')->where(['display'=>1]);
        foreach ($menus as $menu) {
//            debug($menu->types[0]->id);
            if($menu->types[0]->id == $typeID)
                array_push($user_menus,$menu);
            else
                continue;
        }
//        debug($typeID);
//        die();
        return $user_menus;
    }
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }
}
