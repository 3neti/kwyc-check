<?php

namespace App\Actions;

use App\Notifications\RegisteredOrganizationNotification;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\ActionRequest;
use Illuminate\Validation\Rules\Enum;
use MOIREI\Vouchers\VoucherScheme;
use App\Models\Organization;
use App\Models\Repository;
use App\Enums\ChannelEnum;
use App\Enums\FormatEnum;
use App\Models\Campaign;
use App\Models\Package;
use App\Models\Voucher;
use App\Models\User;
use Carbon\Carbon;

class RegisterOrganization
{
    use AsAction;

    public function handle(User $admin, string $name, ChannelEnum $channel, FormatEnum $format, string $address, string $command, Package $package): Voucher
    {
        $campaign = $this->newCampaign($package, $this->newRepository(
            $this->newOrganization($admin, $name),
            compact( 'channel', 'format', 'address', 'command')
        ));
        $voucher = $campaign->createVoucher($this->getAttributes());
        $admin->notify(new RegisteredOrganizationNotification($voucher));

        return $voucher;
    }

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'channel' => ['required', new Enum(ChannelEnum::class)],
            'format' => ['required', new Enum(FormatEnum::class)],
            'address' => ['required'],
            'command' => ['required'],
            'package' => ['required', 'exists:packages,code']
        ];
    }

    public function asController(ActionRequest $request): Voucher
    {
        $data = $request->all();
        $data['channel'] = ChannelEnum::from($data['channel']);
        $data['format'] = FormatEnum::from($data['format']);
        $data['package'] = Package::where(['code' => $data['package']])->first();

        return $this->handle($request->user(), ...$data);
    }

    protected function newOrganization(User $user, $name): Organization
    {
        $organization = Organization::make([]);
        $organization->name = $name;
        $organization->admin()->associate($user);
        $organization->save();

        return $organization;
    }

    protected function newRepository(Organization $organization, $attribs): Repository
    {
        $channel = null; $format = null; $address = null; $command = null; extract($attribs);
        $date = Carbon::today()->format('Y-m-d');
        $name = $organization->name . " [{$channel->value}][{$format->value}][{$date}]";
        $repository = Repository::make(compact('name', 'channel', 'format', 'address', 'command'));
        $repository->organization()->associate($organization);
        $repository->save();

        return $repository;
    }

    protected function newCampaign(Package $package, Repository $repository): Campaign
    {
        return tap(Campaign::make(), function ($campaign) use ($package, $repository) {
            $campaign->package()->associate($package);
            $campaign->repository()->associate($repository);
            $campaign->save();
        });
    }

    protected function getAttributes()
    {
        return [
            'limit_scheme' => VoucherScheme::REDEEMER,
            'quantity' => 1,
            'data' => [
                'brothers' => [
                    'Dene' => ['birthdate' => 'April 1, 1971'],
                    'Glen' => ['birthdate' => 'October 29, 1972'],
                ],
                'sisters' => [
                    'Jo Anna' => ['birthdate' => 'March 5, 1974'],
                    'Rowena' => ['birthdate' => 'April 18, 1975'],
                ],
            ],
        ];
    }
}
