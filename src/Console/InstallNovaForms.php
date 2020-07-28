<?php

namespace Day4\NovaForms\Console;

use Illuminate\Console\Command;

class InstallNovaForms extends Command
{
    protected $signature = 'novaforms:install';

    protected $description = 'Install the NovaForms';

    public function handle()
    {
        $this->info('Installing NovaForms...');

        $this->info('Publishing migrations and GraphQL schema...');

        $this->call('vendor:publish', [
            '--provider' => "Day4\NovaForms\NovaFormsServiceProvider"
        ]);

        $this->info('Running migration');
        $this->call('migrate');

        $file = base_path('graphql/schema.graphql');
        $current = file_get_contents($file);
        if (strpos($current, '#import forms.graphql') === false) {
            $current .= "#import forms.graphql\n";
            file_put_contents($file, $current);
        }

        $this->info('Validating GraphQL');
        $this->call('lighthouse:validate-schema');

        $this->info('NovaForms Installed');
    }
}