# Koriym.DbAppPackage

This package contains the following libraries.

* [Aura.Router v2](https://github.com/auraphp/Aura.Router/tree/2.x) A web router implementation for PHP. 
* [Aura.Sql v2](https://github.com/auraphp/Aura.Sql) Adapters for SQL database access 
* [Aura.SqlQuery v2](https://github.com/auraphp/Aura.SqlQuery) Independent query builders for MySQL, PostgreSQL, SQLite, and Microsoft SQL Server.
* [Phinx](https://phinx.org/) Database migrations 
* [Koriym.QueryLocator](https://github.com/koriym/Koriym.QueryLocator) SQL locator

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
php vendor/koriym/db-app-package/scripts/install.php
```

## Module Install 

Replace `PackageModule` with `DbAppPackage` in your AppModule.

    use josegonzalez\Dotenv\Loader as Dotenv;
    use Koriym\DbAppPackage\DbAppPackage;
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
            $this->install(new DbAppPackage($_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_READ']));
        }
    }

# Configuration

## Database

Setup `.env` file as follows.

    DB_DSN=mysql:host=localhost;dbname=task
    DB_USER=root
    DB_PASS=
    DB_READ=

## Create databse

    php bin/create_db.php 

## Database migrations

    php vendor/bin/phinx create -c var/db/phinx.php MyNewMigration  

Open created file and set it up as follows:

```php
<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class MyNewMigration extends AbstractMigration
{
    public function change()
    {
        // create the table
        $table = $this->table('task');
        $table->addColumn('title', 'string', ['limit' => 100])
            ->addColumn('completed', 'text', ['limit' => MysqlAdapter::INT_TINY])
            ->addColumn('created', 'datetime')
            ->create();
    }
}
```

Perform migration.

    php vendor/bin/phinx migrate -c var/db/phinx.php
    
## Route

Add `/task` route in `var/conf/aura.route.php` as follows:

```php
<?php
/** @var $router \BEAR\Package\Provide\Router\AuraRoute */
$router->route('/task', '/task/{id}');
```

## Resource

Create `Resource/App/Task.php` file and set it up as following example `query Builder Ppattern` or `SQL locator pattern`

### query builder pattern

```php
<?php

namespace MyVendor\MyProject\Resource\App;

use BEAR\RepositoryModule\Annotation\Cacheable;
use BEAR\Resource\ResourceObject;
use Koriym\Now\NowInject;
use Koriym\QueryLocator\QueryLocatorInject;
use Ray\AuraSqlModule\AuraSqlDeleteInject;
use Ray\AuraSqlModule\AuraSqlInject;
use Ray\AuraSqlModule\AuraSqlInsertInject;
use Ray\AuraSqlModule\AuraSqlSelectInject;
use Ray\AuraSqlModule\AuraSqlUpdateInject;

/**
 * @Cacheable
 */
class Task extends ResourceObject
{
    use AuraSqlInject;
    use AuraSqlSelectInject;
    use AuraSqlInsertInject;
    use AuraSqlDeleteInject;
    use AuraSqlUpdateInject;
    use NowInject;
    use QueryLocatorInject;

    /**
     * @param string $id
     */
    public function onGet($id = null)
    {
        $this->select
            ->cols(['title', 'completed', 'created'])
            ->from('task');
        if ($id) {
            $this->select
                ->where('id = :id')
                ->bindValue('id', $id);
        }
        $this->body = $this->pdo->fetchAssoc($this->select->getStatement(), $this->select->getBindValues());

        return $this;
    }

    /**
     * @param string $title
     */
    public function onPost($title)
    {
        $this->insert
            ->into('task')
            ->cols([
                'title' => $title,
                'created' => $this->now
            ]);
        $statement = $this->insert->getStatement();
        $value = $this->insert->getBindValues();
        $this->pdo->perform($statement, $value);
        $id = $this->pdo->lastInsertId($this->insert->getLastInsertIdName('id'));
        $this->code = 201;
        $this->headers['Location'] = "/task/{$id}";

        return $this;
    }

    /**
     * @param string $id
     */
    public function onPatch($id)
    {
        $this->update
            ->table('task')
            ->cols([
                'completed' => true
            ])
            ->where('id = :id')
            ->bindValue('id', $id);
        $this->pdo->perform($this->update->getStatement(), $this->update->getBindValues());
        // affected row
        // $rows = $this->pdo->perform($this->update->getStatement(), $this->update->getBindValues());
        // $this->headers['x-affected-rows'] = $rows;

        return $this;
    }

    /**
     * @param $id
     */
    public function onDelete($id)
    {
        $this->delete
            ->from('task')
            ->where('id = :id')
            ->bindValue('id', $id);
        $this->pdo->perform($this->update->getStatement(), $this->update->getBindValues());

        return $this;
    }
}
```
# Resource Request

## Console access

### OPTIONS
```
php bootstrap/api.php options /task

// 200 OK
// allow: get, post, patch, delete
```
### POST
```
php bootstrap/api.php post '/task?title=run'

// 201 Created
// Location: /task/1
// content-type: application/hal+json
// ...
```

### PATCH

```
php bootstrap/api.php patch /task/1

// 200 OK
// content-type: application/hal+json
```

### GET
```
php bootstrap/api.php get /task/1
```

    200 OK
    content-type: application/hal+json
    ETag: 213861369
    Last-Modified: Fri, 05 Feb 2016 15:05:55 GMT
    
    {
        "run": {
            "title": "run",
            "completed": null,
            "created": "2016-02-05 16:04:05"
        },
        "_links": {
            "self": {
                "href": "/task/1"
            }
        }
    }

## Web access

Start web server.

```
php -S 127.0.0.1:8080 bootstrap/api.php 
```

Request with `curl`.

```
# OPTIONS
curl -i -X OPTIONS http://127.0.0.1:8080/task
# POST
curl -i --form "title=mail" http://127.0.0.1:8080/task
# PATCH
curl -i -X PATCH http://127.0.0.1:8080/task/1
# GET
curl -i -X GET http://127.0.0.1:8080/task/1
```
