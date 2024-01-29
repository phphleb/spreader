<?php

declare(strict_types=1);

namespace Phphleb\Spreader\Src;

use Phphleb\Spreader\Transfer;

class SyncCommand
{
    /**
     * Configuration synchronization for existing
     * and deployed libraries in the project.
     *
     * Синхронизация конфигурации для существующих
     * и развернутых в проекте библиотек.
     */
    public function run(): bool
    {
        $result = true;

        $map = (new TransferMap())->get();
        if (!$map) {
            echo 'No data found to transfer.' . PHP_EOL;
            return true;
        }

        $transfer = new Transfer();
        if ($transfer->isDefault()) {
            echo 'The current storage type is `File`. The transfer could not be completed.' . PHP_EOL;
            return true;
        }
        $transfer->disableCache();
        $transfer->clear();

        foreach ($map as $lib => $names) foreach($names as $name)  {
            echo "Moved data from /storage/lib/$lib/$name.json to global storage... ";
            $transfer->sync($lib, $name);
            $check = $transfer->get($lib, $name) !== null;
            echo ($check ? 'OK' : 'ERROR') . PHP_EOL;
            $result = $check ? $result : false;
        }
        return $result;
    }
}