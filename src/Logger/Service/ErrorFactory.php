<?php
/**
 * @author Bikash Poudel <bikash.poudel.com@gmail.com>
 */

namespace GrassRootsDms\ErrorHandler;

use Zend\Log\Logger;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ErrorFactory
 * @package GrassRootsDms\ErrorHandler
 */
class ErrorFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Logger
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $loggerConfig = $config['exception_logger'];

        return new Logger($loggerConfig);
    }
}
