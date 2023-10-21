<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // デフォルトの件数
        $totalRecords = 10000;

        if (env('APP_ENV') === 'test') {
            $totalRecords = 50;
        }

        // バッチサイズは1000件
        $batchSize = 1000;

        // 必要なバッチ数($totalRecordsが$batchSizeより小さくてもバッチ処理を実行するために切り上げる。最小値は1)
        $batches = ceil($totalRecords / $batchSize);

        // バッチ処理のループ
        for ($batch = 1; $batch <= $batches; $batch++) {
            $records = [];

            // 現在のバッチサイズ
            // 全体のレコード数（$totalRecords）がバッチサイズ（$batchSize）で割り切れない場合、最後のバッチのレコード数は$batchSizeよりも少なくなるため。
            $remainingRecords = $totalRecords - (($batch - 1) * $batchSize);
            $currentBatchSize = min($batchSize, $remainingRecords);

            // レコードの生成と挿入
            for ($i = 0; $i < $currentBatchSize; $i++) {
                $record = \App\Models\Customer::factory()->make()->toArray();
                $records[] = $record;
            }

            \DB::table('customers')->insert($records);
        }
    }
}
