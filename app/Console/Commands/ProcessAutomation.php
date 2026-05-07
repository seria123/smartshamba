<?php

namespace App\Console\Commands;

use App\Services\AutomationService;
use Illuminate\Console\Command;

class ProcessAutomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automation:process {--field= : Process only for a specific field ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automation rules and trigger actions based on sensor readings';

    /**
     * Execute the console command.
     */
    public function handle(AutomationService $automationService): int
    {
        $this->info('Starting automation processing...');

        $fieldId = $this->option('field');

        if ($fieldId) {
            $this->info("Processing automation for field ID: {$fieldId}");
            $field = \App\Models\Field::find($fieldId);

            if (! $field) {
                $this->error("Field with ID {$fieldId} not found.");

                return Command::FAILURE;
            }

            $results = $automationService->processField($field);
        } else {
            $results = $automationService->processAutomation();
        }

        $this->info("Processed: {$results['processed']} fields");
        $this->info("Triggered: {$results['triggered']} actions");

        if (! empty($results['errors'])) {
            $this->warn('Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->warn("  - {$error}");
            }
        }

        $this->info('Automation processing completed.');

        return Command::SUCCESS;
    }
}
