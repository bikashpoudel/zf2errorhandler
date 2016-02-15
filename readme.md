#Installation Guide

Add the error handler in your global config
```
return array(
	'GrassRootsDms\ErrorHandler\ErrorHandler' => 'GrassRootsDms\ErrorHandler\Service\ErrorHandlerFactory',
);
```

Add your error logger configuration
```
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