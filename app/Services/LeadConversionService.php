<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Client;
use App\Models\ClientObjective;
use Illuminate\Support\Facades\DB;

class LeadConversionService
{
    public static function convertIfRequired(Lead $lead, string $newStatus): void
    {
// Log::info($lead);
        // Only when converting
        if ($newStatus !== 'converted') {
            return;
        }

        // Already converted
        if ($lead->client_id) {
            return;
        }

        DB::transaction(function () use ($lead) {

            /* ============================
               2️⃣ CREATE CLIENT IF NOT FOUND
            ============================ */

            // if (!$client) {
                $client = Client::create([
                    'client_name'    => $lead->name,
                    'contact_person' => $lead->name,
                    'email'          => $lead->email,
                    'phone'          => $lead->phone,
                    'created_by'     => auth()->id(),
                ]);
            // }

            /* ============================
               3️⃣ LINK CLIENT TO LEAD
            ============================ */

            $lead->update([
                'client_id'    => $client->id,
                'converted_at' => now(), // optional but recommended
            ]);

            /* ============================
               4️⃣ CLIENT OBJECTIVE
            ============================ */

            // if ($lead->objective_manager_id) {
            //     ClientObjective::firstOrCreate(
            //         [
            //             'client_id'             => $client->id,
            //             'objective_manager_id'  => $lead->objective_manager_id,
            //         ],
            //         [
            //             'status'     => '1',
            //             'created_by' => auth()->id(),
            //         ]
            //     );
            // }
        });
    }
}
