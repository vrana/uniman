<?php

namespace Adminerng\Drivers\MySql;

use Adminerng\Core\AbstractDriver;
use Adminerng\Core\Column;
use Adminerng\Core\Exception\ConnectException;
use Adminerng\Drivers\Redis\Forms\MySqlItemForm;
use PDO;
use PDOException;

class MySqlDriver extends AbstractDriver
{
    const TYPE_TABLE = 'table';
    const TYPE_VIEW = 'view';

    public function check()
    {
        return extension_loaded('pdo_mysql');
    }

    public function type()
    {
        return 'mysql';
    }

    public function defaultCredentials()
    {
        return [
            'server' => 'localhost:3306'
        ];
    }

    protected function getCredentialsForm()
    {
        return new MySqlForm();
    }

    public function connect(array $credentials)
    {
        $host = $credentials['server'];
        $port = '3306';
        if (strpos($credentials['server'], ':') !== false) {
            list($host, $port) = explode(':', $credentials['server'], 2);
        }
        $dsn = 'mysql:;host=' . $host . ';port=' . $port . ';charset=utf8';
        try {
            $this->connection = new PDO($dsn, $credentials['user'], $credentials['password']);
        } catch (PDOException $e) {
            throw new ConnectException($e->getMessage());
        }
    }

    public function databasesHeaders()
    {
        return [
            'database',
            'charset',
            'collation',
            'tables',
            'size'
        ];
    }

    public function tablesHeaders()
    {
        return [
            self::TYPE_TABLE => ['Table', 'Engine', 'Collation', 'Data length', 'Index length', 'Data free', 'Auto increment', 'Rows'],
            self::TYPE_VIEW => ['View', 'Check option', 'Is updatable', 'Definer', 'Security type', 'Character set', 'Collation'],
        ];
    }

    public function columns($type, $table)
    {
        $columns = [];
        foreach ($this->dataManager()->getColumns($type, $table) as $col) {
            $columns[] = (new Column())
                ->setKey($col['Field'])
                ->setTitle($col['Field'])
                ->setIsSortable(true)
                ->setIsNumeric($this->isNumeric($col))
                ->setDecimals($this->getDecimals($col));
        }
        return $columns;
    }

    private function isNumeric(array $column)
    {
        if (strpos($column['Type'], 'int') !== false) {
            return true;
        }
        if (strpos($column['Type'], 'float') !== false) {
            return true;
        }
        if (strpos($column['Type'], 'double') !== false) {
            return true;
        }
        if (strpos($column['Type'], 'decimal') !== false) {
            return true;
        }
        return false;
    }

    private function getDecimals(array $column)
    {
        if (!$this->isNumeric($column)) {
            return 0;
        }
        if (strpos($column['Type'], ',') === false) {
            return 0;
        }
        $pattern = '/(.*?)\((.*?),(.*?)\)/';
        preg_match($pattern, $column['Type'], $match);
        return isset($match[3]) ? $match[3] : 0;
    }

    public function itemForm($database, $type, $table, $item)
    {
        $this->dataManager()->selectDatabase($database);
        return new MySqlItemForm($this->connection, $this->dataManager(), $type, $table, $item);
    }

    protected function getPermissions()
    {
        return new MySqlPermissions();
    }

    protected function getDataManager()
    {
        return new MySqlDataManager($this->connection);
    }
}
