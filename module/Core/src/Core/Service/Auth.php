<?php

namespace Core\Service;

use Core\Service\Service;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Db\Sql\Select;

class Auth extends Service
{

    /**
    * Adapter usado para a autenticação
    * @var Zend\Db\Adapter\Adapter
    */
    private $dbAdapter;

    public function __construct($adapter)
    {
        $this->dbAdapter = $adapter;
    }

    public function authenticate($params)
    {
        if (!isset($params['username']) || $params['username'] == '' || !isset($params['password']) ||  $params['password'] == '') {
            throw new \Exception("Parâmetros inválidos");
        }
        $password = md5($params['password']);
        $auth = new AuthenticationService();
        $authAdapter = new AuthAdapter($this->dbAdapter);
        $authAdapter
            ->setTableName('users')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setIdentity($params['username'])
            ->setCredential($password);

        $result = $auth->authenticate($authAdapter);

        if (! $result->isValid()) {
            throw new \Exception("Login ou senha inválidos");
        }
        $session = $this->getServiceManager()->get('Session');
        $session->offsetSet('user', $authAdapter->getResultRowObject());

        return true;
    }

    public function logout()
    {
        $auth = new AuthenticationService();
        $session = $this->getServiceManager->get('Session');
        $session->offsetUnset('user');
        $auth->clearIdentity();
        return true;
    }
    
    public function authorize()
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
           return true;
        }
        return false;
    }

}