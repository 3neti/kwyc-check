<?php

namespace App\Actions;

use App\Notifications\SendRegisterUserNotification;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\ActionRequest;
use Illuminate\Validation\Rules\Enum;
use App\Models\Organization;
use App\Models\Repository;
use App\Enums\ChannelEnum;
use App\Enums\FormatEnum;
use App\Models\Campaign;
use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;

class RegisterOrganization
{
    use AsAction;

    public function handle(User $user, string $name, ChannelEnum $channel, FormatEnum $format, string $address, string $command, Package $package): Campaign
    {
        $campaign = $this->newCampaign($package, $this->newRepository(
            $this->newOrganization($user, $name),
            compact( 'channel', 'format', 'address', 'command')
        ));
        $user->notify(new SendRegisterUserNotification($campaign));

        return $campaign;
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

    public function asController(ActionRequest $request): Campaign
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
}
