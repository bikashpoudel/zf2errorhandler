#Installation Guide

* Add the error handler in your global config
```
return array(
	'GrassRootsDms\ErrorHandler\ErrorHandler' => 'GrassRootsDms\ErrorHandler\Service\ErrorHandlerFactory',
);
```

* Add your error log configuration
```
$mail = new \Zend\Mail\Message;
$mail->setFrom('bikash.poudel@grg.com');
$mail->setTo('bikash.poudel@grg.com');
$mail->setSubject('Important: Meeting room tool error!');
$mail->setEncoding('UTF-8');

'exception_logger' => array(
	'writers' => array(
		'stream' => array(
			'name' => 'stream',
			'options' => array(
			'stream' => 'php://output',//getcwd() . '/data/logs/' . date('Y-m-d') . '.error.log',
			'formatter' => array(
				'name'    => 'Application\Logger\ErrorHandler\Formatter\Generic',
			),
		),
	),
	'mail' => array(
		'name'    => 'mail',
		'options' => array(
			'mail' => $mail,
			'formatter' => array(
					'name'    => 'Application\Logger\ErrorHandler\Formatter\Mail',
				),
			),
		),
	),
),
```