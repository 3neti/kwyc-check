<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Actions\RegisterOrganization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\Models\User;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::getSystem();
        $attribs = config('domain.seed.organization');
        Arr::set( $attribs, 'package', Package::where('code', Arr::get($attribs,'package'))->first());

        RegisterOrganization::run($user, ...$attribs);
    }
}
