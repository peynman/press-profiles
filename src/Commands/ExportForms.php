<?php

namespace Larapress\Profiles\Commands;

use Illuminate\Console\Command;
use Larapress\Profiles\Flags\UserDomainFlags;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Form;

class ExportForms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lp:profiles:export-forms {path?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export forms.';

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
     * @return int
     */
    public function handle()
    {
        $filepath = $this->argument('path');
        if (is_null($filepath)) {
            if (!file_exists(storage_path('json'))) {
                mkdir(storage_path('json'));
            }
            $filepath = storage_path('/json/forms.json');
        }

        file_put_contents($filepath, json_encode(Form::all(), JSON_PRETTY_PRINT));
        $this->info('Forms exported to path: '.$filepath.'.');

        return 0;
    }
}
