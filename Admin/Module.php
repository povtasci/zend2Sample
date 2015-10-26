<?php
namespace Admin;

use Zend\Db\ResultSet\ResultSet;
use Zend\Authentication\Storage;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Db\TableGateway\TableGateway;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{

    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $eventManager->getSharedManager()->attach(array('Admin\Controller\AdminController', 'Admin\Controller\AdminActionController'), 'dispatch', function ($e) {
            $controller = $e->getTarget();
            $controller->layout('admin/layout');
        });
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

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Admin\Model\AdminAuthTable' => function ($sm) {
                    $tableGateway = $sm->get('AdminAuthTableGateway');
                    $table = new \Admin\Model\AdminAuthTable($tableGateway);
                    return $table;
                },
                'AdminAuthTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new \Admin\Model\AdminAuth());
                    return new TableGateway('admin', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\AdminAuthStorage' => function ($sm) {
                    return new \Admin\Model\AdminAuthStorage();
                },
                'AdminAuthService' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $dbTableAuthAdapter = new CredentialTreatmentAdapter($dbAdapter, 'admin', 'email', 'password', 'MD5(?)');

                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('Admin\Model\AdminAuthStorage'));

                    return $authService;
                },
            ),
        );
    }

}