<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Crypt;
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
        $attribs = config('domain.seed.user.system');
//        foreach ($attribs as $key => $value) {
//            $attribs[$key] = decrypt($value);
//        }

        tap(app(CreateNewUser::class)->create($attribs), function (User $system) use ($attribs) {
//            $system->mobile = $attribs['mobile'];
//            $system->save();
            $system->depositFloat(config('domain.default.system.deposit'));
        });
    }
}
