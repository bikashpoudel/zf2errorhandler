<?php
/**
 * @author Bikash Poudel <bikash.poudel.com@gmail.com>
 */

namespace GrassRoots\ErrorHandler;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Log\Logger;
use Zend\Mvc\MvcEvent;

/**
 * Class ErrorHandler
 * @package GrassRoots\ExceptionLogger\Listener
 */
class ErrorHandler implements ListenerAggregateInterface
{

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $fatalErrorTemplatePath;

    /**
     * ErrorHandler constructor.
     * @param Logger $logger
     * @param string $fatalErrorTemplatePath
     */
    public function __construct(Logger $logger, $fatalErrorTemplatePath)
    {
        $this->logger = $logger;
        $this->fatalErrorTemplatePath = $fatalErrorTemplatePath;
    }

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, array($this, 'handleNativeErrors'), -1);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, array($this, 'handleFatalErrors'), -2);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'handleExceptions'), -1);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'handleExceptions'), -1);
    }

    /**
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * @param MvcEvent $event
     */
    public function handleNativeErrors(MvcEvent $event)
    {
        set_error_handler(function ($level, $message, $file, $line) use ($logger) {
            $minErrorLevel = error_reporting();
            if ($minErrorLevel & $level) {
                throw new \ErrorException($message, $code = 0, $level, $file, $line);
            }
            // return false to not continue native handler
            return false;
        });
    }

    /**
     * @param MvcEvent $event
     */
    public function handleFatalErrors(MvcEvent $event)
    {
        $logger = $this->logger;
        $templatePath = $this->fatalErrorTemplatePath;

        register_shutdown_function(function () use ($logger, $templatePath) {
            // get error
            $error = error_get_last();
            // check and allow only errors
            if (null === $error || $error['type'] !== E_ERROR) {
                return;
            }

            // clean any previous output from buffer
            while( ob_get_level() > 0 ) {
                ob_end_clean();
            }

            // generate unique reference for this error
            $chars = md5(uniqid('', true));
            $errorReference = substr($chars, 2, 2) . substr($chars, 12, 2) . substr($chars, 26, 2);

            $extras = array(
                'reference' => $errorReference,
                'file' => $error['file'],
                'line' => $error['line']
            );

            // get priority from logger class
            $priority = Logger::$errorPriorityMap[$error['type']];
            // log error message and the extra info
            $logger->log($priority, $error['message'], $extras);

            // read content of file
            $body = file_get_contents($templatePath);
            // inject error reference
            $body = str_replace('%__ERROR_REFERENCE__%', 'Error Reference: ' .  $errorReference, $body);
            echo $body;
            die;
        });
    }

    /**
     * Handle exceptions
     *
     * @param MvcEvent $event
     */
    public function handleExceptions(MvcEvent $event)
    {
        $logger = $this->logger;

        // check if event is error
        if (!$event->isError()) {
            return;
        }
        // get message and exception (if present)
        $message = $event->getError();
        $exception = $event->getParam('exception');

        if (!$exception) {
            return;
        }

        // generate unique reference for this error
        $chars = md5(uniqid('', true));
        $errorReference = substr($chars, 2, 2) . substr($chars, 12, 2) . substr($chars, 26, 2);
        // add it to logger extra array
        $extras = array(
            'reference' => $errorReference
        );

        // check if event has exception and populate extras array.
        if (!empty($exception)) {
            $stackTrace   = array();

            $currentTrace = new \stdClass();
            $currentTrace->class      = get_class($exception);
            $currentTrace->file       = $exception->getFile();
            $currentTrace->message    = $exception->getMessage();
            $currentTrace->stackTrace = $exception->getTraceAsString();
            $stackTrace[] = $currentTrace;

            //$stackTrace[] = $exception->getTraceAsString();

            $e = $exception->getPrevious();
            if ($e instanceof \Exception) {
                while ($e) {
                    $currentTrace = new \stdClass();
                    $currentTrace->class = get_class($e);
                    $currentTrace->file = $e->getFile();
                    $currentTrace->message = $e->getMessage();
                    $currentTrace->stackTrace = $e->getTraceAsString();
                    $stackTrace[] = $currentTrace;

                    $e = $e->getPrevious();
                }
            }

            $exceptionBody = '';
            $i = 0;
            foreach ($stackTrace as $trace) {
                if ($i == 1) {
                    $exceptionBody .= PHP_EOL . PHP_EOL . 'Previous Exceptions' . PHP_EOL . PHP_EOL ;
                }
                $i++;

                $exceptionBody .= PHP_EOL . $trace->class . PHP_EOL;
                $exceptionBody .= PHP_EOL . 'File:' . PHP_EOL . $trace->file . PHP_EOL;
                $exceptionBody .= PHP_EOL . 'Message:' . PHP_EOL . $trace->message . PHP_EOL;
                $exceptionBody .= PHP_EOL . 'Stack trace:' . PHP_EOL  . $trace->stackTrace . PHP_EOL;
            }

            $extras['trace'] = $exceptionBody;
        }

        // log it
        $priority = Logger::ERR;
        $logger->log($priority, $message, $extras);

        // hijack skeleton error view and add error reference to the message
        $originalMessage = $event->getResult()->getVariable('message');
        $event->getResult()->setVariable('message', $originalMessage . '<br /> Error Reference: ' .  $errorReference);
    }
}
