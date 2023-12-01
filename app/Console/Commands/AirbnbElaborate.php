<?php

namespace App\Console\Commands;

use App\Models\Room;
use DOMDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class AirbnbElaborate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'airbnb:elaborate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        Room::with('hosts', 'amenities', 'images')->chunkById(100, function ($rooms) {
            foreach ($rooms as $room) {
                $response = Http::get('https://www.airbnb.co.uk/rooms/' . $room->airbnb_id);
                if ($response->status() == 200) {

                    // $d = new DOMDocument();
                    // @$d->loadHTML($response->body());
                    // $new_title = $d->getElementsByTagName('h1')[0]->nodeValue;
                    // $this->info(print_r($new_title, 1));
                    // $this->info($room->name . ' - ' .  $new_title . ($room->name !== $new_title ? ' - DIFFERENT!' : ''));
                    // // $room->name = $new_title;
                    // // $room->save();

                    // $this->info($response->body());
                    $this->info($room->name . ' is good! - https://www.airbnb.co.uk/rooms/' . $room->airbnb_id);
                } else {
                    $this->error('Delete ' . $room->name . ' - https://www.airbnb.co.uk/rooms/' . $room->airbnb_id);
                    $room->images()->delete();
                    $room->amenities()->detach();
                    $room->hosts()->detach();
                    $room->forceDelete();
                }
            }
        });
    }
}
