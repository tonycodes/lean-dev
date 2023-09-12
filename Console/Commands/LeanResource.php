<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class LeanResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:lean-resource {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffolds crud for Laravel resources';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $model = $this->argument('model');

        Artisan::call("make:model $model");
        Artisan::call("make:controller $model/{$model}Controller --model={$this->argument('model')}");
        Artisan::call("make:request $model/{$model}CreateRequest");
        Artisan::call("make:request $model/{$model}UpdateRequest");

    }

}
