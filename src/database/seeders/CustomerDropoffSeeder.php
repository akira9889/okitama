<?php

namespace Database\Seeders;

use App\Models\Dropoff;
use Illuminate\Database\Seeder;

class CustomerDropoffSeeder extends Seeder
{
    public function run()
    {
        $customers = \DB::table('customers')->select('id')->get()->toArray();
        $totalRecords = count($customers);
        $batchSize = 1000;
        $batches = ceil($totalRecords / $batchSize);

        $dropoffTotal = Dropoff::count();

        for ($batch = 1; $batch <= $batches; $batch++) {
            $records = [];
            $remainingRecords = $totalRecords - (($batch - 1) * $batchSize);
            $currentBatchSize = min($batchSize, $remainingRecords);

            for ($i = 0; $i < $currentBatchSize; $i++) {
                $customer = array_shift($customers);

                $dropoffCount = rand(0, $dropoffTotal);

                if (!$dropoffCount) {
                    continue;
                }

                $dropoffIds = Dropoff::inRandomOrder()->take($dropoffCount)->get()->pluck('id')->toArray();

                foreach ($dropoffIds as $dropoffId) {
                    $records[] = [
                        'customer_id' => $customer->id,
                        'dropoff_id' => $dropoffId,
                    ];
                }
            }

            \DB::table('customer_dropoff')->insert($records);
        }
    }
}
