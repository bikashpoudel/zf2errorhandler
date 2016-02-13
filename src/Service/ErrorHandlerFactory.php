<?php
/**
 * @author Bikash Poudel <bikash.poudel.com@gmail.com>
 */

namespace GrassRoots\ErrorHandler\Service;

use GrassRoots\ErrorHandler\ErrorHandler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ErrorHandlerFactory
 * @package GrassRoots\ExceptionLogger\Listener
 */
class ErrorHandlerFactory implements FactoryInterface
{

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return ErrorHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $logger       = $serviceLocator->get('GrassRoots\Exception\Logger');
        $resolver     = $serviceLocator->get('Zend\View\Resolver\TemplatePathStack');
        $templatePath = $resolver->resolve('error/fatal');

        return new ErrorHandler($logger, $templatePath);
    }
}
