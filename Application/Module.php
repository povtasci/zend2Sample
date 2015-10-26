<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Application\Model\Author;
use Application\Model\AuthorTable;
use Application\Model\Book;
use Application\Model\BookTable;
use Application\Model\Genre;
use Application\Model\GenreTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
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

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\Model\BookTable' => function ($sm) {
                    $tableGateway = $sm->get('BookTableGateway');
                    $table = new BookTable($tableGateway);
                    return $table;
                },
                'BookTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Book());
                    return new TableGateway('books', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\GenreTable' => function ($sm) {
                    $tableGateway = $sm->get('GenreTableGateway');
                    $table = new GenreTable($tableGateway);
                    return $table;
                },
                'GenreTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Genre());
                    return new TableGateway('genres', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\AuthorTable' => function ($sm) {
                    $tableGateway = $sm->get('AuthorTableGateway');
                    $table = new AuthorTable($tableGateway);
                    return $table;
                },
                'AuthorTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Author());
                    return new TableGateway('authors', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
