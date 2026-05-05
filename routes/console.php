<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about:foundation', function (): void {
    $this->info('SmartShamba modular farm platform foundation.');
})->purpose('Describe the project foundation');
