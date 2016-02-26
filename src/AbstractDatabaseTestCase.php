<?php

/**
 * This file is part of the Koriym.DevAppPackage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Koriym\DbAppPackage;

abstract class AbstractDatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * @var \BEAR\Resource\ResourceInterface
     */
    protected $resource;

    protected function setUp()
    {
        parent::setUp();
        $this->resource = $GLOBALS['RESOURCE'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getSetUpOperation()
    {
        return \PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTearDownOperation()
    {
        return \PHPUnit_Extensions_Database_Operation_Factory::TRUNCATE();
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSet()
    {
        $path = dirname((new \ReflectionClass($this))->getFileName()) . '/fixtures';
        $dataSets = [];
        foreach (glob(rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.xml') as $file) {
            $dataSets[] = $this->createMySQLXMLDataSet($file);
        }
        $compositeDataset = new \PHPUnit_Extensions_Database_DataSet_CompositeDataSet;
        foreach ($dataSets as $dataSet) {
            $compositeDataset->addDataSet($dataSet);
        }

        return $compositeDataset;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        $pass = isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : '';
        $dsn = $this->getTestDsn($_ENV['DB_DSN']);
        $pdo = new \PDO($dsn, $_ENV['DB_USER'], $pass);

        return $this->createDefaultDBConnection($pdo);
    }

    /**
     * {@inheritdoc}
     */
    public function getRowCount()
    {
        return $this->getConnection()->getRowCount(static::TABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getPdo()
    {
        return $this->getConnection()->getConnection();
    }

    /**
     * @param string $dsn
     *
     * @return string
     */
    private function getTestDsn($dsn)
    {
        return preg_replace("/(.*?)dbname=(.*?)($|;)(.*)/", "$1dbname=$2_test$3", $dsn);
    }
}
