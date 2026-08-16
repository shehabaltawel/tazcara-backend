<?php

namespace App\Services;

use App\Models\Bus;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Admin Bus Service
 */
class AdminBusService
{
    /**
     * Create a bus and optionally its seats atomically.
     */
    public function create(array $data): Bus
    {
        return DB::transaction(function () use ($data): Bus {
            $bus = Bus::create([
                'class' => $data['class'],
                'plate_number' => $data['plate_number'],
            ]);

            $this->addSeats($bus, $data['seats'] ?? []);

            return $bus->load('seats');
        });
    }

    /**
     * Soft delete the given bus, refusing to remove one still assigned to a trip.
     *
     * @throws ConflictHttpException
     */
    public function delete(Bus $bus): void
    {
        throw_if(
            Trip::where('bus_id', $bus->id)->exists(),
            ConflictHttpException::class,
            'Cannot delete a bus that is assigned to a trip'
        );

        $bus->delete();
    }

    /**
     * Create the given seats for the bus in a fixed number of queries,
     * restoring any soft-deleted seat that already carries the same code.
     */
    private function addSeats(Bus $bus, array $codes): void
    {
        $existingCodes = $bus->seats()->withTrashed()->pluck('code');

        $bus->seats()->onlyTrashed()->whereIn('code', $codes)->restore();

        $bus->seats()->createMany(
            collect($codes)->diff($existingCodes)->map(fn (string $code) => ['code' => $code])
        );
    }
}
