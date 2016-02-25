# Koriym.DbAppPackage

This package contains the following libraries.

* [Aura.Router v2](https://github.com/auraphp/Aura.Router/tree/2.x) A web router implementation for PHP. 
* [Aura.Sql v2](https://github.com/auraphp/Aura.Sql) Adapters for SQL database access 
* [Aura.SqlQuery v2](https://github.com/auraphp/Aura.SqlQuery) Independent query builders for MySQL, PostgreSQL, SQLite, and Microsoft SQL Server.
* [Phinx](https://phinx.org/) Database migrations 
* [Koriym.QueryLocator](https://github.com/koriym/Koriym.QueryLocator) SQL locator
* [Koriym.DevPdoDtatement](https://github.com/koriym/Koriym.DevPdoStatement) PDOStatement for query inspection

# Installation

## Composer Intall

```
composer create-project bear/skeleton {project-path}
```

    Created project in my-project
    > BEAR\Skeleton\Installer::preInstall

    What is the vendor name ?

    (MyVendor):

    What is the project name ?

    (MyProject):
    
```
cd {project-path}
composer require koriym/db-app-package
php vendor/koriym/db-app-package/bin/install.php
```

## Module Install 

Replace `PackageModule` with `DbAppPackage` in your AppModule.

    use josegonzalez\Dotenv\Loader as Dotenv;
    use Koriym\DbAppPackage\DbAppPackage; // add this line
    use Ray\Di\AbstractModule;

    class AppModule extends AbstractModule
    {
        /**
         * {@inheritdoc}
         */
        protected function configure()
        {
            Dotenv::load([
                'filepath' => dirname(dirname(__DIR__)) . '/.env',
                'toEnv' => true
            ]);
             // add this line
            $this->install(new DbAppPackage($_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_READ'])); 
        }
    }

# Configuration

## Database

`.env`

    DB_DSN=mysql:host=localhost;dbname=task
    DB_USER=root
    DB_PASS=
    DB_READ=

## Create databse

    php bin/create_db.php 

## Database migrations

Create migration.

    php vendor/bin/phinx create -c var/db/phinx.php MyNewMigration  


Perform migration.

    php vendor/bin/phinx migrate -c var/db/phinx.php

see more at [Phinx](http://docs.phinx.org/).
    
# Route

Edit `var/conf/aura.route.php`.

```php
<?php
/** @var $router \BEAR\Package\Provide\Router\AuraRoute */
$router->route('/task', '/task/{id}');
```
