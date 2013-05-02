<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\ActionController;
use Application\Form\Login;

class AuthController extends ActionController
{
    public function indexAction()
    {
        $form = new Login();
        return new ViewModel(array(
            'form' => $form
        ));
    }

    public function loginAction()
    {
        $request = $this->getRequest();
        
        if (!$request->isPost()) {
            throw new \Exception('Acesso inválido');
        }
        
        $data = $request->getPost();
        $service = $this->getService('Core\Service\Auth');
        $auth = $service->authenticate(
            array(
            'username' => $data['username'],
            'password' => $data['password']
            )
        );

        return $this->redirect()->toUrl('/');
    }

    public function logoutAction()
    {
        $service = $this->getService('Core\Service\Auth');
        $auth = $service->logout();

        return $this->redirect()->toUrl('/');
    }
}