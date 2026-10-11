<?php

namespace App\Providers;

use Aws\CommandInterface;
use Aws\Middleware;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::extend('r2', function (Application $app, array $config): AwsS3V3Adapter {
            $disk = $app->make('filesystem')->createS3Driver($config);
            $disk->getClient()->getHandlerList()->appendInit(Middleware::mapCommand(function (CommandInterface $command): CommandInterface {
                unset($command['ACL']);

                return $command;
            }), 'r2-without-acl');

            return $disk;
        });
    }
}
