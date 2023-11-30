<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Console\Command;
use League\Csv\Reader;


class AirbnbImportNY extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'airbnb:import-ny';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from AirBnb data'; // https://www.kaggle.com/datasets/thedevastator/airbnbs-nyc-overview

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $csv = Reader::createFromPath(storage_path('app/ny_airbnb.csv'), 'r');
        $csv->setHeaderOffset(0); // so we have the array key inside $data as the header
        $records = $csv->getRecords(); // get all records

        foreach ($records as $record) {
            if ($record['name'] != '') {
                $this->info(print_r($record, 1));

                $neighbourhood_group = City::firstOrCreate(
                    [
                        'name' => $record['neighbourhood_group'],
                    ],
                    [
                        'name' => $record['neighbourhood_group'],
                    ]
                );

                $neighbourhood = City::updateOrCreate(
                    [
                        'name' => $record['neighbourhood'],
                    ],
                    [
                        'name' => $record['neighbourhood'],
                        'parent' => $neighbourhood_group->id,
                    ]
                );

                $room_type = RoomType::firstOrCreate(
                    [
                        'name' => $record['room_type'],
                    ],
                    [
                        'name' => $record['room_type'],
                    ]
                );

                Room::updateOrCreate(
                    [
                        'airbnb_id' => $record['id'],
                    ],
                    [
                        // 'user_id' => 1,
                        'airbnb_id' => $record['id'],
                        'name' => $record['name'],
                        'airbnb_host_id' => $record['host_id'],
                        'latitude' => $record['latitude'],
                        'longitude' => $record['longitude'],
                        'type_id' => $room_type->id,
                        'city_id' => $neighbourhood->id,
                    ]
                );
            }
        }
    }
}
