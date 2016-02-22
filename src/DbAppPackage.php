<?php

namespace Koriym\DbAppPackage;

use BEAR\Package\PackageModule;
use BEAR\Package\Provide\Router\AuraRouterModule;
use Ray\AuraSqlModule\AuraSqlModule;
use Ray\Di\AbstractModule;
use Koriym\QueryLocator\QueryLocatorModule;
use Koriym\Now\NowModule;

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
    public function __construct($dsn, $user, $pass, $read)
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->pass = $pass;
        $this->read = $read;
        $appDir = dirname(dirname(dirname(dirname(__DIR__))));
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
                $_ENV['DB_DSN'],
                $_ENV['DB_USER'],
                $_ENV['DB_PASS'],
                $_ENV['DB_READ']
            )
        );
        $this->install(new QueryLocatorModule($this->dbDir . '/sql'));
        // datetime
        $this->install(new NowModule);
        $this->install(new PackageModule);
    }
}
