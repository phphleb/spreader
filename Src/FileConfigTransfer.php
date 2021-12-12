<?php
declare(strict_types=1);

namespace Phphleb\Spreader\Src;


use Phphleb\Nicejson\JsonConverter;

class FileConfigTransfer implements TransferInterface
{

    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function get(): array
    {
        return json_decode(file_get_contents($this->path), true);
    }

    public function saveIfNotExists(array $config): bool
    {
        if (!file_exists($this->path)) {
            return $this->save($config);
        }
        return false;
    }

    public function save(array $config): bool
    {
        return (bool)file_put_contents($this->path, (new JsonConverter($config))->get());
    }

    public function remove(): bool
    {
        return unlink($this->path);
    }


}

