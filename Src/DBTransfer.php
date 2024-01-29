<?php

namespace Phphleb\Spreader\Src;

use Hleb\Static\Settings;
use Hleb\Static\System;
use Phphleb\Spreader\Transfer;

class DBTransfer implements TransferInterface
{
    private string $tbl = "hleb_spreader_conf";

    /**
     * @inheritDoc
     */
    #[\Override]
    public function get(string $lib, string $name): ?array
    {
        try {
            // Attempt to get data.
            // Попытка получения данных.
            $data = $this->getData($lib);
        } catch (\PDOException) {
            $data = null;
        }
        if (!$data) {
            // The table may not have been created.
            // Возможно, не создана таблица.
            $this->createTableIfNotExists();
            $data = (new Transfer(isDefault: true))->get($lib, $name);
            // If the table has been created, then transfer the data.
            // Если таблица создалась, то перенос данных.
            if (!$data) {
                return null;
            }
            $this->save($lib, $name, $data);
            $data = $this->getData($lib);
        }
        // Another attempt to get data.
        // Еще одна попытка получить данные.
        if (\is_string($data) && \json_validate($data)) {
            $params = \json_decode($data, true, JSON_THROW_ON_ERROR);
            return $params[$name] ?? null;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function save(string $lib, string $name, array $config): void
    {
        try {
            // Attempt to get data.
            // Попытка получения данных.
            $content = $this->getData($lib);
        } catch (\PDOException) {
            $content = null;
        }
        if ($content === null) {
            // The table may not have been created.
            // Возможно, не создана таблица.
            $this->createTableIfNotExists();
        }
        $data = [$name => $config];
        $params = $this->getData($lib);
        if ($params) {
            $data = \json_decode($params, true, JSON_THROW_ON_ERROR);
            $data[$name] = $config;
        }

        $json = \json_encode($data);
        if (!$content) {
            self::run("INSERT INTO {$this->tbl} (designation, content) VALUES ('{$lib}', '" . $json . "');")->fetch();
        }
        self::run("UPDATE {$this->tbl} SET content = ? WHERE designation = ?", [$json, $lib])->fetch();

        // Not quite the right architectural decision, but it is necessary to unload the default method.
        // Не совсем правильное архитектурное решение, но оно необходимо, чтобы разгрузить дефолтный метод.
        (new Transfer(isDefault: true))->save($lib, $name, $config);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function sync(string $lib, string $name): void
    {
        $data = $this->get($lib, $name) ?? (new Transfer(isDefault: true))->get($lib, $name);
        if ($data) {
            $this->save($lib, $name, $data);
        }
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function update(string $lib, string $name): void
    {
        $data = (new Transfer(isDefault: true))->get($lib, $name);
        if ($data) {
            $this->save($lib, $name, $data);
        }
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function clear(): void
    {
        try {
            self::run("DELETE FROM {$this->tbl}")->fetchColumn();
        } catch(\PDOException) {
            // The table may not exist.
            // Таблицы может не существовать.
        }
    }

    protected static function run($sql, $args = []): \PDOStatement
    {
        $configKey = Settings::getParam('database', 'spread.db.type');
        $pdo = System::getPdoManager($configKey);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($args);

        return $stmt;
    }

    private function createTableIfNotExists(): void
    {
        self::run("CREATE TABLE IF NOT EXISTS {$this->tbl} (designation varchar(255) NOT NULL, content varchar(5000) NOT NULL, UNIQUE (designation) );")->fetch();
    }

    private function getData(string $lib): string|null
    {
        return self::run("SELECT content FROM {$this->tbl} WHERE designation = ? LIMIT 1", [$lib])->fetchColumn() ?: null;
    }
}