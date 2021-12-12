<?php
declare(strict_types=1);

namespace Phphleb\Spreader\Src;


use Hleb\Main\MainDB;

class DbConfigTransfer implements TransferInterface
{
    private const DEFAULT_NAME = 'global';

    private string $tableName = "spreader_configs";

    private string $name;

    private string $target;

    private ?array $dbConfig;

    public function __construct(string $target)
    {
        $this->name = (defined("HLEB_CONFIG_SPREADER_NAME") ? htmlentities(HLEB_CONFIG_SPREADER_NAME) : self::DEFAULT_NAME);

        $this->dbConfig = defined("HLEB_SPREADER_TYPE_DB") ? HLEB_PARAMETERS_FOR_DB[HLEB_SPREADER_TYPE_DB] : null;

        $this->target = $target;
    }

    public function get(): array
    {
        $result = [];
        try {
            $content = $this->getDataByDesignation();
        } catch (\Throwable $e) {
            $this->createTable($e);
            return $result;
        }
        if ($content) {
            $result = json_decode($content, true)[$this->target];
        }
        return $result;
    }

    public function saveIfNotExists(array $config): bool
    {
        $data = [];
        $content = null;
        try {
            $content = $this->getDataByDesignation();
            try {
                $data = json_decode($content, true);
            } catch (\Throwable $e) {
                error_log($e->getMessage());
                return false;
            }
        } catch (\Throwable $e) {
            $this->createTable($e);
        }
        $data[$this->target] = $config;
        if (!$content) {
            return (bool)MainDB::run("INSERT INTO {$this->tableName} (designation, content) VALUES ('{$this->name}', '" . json_encode($data) . "');", $this->dbConfig)->fetch();
        }
        return false;
    }

    public function save(array $config): bool
    {
        $data = [];
        $content = null;
        try {
            $content = $this->getDataByDesignation();
            try {
                $data = json_decode($content, true);
            } catch (\Throwable $e) {
                error_log($e->getMessage());
                return false;
            }
        } catch (\Throwable $e) {
            $this->createTable($e);
        }
        $data[$this->target] = $config;
        if (!$content) {
            return (bool)MainDB::run("INSERT INTO {$this->tableName} (designation, content) VALUES ('{$this->name}', '" . json_encode($data) . "');", $this->dbConfig)->fetch();
        }
        return (bool)MainDB::run("UPDATE {$this->tableName} SET content = ? WHERE designation = ?", [json_encode($data), $this->name], $this->dbConfig)->fetch();
    }

    public function remove(): bool
    {
        try {
            return MainDB::run("DELETE FROM {$this->tableName} WHERE designation = ?", [$this->name], $this->dbConfig)->fetchColumn();
        } catch (\Throwable $e) {
            error_log($e->getMessage());
        }
        return true;
    }

    private function createTable(?\Throwable $e = null): bool
    {
        if ($e !== null) {
            error_log($e->getMessage());
        }
        return (bool)MainDB::db_query("CREATE TABLE IF NOT EXISTS {$this->tableName} (designation varchar(100) NOT NULL, content varchar(5000) NOT NULL, UNIQUE KEY _designation (designation) );", $this->dbConfig);
    }

    private function getDataByDesignation(): ?string
    {
        return MainDB::run("SELECT content FROM {$this->tableName} WHERE designation = ? LIMIT 1", [$this->name], $this->dbConfig)->fetchColumn() ?: null;
    }

}

