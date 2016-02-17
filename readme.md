#Installation Guide

* Add the erorr log configuration (application.local.php)
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

* Add the error handler in your global config (global.php)
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

* Add your template for the fatal errors (global.php)
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

* error/fatal.html (view/error/fatal.html)
```
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title>We're sorry!</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
        *
        {
            color: #5e5e5e;
            font-family: Helvetica, arial, sans-serif;
            margin: 0px;
            padding: 0px;
        }
        #content
        {
            color: black;
            width: 500px;
            margin: 250px auto 0px auto;
        }
        #message
        {
            color: black;
            display: block;
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: -1px;
            text-align: center;
        }
        #message .caption
        {
            color: black;
            display: block;
            font-size: .8em;
        }
        #error-reference
        {
            color: black;
            text-align: center;
        }
        b {
            display: none;
        }
    </style>
</head>
<body>

<div id="content">
    <div id="message">
        Oops
        <div class="caption">Something went wrong. Please try again later.</div>
    </div>
    <br />
    <div id="error-reference">
        %__ERROR_REFERENCE__%
    </div>
</div>

</body>
</html>
```

And thats it.. you now have a fully working error handler that logs the errors to either the file of your choice or to you email. Db formatter will follow up in newer versions!!
