<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Helpers\DataHelper;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organizations = DataHelper::organizations();

        foreach ($organizations as $organization) {
            $data = [];
            $data['name'] = $organization['name'];
            $data['admin_id'] = $organization['admin_id'];

            DB::table('organizations')->insert($data);
        }
    }
}
