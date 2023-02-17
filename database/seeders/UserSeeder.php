<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tap(app(CreateNewUser::class)->create($attrib = config('domain.seed.user.system')), function (User $system) use ($attrib) {
            $system->mobile = $attrib['mobile'];
            $system->save();
            $system->depositFloat(1000000);
        });
    }
}
