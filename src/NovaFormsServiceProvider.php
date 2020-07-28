<?php

namespace Day4\NovaForms;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Day4\NovaForms\Resources\Form;
use Day4\NovaForms\Resources\FormEntry;
use Day4\NovaForms\Console\InstallNovaForms;

class NovaFormsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::resources([
            Form::class,
            FormEntry::class,
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallNovaForms::class,
            ]);

            // Publish migrations
            $this->publishes([
                __DIR__ . '/../database/migrations/2020_07_28_115435_create_forms.php.stub' => database_path('migrations/2020_07_28_115435_create_forms.php'),
                __DIR__ . '/../database/migrations/2020_07_28_115436_create_form_entries.php.stub' => database_path('migrations/2020_07_28_115436_create_form_entries.php'),
            ], 'migrations');

            // Publish graphql schema
            $this->publishes([
                __DIR__ . '/../graphql/schema.graphql.stub' => base_path('graphql/forms.graphql')
            ], 'gql-schema');
        }
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::namespace('Day4\NovaForms\Http\Controllers')
            ->prefix('day4/nova-forms')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
