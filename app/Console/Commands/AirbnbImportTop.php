<?php

namespace App\Console\Commands;

use App\Models\Amenity;
use App\Models\City;
use App\Models\File;
use App\Models\Host;
use App\Models\PropertyType;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use League\Csv\Reader;

class AirbnbImportTop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'airbnb:import-top';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description'; // https://www.kaggle.com/datasets/mexwell/airbnb-listings

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $csv = Reader::createFromPath(storage_path('app/airbnb-listings.csv'), 'r');
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0); // so we have the array key inside $data as the header
        $records = $csv->getRecords(); // get all records
        // $this->info($csv->count() . ' rooms');

        foreach ($records as $key => $record) {
            $this->info(print_r($record, 1));

            if ($record['Name'] != '') { // only with name

                ##################################################### Amenities
                $amenities = explode(',', $record['Amenities']);
                $amenity_ids = [];
                foreach ($amenities as $amenity) {
                    if (strpos($amenity, 'translation missing') === false && $amenity != '') {
                        $tmp_amenity = Amenity::firstOrCreate(['name' => $amenity], ['name' => $amenity]);
                        $amenity_ids[] = $tmp_amenity->id;
                    }
                }

                ############################# Room Type
                $room_type = '';
                if ($record['Room Type'] != '') {
                    $room_type = RoomType::firstOrCreate(['name' => $record['Room Type']], ['name' => $record['Room Type']]);
                }

                ############################ Property Type
                $property_type = '';
                if ($record['Property Type'] != '') {
                    $property_type = PropertyType::firstOrCreate(['name' => $record['Property Type']], ['name' => $record['Property Type']]);
                }

                break;
                // $neighbourhood = null;
                // $city = City::firstOrCreate(['name' => $record['City']], ['name' => $record['City']]);

                // $neighbourhood = City::updateOrCreate(
                //     [
                //         'name' => $record['Neighbourhood Cleansed'],
                //     ],
                //     [
                //         'name' => $record['Neighbourhood Cleansed'],
                //         'parent' => $city->id,
                //     ]
                // );


                ########################### Room
                $room = Room::firstOrCreate(
                    [
                        'airbnb_id' => $record['ID'],
                    ],
                    [
                        // 'user_id' => 1,
                        'airbnb_id' => $record['ID'],
                        'name' => $record['Name'],
                        'description' => $record['Description'],
                        'airbnb_host_id' => $record['Host ID'],
                        'latitude' => ($record['Latitude'] > 90 ? null : $record['Latitude']),
                        'longitude' => ($record['Longitude'] > 180 ? null : $record['Longitude']),
                        'room_type_id' => ($room_type != null ? $room_type->id : null),
                        'property_type_id' => ($property_type != null ? $property_type->id : null),
                        'guests' => ($record['Accommodates'] == '' ? null : (int)$record['Accommodates']),
                        'bedrooms' => ($record['Bedrooms'] == '' ? null : (int)$record['Bedrooms']),
                        'beds' => ($record['Beds'] == '' ? null : (int)$record['Beds']),
                        'bathrooms' => ($record['Bathrooms'] == '' ? null : (int)$record['Bathrooms']),
                        // 'city_id' => ($neighbourhood != null ? $neighbourhood->id : null),
                    ]
                );

                ########################################### Image
                if ($record['XL Picture Url'] != '') {
                    File::updateOrCreate(
                        [
                            'entity_id' => $room->id,
                            'entity_type' => 'room',
                        ],
                        [
                            'entity_id' => $room->id,
                            'entity_type' => 'room',
                            'url' => $record['XL Picture Url'],
                            'alt' => $record['Name'],
                            'filemime' => 'jpg',
                        ]
                    );
                }

                if (count($amenity_ids) > 0) {
                    $room->amenities()->syncWithoutDetaching($amenity_ids);
                }

                ############################################ Host
                $host = Host::updateOrCreate(
                    [
                        'airbnb_host_id' => $record['Host ID'],
                    ],
                    [
                        'airbnb_host_id' => $record['Host ID'],
                        'airbnb_host_name' => $record['Host Name'],
                        'airbnb_host_since' => ($record['Host Since'] == '' ? null : $record['Host Since']),
                        'airbnb_host_description' => $record['Host About'],
                    ]
                );
                $room->hosts()->syncWithoutDetaching($host->id);
                if ($record['Host Picture Url'] != '') {
                    File::updateOrCreate(
                        [
                            'entity_id' => $host->id,
                            'entity_type' => 'host',
                        ],
                        [
                            'entity_id' => $host->id,
                            'entity_type' => 'host',
                            'url' => $record['Host Picture Url'],
                            'alt' => $record['Name'],
                            'filemime' => 'jpg',
                        ]
                    );
                }

                // if ($key == 10) {
                //     break;
                // }
            }
        }
    }
}
