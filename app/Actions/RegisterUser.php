<?php

namespace App\Actions;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\Organization;
use App\Models\User;

class RegisterUser
{
    use AsAction;

    public function handle(Organization $organization, array $attribs): User
    {
        $user = User::create($attribs);//TODO: change this findOrCreate
        $user->organizations()->attach($organization);

        return $user;
    }

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required'],
            'mobile' => ['required'],
            'password' => ['required'],
        ];
    }

    public function asController(Organization $org, ActionRequest $request): User
    {
        return $this->handle($org, $request->all());
    }
}
