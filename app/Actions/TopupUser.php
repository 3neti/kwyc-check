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
        $user = User::fromMobile($data['mobile']);
        $amount = $data['amount'];

        return $this->handle($user, $amount);
    }

    public function asJob(User $user, float $amount): void
    {
        $this->handle($user, $amount);
    }
}
