<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Interfaces\CategoryInterface',
            'App\Repositories\CategoryRepository',
        );
        $this->app->bind(
            'App\Interfaces\BookInterface',
            'App\Repositories\BookRepository',
        );
        $this->app->bind(
            'App\Interfaces\MemberInterface',
            'App\Repositories\MemberRepository',
        );
        $this->app->bind(
            'App\Interfaces\IssueStatusInterface',
            'App\Repositories\IssueStatusRepository',
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
