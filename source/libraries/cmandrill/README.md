CMandrill - Mandrill Library for Joomla by compojoom
=======================================================

This library is based on the original PHP Wrapper developed by Mandrill:

The goal of this library is to make it easy for Joomla developers to use
this library in their extensions


Basic usage:
```php
$mandrill = new CmandrillQuery($apiKey, array( 'ssl' => 0));
$mandrill->message->send($message);
```
