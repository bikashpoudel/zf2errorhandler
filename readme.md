#Installation Guide

* Add the error handler in your global config
```
return array(
	'GrassRootsDms\ErrorHandler\ErrorHandler' => 'GrassRootsDms\ErrorHandler\Service\ErrorHandlerFactory',
);
```