<?php

namespace App\Console\Commands;

use App\Imports\PortalsImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportPortals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:portals {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import portals from Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error('File does not exist!');
            return 1;
        }

        try {
            Excel::import(new PortalsImport, $file);
            $this->info('Portals imported successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error importing portals: ' . $e->getMessage());
            return 1;
        }
    }
}
