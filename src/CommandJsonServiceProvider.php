<?php

namespace Patrimeo\AddonCommandJson;

use Patrimeo\Contracts\AddonRegistrar;
use Illuminate\Support\ServiceProvider;
use Patrimeo\AddonCommandJson\CommandJsonDescriptor;

final class CommandJsonServiceProvider extends ServiceProvider
{

    public function boot(AddonRegistrar $addonRegistrar)
    {
        $addonRegistrar->register(new CommandJsonDescriptor());
    }
}
