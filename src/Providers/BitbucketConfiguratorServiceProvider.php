<?php

namespace Waglero;

use Illuminate\Support\ServiceProvider;
use Waglero\Commands\Bitbucket\AddItemCommand;
use Waglero\Commands\Bitbucket\RemoveItemCommand;

class BitbucketConfiguratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            AddItemCommand::class,
            RemoveItemCommand::class
        ]);
    }

    public function register()
    {
        $this->commands([
            AddItemCommand::class,
            RemoveItemCommand::class
        ]);
    }
}