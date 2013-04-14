=======
Http
=======

[![Build Status](https://travis-ci.org/Molajo/Http.png?branch=master)](https://travis-ci.org/Molajo/Http)

Molajo Http: Client, Cookies, File Upload, Headers, Redirect, Request, Response, Server, Session Classes
<pre>
URI Syntax (RFC 3986)

http://tools.ietf.org/html/rfc3986

scheme          http://
user            molajo:
password        crocodile
host            molajo.org
port            :80
base path       base/path/index.php?name=value#fragment

authority       molajo:crocodile@molajo.org:80
path            base/path/
query           name=value
fragment        #fragment
</pre>

## System Requirements ##

* PHP 5.3.3, or above
* [PSR-0 compliant Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
* PHP Framework independent
* [optional] PHPUnit 3.5+ to execute the test suite (phpunit --version)

## What is Http? ##

**Https** are used as **Services**, defined as **Interfaces** in Molajo.

## Basic Usage ##

```php
    $adapter = new Molajo/Http/Adapter($action, $component_type, $options);
```
#### Parameters ####

- **$action** valid values: `This`, `That`, and `TheOther`;
- **$component_type** Identifier for the component. Examples include `HttpType1` (default) and `HttpType2`;
- **$options** Associative array of named pair values needed for the specific Action.

#### Results ####

The output from the component action request, along with relevant data, can be accessed from the returned
object, as follows:

**Action Results:** For any request where data is to be returned, this example shows how to retrieve the output:

```php
    echo $adapter->ct->field;
```

### Installation

#### Install using Composer from Packagist

**Step 1** Install composer in your project:

```php
    curl -s https://getcomposer.org/installer | php
```

**Step 2** Create a **composer.json** file in your project root:

```php
{
    "require": {
        "Molajo/Http": "1.*"
    }
}
```

**Step 3** Install via composer:

```php
    php composer.phar install
```

**Step 4** Add this line to your application’s **index.php** file:

```php
    require 'vendor/autoload.php';
```

This instructs PHP to use Composer’s autoloader for **Http** project dependencies.

#### Or, Install Manually

Download and extract **Http**.

Create a **Molajo** folder, and then a **Http** subfolder in your **Vendor** directory.

Copy the **Http** files directly into the **Http** subfolder.

Register `Molajo\Http\` subfolder in your autoload process.

About
=====

Molajo Project adopted the following:

 * [Semantic Versioning](http://semver.org/)
 * [PSR-0 Autoloader Interoperability](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
 * [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
 and [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
 * [phpDocumentor2] (https://github.com/phpDocumentor/phpDocumentor2)
 * [phpUnit Testing] (https://github.com/sebastianbergmann/phpunit)
 * [Travis Continuous Improvement] (https://travis-ci.org/profile/Molajo)
 * [Packagist] (https://packagist.org)


Submitting pull requests and features
------------------------------------

Pull requests [GitHub](https://github.com/Molajo/Fileservices/pulls)

Features [GitHub](https://github.com/Molajo/Fileservices/issues)

Author
------

Amy Stephen - <AmyStephen@gmail.com> - <http://twitter.com/AmyStephen><br />
See also the list of [contributors](https://github.com/Molajo/Http/contributors) participating in this project.

License
-------

**Molajo Http** is licensed under the MIT License - see the `LICENSE` file for details

More Information
----------------
- [Extend](https://github.com/Molajo/Http/blob/master/.dev/Doc/extend.md)
- [Install](https://github.com/Molajo/Http/blob/master/.dev/Doc/install.md)
