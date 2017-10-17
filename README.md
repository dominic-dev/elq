# Elastique assesment

PHP backend RESTful API

#### Requires
```
php_apcu
```
```
sqlite3
```
```
composer
```

## Installing


#### Install dependencies with composer

From the root directory of the package where the index.php is located execute:

```
$ composer update
```

To install ```twig```, ```phpunit``` and its dependencies.

#### SQLITE
Make sure sqlite3 is installed,
and enabled in your php.ini file

UNIX

```
extension = sqlite.so
```

Windows

```
extension = php_pdo_sqlite.dll
```

## Getting Started

Run

```
$ php -S localhost:8080
```

And browse to localhost:8080 to access the API.



## Running the tests

From the root directory of the project where index.php is located execute:


```
./vendor/bin/phpunit
```

