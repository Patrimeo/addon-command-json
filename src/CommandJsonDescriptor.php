<?php

namespace Patrimeo\AddonCommandJson;

use Composer\InstalledVersions;
use Patrimeo\Contracts\AddonDescriptor;
use Patrimeo\Contracts\Enums\Capability;
use Patrimeo\AddonCommandJson\CommandJsonService;

final class CommandJsonDescriptor implements AddonDescriptor
{
    public function getKey(): string
    {
        return 'command-json';
    }

    public function getCapability(): Capability
    {
        return Capability::ASSET_TRANSACTIONS;
    }

    public function getLabel(): string
    {
        return 'Command JSON';
    }

    public function getServiceClass(): string
    {
        return CommandJsonService::class;
    }

    public function getVersion(): string
    {
        return InstalledVersions::getPrettyVersion(
            'patrimeo/addon-command-json'
        ) ?? 'dev';
    }

    public function getDefaultSettings(): array
    {
        return [];
    }
}
