<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Rooms Index
     * @group Rooms
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $entities = Room::orderBy('id', 'DESC')->paginate(50, ['*'], 'p', request()->get('p', 1));

        if ($entities) {
            return response()->json(['entities' => $entities], 200);
        }
        return response()->json(['message' => 'No entities available'], 404);
    }

    /**
     * Room Show
     * @group Rooms
     * @authenticated
     *
     * @urlParam room_slug numeric required Example: best-room-ever
     */
    public function show(Request $request): JsonResponse
    {
        $room = Room::with('type', 'amenities')->where('slug', $request->route('room_slug'))->first();
        if ($room) {
            return response()->json(['room' => $room], 200);
        }
        return response()->json(['message' => 'Room not found'], 404);
    }
}
