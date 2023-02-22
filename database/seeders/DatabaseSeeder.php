<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(PackageSeeder::class);
        $this->call(OrganizationSeeder::class);
//        \LBHurtado\SMS\Facades\SMS::channel("engagespark")
//            ->from('TXTCMDR')
//            ->to('+639173011987')
//            ->content('db:seed')
//            ->send();
    }
}
