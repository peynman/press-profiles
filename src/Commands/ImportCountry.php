<?php

namespace Larapress\Profiles\Commands;

use Illuminate\Console\Command;
use Larapress\Profiles\Models\Filter;

class ImportCountry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lp:profiles:import-countries {path?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import country from json.';

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
            $filepath = storage_path('/json/countries.json');
        }

        $countries = json_decode(file_get_contents($filepath), true);

        $indexer = 0;
        foreach ($countries as $country) {
            Filter::updateOrCreate([
                'name' => 'country-' . $indexer,
                'type' => 'country',
                'author_id' => 1,
            ], [
                'data' => [
                    'title' => $country['name'],
                ],
                'zorder' => $indexer,
            ]);

            $pindexer = 0;
            foreach ($country['provinces'] as $province) {
                Filter::updateOrCreate([
                    'name' => 'province-' . $indexer . '-' . $pindexer,
                    'type' => 'province',
                    'author_id' => 1,
                ], [
                    'data' => [
                        'title' => $province['name'],
                    ],
                    'zorder' => $pindexer,
                ]);

                $cindexer = 0;
                foreach ($province['cities'] as $city) {
                    Filter::updateOrCreate([
                        'name' => 'city-' . $indexer . '-' . $pindexer . '-' . $cindexer,
                        'type' => 'city',
                        'author_id' => 1,
                    ], [
                        'data' => [
                            'title' => $city,
                        ],
                        'zorder' => $cindexer,
                    ]);
                    $cindexer++;
                }

                $pindexer++;
            }

            $indexer++;
        }

        $this->info('Forms imported.');

        return 0;
    }
}
