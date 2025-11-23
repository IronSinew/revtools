<?php

namespace Database\Seeders;

use App\Enums\Regions\RoomColor;
use App\Enums\Regions\RoomExitType;
use App\Models\Region;
use App\Models\Room;
use App\ValueObjects\Regions\RoomObject;
use App\ValueObjects\Regions\RoomPosition;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('app/data/WorldData.json');
        $now = now();

        foreach (JsonParser::parse($file) as $key => $value) {
            if ($key !== 'Zones') {
                continue;
            }

            foreach ($value as $zoneKey => $rawZone) {
                if (empty($rawZone['Rooms'])) {
                    continue;
                }

                $region = Region::where('name', $rawZone['ZoneName'])->first();

                foreach ($rawZone['Rooms'] as $rawRoom) {
                    $flagArray = collect(str_split($rawRoom['RawExitFlags']));

                    if (count($flagArray) === count(RoomExitType::cases())) {
                        $roomExitFlags = $flagArray->map(function ($flag, $key) {
                            if ($flag === 'y') {
                                return RoomExitType::fromCrapValue($key);
                            }

                            return null;
                        })->filter(fn ($flag) => ! is_null($flag))->values();
                    }

                    $npcs = collect($rawRoom['Npcs'] ?? [])->map(function ($npc) {
                        return $npc['NpcName'];
                    });

                    $position = new RoomPosition(
                        x: $rawRoom['X'],
                        y: $rawRoom['Y'],
                        z: $rawRoom['Z'],
                    );

                    $room = new RoomObject(
                        name: $rawRoom['Name'],
                        description: $rawRoom['Description'],
                        color_code: $rawRoom['ColorCode'],
                        terrain_color: RoomColor::fromCrapValue($rawRoom['TerrainColor'])->toColor(),
                        coordinates: $position,
                        region_id: $region->id,
                        exits: $roomExitFlags->toArray() ?? null,
                        npcs: $npcs->toArray() ?? null,
                    );

                    if ($rawRoom['IsZoneExit'] === true) {
                        $room->exit_region_id = Region::where('name', $rawRoom['ExitToZone'])->first()->id ?? null;
                    }

                    $arrayData = collect($room->toArray())
                        ->transform(function (mixed $value) {
                            if (is_array($value)) {
                                return ! empty($value) ? json_encode($value) : null;
                            }

                            return $value;
                        })
                        ->toArray();

                    $arrayData['created_at'] = $now;
                    $arrayData['updated_at'] = $now;

                    $toInsert[] = $arrayData;

                    if (count($toInsert) > 500) {
                        $region->rooms()->insert($toInsert);
                        unset($toInsert);
                        $now = now();
                    }
                }

                if (! empty($toInsert)) {
                    $region->rooms()->insert($toInsert);
                }
            }
        }

        $rooms = Room::all()->groupBy('region_id')->map(function ($rooms) {
            return $rooms->pluck('coordinates', 'id');
        });

        $roomsWithExits = Room::with('region')->whereNotNull(['exit_region_id', 'exits'])->get();

        $batchUpdates = [];
        foreach ($roomsWithExits as $room) {
            $allCoordinates = $rooms[$room->region_id] ?? collect();
            $direction = $this->determineExitDirection($room, $allCoordinates);

            $batchUpdates[] = [
                'id' => $room->id,
                'exit_region_direction' => $direction->value,
            ];

            if (count($batchUpdates) > 500) {
                Room::batchUpdate($batchUpdates, 'id');
                $batchUpdates = [];
            }
        }

        if (! empty($batchUpdates)) {
            Room::batchUpdate($batchUpdates, 'id');
        }

        Region::all()->each(function (Region $region) {
            $connections = $region->rooms()->where('exit_region_id', '!=', null)
                ->pluck('exit_region_id')->unique()->values()->toArray();
            $region->update(['connections' => $connections]);
        });
    }

    private function determineExitDirection(Room $room, Collection $allCoordinates): RoomExitType
    {
        foreach ($room->exits as $exit) {
            $direction = match ($exit) {
                RoomExitType::North => ['x' => $room->coordinates->x, 'y' => $room->coordinates->y + 1, 'z' => $room->coordinates->z],
                RoomExitType::South => ['x' => $room->coordinates->x, 'y' => $room->coordinates->y - 1, 'z' => $room->coordinates->z],
                RoomExitType::East => ['x' => $room->coordinates->x + 1, 'y' => $room->coordinates->y, 'z' => $room->coordinates->z],
                RoomExitType::West => ['x' => $room->coordinates->x - 1, 'y' => $room->coordinates->y, 'z' => $room->coordinates->z],
                RoomExitType::Up => ['x' => $room->coordinates->x, 'y' => $room->coordinates->y, 'z' => $room->coordinates->z + 1],
                RoomExitType::Down => ['x' => $room->coordinates->x, 'y' => $room->coordinates->y, 'z' => $room->coordinates->z - 1],
                default => null
            };

            if ($direction === null) {
                continue;
            }

            $exists = $allCoordinates->contains(function ($coord) use ($direction) {
                return $coord->x === $direction['x'] && $coord->y === $direction['y'] && $coord->z === $direction['z'];
            });

            if (! $exists) {
                return $exit;
            }
        }

        return RoomExitType::Up;
    }
}
