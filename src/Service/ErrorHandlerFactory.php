<?php
/**
 * @author Bikash Poudel <bikash.poudel.com@gmail.com>
 */

namespace GrassRootsDms\ErrorHandler\Service;

use GrassRootsDms\ErrorHandler\ErrorHandler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ErrorHandlerFactory
 * @package GrassRootsDms\ErrorHandler\Service
 */
class ErrorHandlerFactory implements FactoryInterface
{

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return ErrorHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $logger       = $serviceLocator->get('GrassRootsDms\Logger\Error');
        $resolver     = $serviceLocator->get('Zend\View\Resolver\TemplatePathStack');
        $templatePath = $resolver->resolve('error/fatal');

        return new ErrorHandler($logger, $templatePath);
    }
}
