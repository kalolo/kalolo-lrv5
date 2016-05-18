<?php

namespace Klrv\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class SetupConfig extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'klrv:setup-config
    {config : Configuration file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup configuration file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {

        if ( config(array_get($_SERVER, 'argv.2'))) {
            $this->signature .= PHP_EOL;
            foreach (config(array_get($_SERVER, 'argv.2')) as $key => $currentValue) {
                $this->signature .= "{--{$key}= : (optional)}" . PHP_EOL;
            }
        }

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('[+] Config file setup');

        $config = $this->argument('config');

        if (config($config)) {

            $data = [];

            foreach (config(array_get($_SERVER, 'argv.2')) as $key => $currentValue) {
                $this->info($key.' '.$currentValue.' newvalue:' .$this->option($key));

                $newValue = ( $this->option($key) ) ? $this->option($key) : $currentValue;

                $data[$key] = $newValue;
            }

            $filesystem = new \Illuminate\Filesystem\Filesystem();
            $filesystem->put('config/'.$config.'.php', '<?php return '.var_export($data,true).';');

        } else {
            $this->error('Unable to find the given configuration file');
        }


    }
}
