<?php
return array(
	'di' => array(),
    'service_manager' => array(
        'factories' => array(
            'Session' => function($sm) {
                return new Zend\Session\Container('gestor_cc22ff');
            },
            'Core\Service\Auth' => function($sm) {
                $dbAdapter = $sm->get('DbAdapter');
                return new Core\Service\Auth($dbAdapter);
            },
        )
    ),
);
