<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerDropoffSeeder extends Seeder
{
    public function run()
    {
        $customers = \DB::table('customers')->select('id')->get()->toArray();
        $totalRecords = count($customers);
        $batchSize = 1000;
        $batches = ceil($totalRecords / $batchSize);

        for ($batch = 1; $batch <= $batches; $batch++) {
            $records = [];
            $remainingRecords = $totalRecords - (($batch - 1) * $batchSize);
            $currentBatchSize = min($batchSize, $remainingRecords);

            for ($i = 0; $i < $currentBatchSize; $i++) {
                $customer = array_shift($customers);

                if (rand(1, 100) <= 1) {
                    continue;
                }

                $record = [
                    'customer_id' => $customer->id,
                    'dropoff_id' => \App\Models\Dropoff::inRandomOrder()->first()->id,
                ];
                $records[] = $record;
            }

            \DB::table('customer_dropoff')->insert($records);
        }
    }
}
