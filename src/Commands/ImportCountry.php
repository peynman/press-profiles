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

        foreach ($countries as $country) {
            Filter::updateOrCreate([
                'name' => 'country-' . $country['id'],
                'type' => 'country',
                'author_id' => 1,
            ], [
                'data' => [
                    'title' => $country['name'],
                ],
                'zorder' => $country['id'],
            ]);
            $this->info("importing ".$country['name']." ".$country['id']);

            foreach ($country['provinces'] as $pname => $province) {
                Filter::updateOrCreate([
                    'name' => 'province-' . $country['id'] . '-' . $province['id'],
                    'type' => 'province',
                    'author_id' => 1,
                ], [
                    'data' => [
                        'title' => $pname,
                        'country' => $country['name'],
                    ],
                    'zorder' => $province['id'],
                ]);
                $this->info("importing ".$pname." ".$province['id']);

                foreach ($province['cities'] as $city) {
                    Filter::updateOrCreate([
                        'name' => 'province-' . $country['id'] . '-' . $province['id'] . '-' . $city['id'],
                        'type' => 'city',
                        'author_id' => 1,
                    ], [
                        'data' => [
                            'title' => $city['name'],
                            'province' => $pname,
                            'country' => $country['name'],
                        ],
                        'zorder' => $city['id'],
                    ]);
                    $this->info("importing ".$city['name']);
                }
            }
        }

        $this->info('Forms imported.');

        return 0;
    }
}
