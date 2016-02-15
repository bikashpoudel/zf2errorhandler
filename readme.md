#Installation Guide

* Add your error log configuration
```
$mail = new \Zend\Mail\Message;
$mail->setFrom('bikash.poudel@grg.com');
$mail->setTo('bikash.poudel@grg.com');
$mail->setSubject('Important: Meeting room tool error!');
$mail->setEncoding('UTF-8');

return array(
    'exception_logger' => array(
        'writers' => array(
            'stream' => array(
                'name' => 'stream',
                'options' => array(
                    'stream' => 'php://output',//getcwd() . '/data/logs/' . date('Y-m-d') . '.error.log',
                    'formatter' => array(
                        'name'    => 'GrassRootsDms\ErrorHandler\Formatter\Generic',
                    ),
                ),
            ),
            'mail' => array(
                'name'    => 'mail',
                'options' => array(
                    'mail' => $mail,
                    'formatter' => array(
                        'name'    => 'GrassRootsDms\ErrorHandler\Formatter\Generic',
                    ),
                ),
            ),
        ),
    ),
);
```

* Add the error handler in your global config
```
return array(
    'listeners' => array(
        'GrassRootsDms\ErrorHandler\ErrorHandler',
    ),
    'service_manager' => array(
        'factories' => array(
            'GrassRootsDms\Logger\Error'              => 'GrassRootsDms\ErrorHandler\Logger\Service\ErrorFactory',
            'GrassRootsDms\ErrorHandler\ErrorHandler' => 'GrassRootsDms\ErrorHandler\Service\ErrorHandlerFactory',
        ),
    ),
);
```

* Add your template for the fatal errors
```
return array(
    'view_manager' => array(
        ...
        'template_map' => array(
                'error/fatal'             => __DIR__ . '../view/error/fatal.phtml',      
        )
        ...
    ),
);
```