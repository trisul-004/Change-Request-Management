<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportPortalsFromExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-portals-from-excel {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import portals from an Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');
        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PortalsImport, $file);
            $this->info('Portals imported successfully!');
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return 1;
        }
        return 0;
    }
}
