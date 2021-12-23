<?php


namespace Phphleb\Spreader\Src;


interface TransferInterface
{
    public function get(bool $isUnaltered = false): ?array;

    public function saveIfNotExists(array $config): bool;

    public function save(array $config): bool;

    public function remove(): bool;

    public function setTarget(string $path, string $libName): TransferInterface;
}

