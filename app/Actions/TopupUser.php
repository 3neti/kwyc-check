<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\ActionRequest;
use Bavix\Wallet\Models\Wallet;
use App\Models\User;

class TopupUser
{
    use AsAction;

    public function handle(User $user, float $amount): Wallet
    {
        $system = User::getSystem();
        $system->transferFloat($user, $amount);

        return $user->wallet;
    }

    public function rules(): array
    {
        return [
            'mobile' => ['required'],
            'amount' => ['required'],
        ];
    }

    public function asController(ActionRequest $request): Wallet
    {
        $data = $request->all();
        $user = $this->getUserFromMobile($data['mobile']);
        $amount = $data['amount'];

        return $this->handle($user, $amount);
    }

    protected function getUserFromMobile(string $mobile): User
    {
        return User::where(compact('mobile'))->first();
    }
}
