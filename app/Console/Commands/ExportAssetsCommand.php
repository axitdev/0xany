<?php

namespace App\Console\Commands;

use App\Exports\AssetsExport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Excel;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ExportAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export:assets {--output=assets : Output filename} {--disk=google : Disk to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export assets to XLSX file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $outputFile = $this->option('output') . '-' . Carbon::now()->unix() . '.xlsx';
        $disk = $this->option('disk');

        $this->info('Export assets to XLSX file...');

        (new AssetsExport)->store($outputFile, $disk, Excel::XLSX);

        $this->info('Success!');

        return CommandAlias::SUCCESS;
    }
}
