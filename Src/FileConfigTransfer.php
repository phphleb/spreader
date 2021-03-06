<?php
declare(strict_types=1);

namespace Phphleb\Spreader\Src;


use Phphleb\Nicejson\JsonConverter;

class FileConfigTransfer implements TransferInterface
{

    private ?string $path = null;

    public function get($isUnaltered = false): ?array
    {
        $content = @file_get_contents($this->path);
        if (!$content) {
            return null;
        }
        $result = json_decode($content, true);
        if (!$result) {
            return null;
        }
        return $result;
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
        $this->createDirIfExists();
        if (!file_exists($this->path)) {
            $fp = fopen($this->path, "w");
            $result = fwrite($fp, (new JsonConverter($config))->get());
            fclose($fp);
        } else {
            $result = file_put_contents($this->path, (new JsonConverter($config))->get());
        }
        return (bool)$result;
    }

    public function remove(): bool
    {
        return unlink($this->path);
    }

    public function createConfigStorage(): TransferInterface
    {
        $this->createDirIfExists();

        return $this;
    }

    public function setTarget(string $path, string $libName): TransferInterface
    {
        $this->path = $path;

        return $this;
    }

    private function createDirIfExists() {
        $directory = dirname($this->path);
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
    }


}

