<?php

namespace Phphleb\spreader\Src;

use ErrorException;
use Hleb\Static\Settings;
use Phphleb\Nicejson\JsonConverter;

class FileTransfer implements TransferInterface
{
    /**
     * @inheritDoc
     */
    #[\Override]
    public function get(string $lib, string $name): ?array
    {
        $data = @\file_get_contents($this->getPath($lib, $name));
        if (!$data) {
            return null;
        }
        if (\is_string($data) && \json_validate($data)) {
            return \json_decode($data, true, JSON_THROW_ON_ERROR);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function save(string $lib, string $name, array $config): void
    {
        $path = $this->getPath($lib, $name);
        \hl_create_directory($path);
        \file_put_contents($path, (new JsonConverter())->get($config), LOCK_EX);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function sync(string $lib, string $name): void
    {
        // Synchronization of the default configuration is not required.
        // Синхронизация дефолтной конфигурации не требуется.
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function update(string $lib, string $name): void
    {
        // You don't need to update the default configuration.
        // Обновление дефолтной конфигурации не требуется.
    }

    /**
     * @inheritDoc
     *
     * @throws ErrorException
     */
    #[\Override]
    public function clear(): void
    {
        throw new ErrorException('For file storage, data cleaning is not allowed!');
    }

    private function getPath(string $lib, string $name): string
    {
        return Settings::getPath("@storage/lib/$lib/$name.json");
    }
}