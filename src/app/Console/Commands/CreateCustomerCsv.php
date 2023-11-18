<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateCustomerCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:customer-csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a CSV file for customers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = fopen('customers.csv', 'w');

        $dropoffs = ['玄関', '車庫', '自転車', 'その他'];
        // 生成するレコード数
        $totalRecords = 1000;

        for ($i = 0; $i < $totalRecords; $i++) {
            $record = \App\Models\Customer::factory()->make();

            $town = \App\Models\Town::find($record->town_id);

            // ランダムに1~2個の置き配場所を選択
            $randomKeys = array_rand($dropoffs, rand(1, 2));
            $selectedDropoffs = implode('、', is_array($randomKeys) ? array_intersect_key($dropoffs, array_flip($randomKeys)) : [$dropoffs[$randomKeys]]);

            $line = $town->city->prefecture->name . ',' . $town->city->name . ',' . $town->name . ',' . $record->address_number . ',' . $record->room_number . ',' . $record->last_name . ',' . $record->first_name . ',' . $selectedDropoffs . ',' . $record->description . PHP_EOL;

            fwrite($file, $line);
        }

        fclose($file);
        $this->info('CSV file has been generated.');
    }
}
