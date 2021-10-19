<?php

namespace Larapress\Profiles\Commands;

use Illuminate\Console\Command;
use Larapress\Profiles\Models\Form;

class ImportForms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lp:profiles:import-forms {path?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import forms.';

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
            $filepath = storage_path('/json/forms.json');
        }

        $types = json_decode(file_get_contents($filepath), true);

        foreach ($types as $type) {
            Form::updateOrCreate([
                'id' => $type['id'],
                'name' => $type['name'],
            ], [
                'data' => $type['data'],
                'author_id' => $type['author_id'],
                'flags' => $type['flags'],
                'created_at' => $type['created_at'],
                'updated_at' => $type['updated_at'],
                'deleted_at' => $type['deleted_at'],
            ]);
            $this->info('Form added with name: '.$type['name'].'.');
        }

        $this->info('Forms imported.');

        return 0;
    }
}
