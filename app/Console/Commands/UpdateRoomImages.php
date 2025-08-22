<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Room;

class UpdateRoomImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rooms:update-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing rooms with sample images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating room images...');

        $rooms = Room::all();
        $sampleImage = 'room-images/IlRcmO9uTTdNXhXM3e73hDn04qTVOsWvWMaUP5zp.png';

        foreach ($rooms as $room) {
            if (!$room->image) {
                $room->update(['image' => $sampleImage]);
                $this->line("Updated room {$room->number} with image");
            } else {
                $this->line("Room {$room->number} already has an image");
            }
        }

        $this->info('Room images updated successfully!');
    }
}
