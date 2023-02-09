<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Helpers\DataHelper;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $packages = DataHelper::packages();

        foreach ($packages as $package) {
            $data = [];
            $data['code'] = $package['code'];
            $data['name'] = $package['name'];
            $data['price'] = $package['price'];

            DB::table('packages')->insert($data);
        }
    }
}
