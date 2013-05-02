<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap($e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $moduleManager = $e->getApplication()->getServiceManager()->get('modulemanager');
        
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        //adiciona eventos ao módulo
        $sharedEvents->attach(
            'Zend\Mvc\Controller\AbstractActionController',
            MvcEvent::EVENT_DISPATCH,
                array($this, 'mvcPreDispatch'),
            100
        );
    }

    public function mvcPreDispatch($event)
    {
        $di = $event->getTarget()->getServiceLocator();
        $routeMatch = $event->getRouteMatch();
        $moduleName = $routeMatch->getParam('module');
        $controllerName = $routeMatch->getParam('controller');
        $actionName = $routeMatch->getParam('action');

        // $params = $routeMatch->getParams();
        // echo "<pre>";
        // print_r($params);
        // echo('Module: ' . $moduleName . '<br />Controller: ' . $controllerName . '<br>Action: ' . $actionName);
        // exit();
        
        if ($moduleName == 'admin' && $controllerName == 'Admin\Controller\Index') {
            $authService = $di->get('Core\Service\Auth');
            if (! $authService->authorize()) {
                $redirect = $event->getTarget()->redirect();
                $redirect->toUrl('/application/auth');
            }
        }

        return true;
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
