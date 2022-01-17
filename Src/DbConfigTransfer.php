<?php
declare(strict_types=1);

namespace Phphleb\Spreader\Src;


use Hleb\Main\MainDB;

class DbConfigTransfer implements TransferInterface
{
    private const DEFAULT_NAME = 'global';

    private string $tableName = "spreader_configs";

    private string $name;

    private ?string $target = null;

    private ?array $dbConfig;

    private ?array $data = null;

    public function __construct()
    {
        $this->name = (defined("HLEB_CONFIG_SPREADER_NAME") ? htmlentities(HLEB_CONFIG_SPREADER_NAME) : self::DEFAULT_NAME);

        $this->dbConfig = defined("HLEB_SPREADER_TYPE_DB") ? HLEB_PARAMETERS_FOR_DB[HLEB_SPREADER_TYPE_DB] : null;
    }

    public function get($isUnaltered = false): ?array
    {
        $result = [];
        if ($isUnaltered && !is_null($this->data)) {
            return $this->data[$this->target] ?? null;
        }
        $content = $this->getDataByDesignation();
        if ($content) {
            $data = json_decode($content, true);
            $this->data = $isUnaltered ? $data : null;
            $result = $data[$this->target] ?? null;
        }
        return $result;
    }

    public function saveIfNotExists(array $config): bool
    {
        $data = [];
        $content = $this->getDataByDesignation();
        if ($content) {
            try {
                $data = json_decode($content, true);
            } catch (\Throwable $e) {
                error_log($e->getMessage());
                return false;
            }
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
        $content = $this->getDataByDesignation();
        if ($content) {
            try {
                $data = json_decode($content, true);
            } catch (\Throwable $e) {
                error_log($e->getMessage());
                return false;
            }
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

    public function setTarget(string $path, string $target): TransferInterface
    {
        $this->target = $target;

        return $this;
    }

    public function createConfigStorage(): bool
    {
       return $this->createTableIfNotExists();
    }

    private function createTableIfNotExists(): bool
    {
        return (bool)MainDB::db_query("CREATE TABLE IF NOT EXISTS {$this->tableName} (designation varchar(100) NOT NULL, content varchar(5000) NOT NULL, UNIQUE (designation) );", $this->dbConfig);
    }

    private function getDataByDesignation(): ?string
    {
        return MainDB::run("SELECT content FROM {$this->tableName} WHERE designation = ? LIMIT 1", [$this->name], $this->dbConfig)->fetchColumn() ?: null;
    }

}

