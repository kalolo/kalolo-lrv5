<?php

namespace Klrv\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\CountValidator\Exception;
use Symfony\Component\Console\Input\InputArgument;

class DomainImport extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lrv:domain-import
    {csv : CSV File path}
    {--dry-run=true : Dry run mode}
    {--ignore-header= : Ignore CSV Header}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import domains';

    protected $csvPath;

    protected $dryRun;

    protected $csvData;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('[+] Reading CSV');

        $this->csvPath      = $this->argument('csv');
        $this->dryRun       = boolval($this->option('dry-run'));
        $this->ignoreHeader = boolval($this->option('ignore-header'));

        if ($this->dryRun) return $this->doDryRun();

        return $this->import();

    }

    private function doDryRun()
    {
        $this->info('CSV Path: ' . $this->csvPath);
        $this->info('Dry Run : ' . $this->dryRun);

        $this->readCSV();

        foreach ($this->csvData as $data) {
            $this->info($data[0] . ' [' . $data[1] . '] ' . $data[2]);
        }

        return;
    }

    private function import()
    {
        $this->info('Importing');

        $this->readCSV();

        $systemUser = User::where('email','=','system@app.com')->first();

        if (!$systemUser) throw new Exception('System user not found, its needed for creating the history record');

        Auth::loginUsingId($systemUser->id);

        foreach ($this->csvData as $data) {

            // call model for the import..
        }

    }

    private function parseUsd($number)
    {

        if (empty( $number )) {
            return null;
        }

        $number = str_replace(['$',',','-'], '', $number);

        return filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    }

    private function readCSV()
    {
        if ( ! file_exists($this->csvPath) || ! is_readable($this->csvPath)) {
            throw new Exception('Can not open CSV file');
        }

        $this->csvData = array_map('str_getcsv', file($this->csvPath));

    }
}
