<?php


namespace Phphleb\Spreader\Src;


interface TransferInterface
{
    public function __construct(string $parameter, string $target);

    public function get(): array;

    public function saveIfNotExists(array $config): bool;

    public function save(array $config): bool;

    public function remove(): bool;
}

