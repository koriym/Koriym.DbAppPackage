<?php

/**
 * This file is part of the Koriym.DevAppPackage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Koriym\DbAppPackage;

use BEAR\Package\Provide\Router\AuraRouterModule;
use Ray\AuraSqlModule\AuraSqlModule;
use Ray\Di\AbstractModule;
use Koriym\QueryLocator\QueryLocatorModule;
use Koriym\Now\NowModule;
use Ray\Query\SqlQueryModule;

class DbAppPackage extends AbstractModule
{
    /**
     * @var string
     */
    private $dsn;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var string
     */
    private $read;

    /**
     * @var string
     */
    private $dbDir;

    /**
     * @param string $dsn  PDO DSN {dbtype}:dbname={dbname};host={dbhost};port={dbport}
     * @param string $user username
     * @param string $pass password if any
     * @param string $read comma separated slave db server list
     */
    public function __construct(string $dsn, string $user, string $pass, string $read)
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->pass = $pass;
        $this->read = $read;
        $appDir = dirname(__DIR__, 4);
        $this->dbDir = $appDir . '/var/db';
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // router
        $this->override(new AuraRouterModule);
        // database
        $this->install(
            new AuraSqlModule(
                $this->dsn,
                $this->user,
                $this->pass,
                $this->read
            )
        );
        $this->install(new QueryLocatorModule($this->dbDir . '/sql'));
        $this->install(new SqlQueryModule($this->dbDir . '/sql'));
        $this->install(new NowModule);
    }
}
