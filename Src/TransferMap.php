<?php

declare(strict_types=1);

namespace Phphleb\Spreader\Src;

use Hleb\Static\Settings;

class TransferMap
{
    public function get(): array
    {
        $map = [];

        $storagePath = Settings::getRealPath('@storage/lib');
        if (!$storagePath) {
            return [];
        }
        $storageDir = \opendir($storagePath);
        if (!\is_resource($storageDir)) {
            return [];
        }

        while (false !== ($vendor = \readdir($storageDir))) {
            if ($vendor !== '.' && $vendor !== '..') {
                $vendorPath = Settings::getRealPath("@storage/lib/$vendor");
                if ($vendorPath) {
                    $libDir = \opendir($vendorPath);
                    if (!\is_resource($libDir)) {
                        continue;
                    }
                    while (false !== ($library = \readdir($libDir))) {
                        if ($library !== '.' && $library !== '..') {
                            $configPath = Settings::getRealPath("@storage/lib/$vendor/$library");
                            if ($configPath && Settings::getRealPath("@vendor/$vendor/$library")) {
                                $configDir = \opendir($configPath);
                                if (!\is_resource($configDir)) {
                                    continue;
                                }
                                while (false !== ($configName = \readdir($configDir))) {
                                    if ($configName !== '.' && $configName !== '..') {
                                        $lib = $vendor . '/' . $library;
                                        $configName = \str_replace('.json', '', $configName);
                                        $map[$lib][] = $configName;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $map;
    }
}