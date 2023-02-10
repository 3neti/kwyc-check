<?php

namespace App\Actions;

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

class OrgRegistration
{
    use AsAction;

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

    public function handle(User $user, string $name, ChannelEnum $channel, FormatEnum $format, string $address, string $command, Package $package): Campaign
    {
        $organization = Organization::make([]);
        $organization->name = $name;
        $organization->admin()->associate($user);
        $organization->save();

        $date = Carbon::today()->format('Y-m-d');

        $repository = Repository::make([
            'name' => $organization->name . " [{$channel->value}][{$format->value}][{$date}]",
            'channel' => $channel,
            'format' => $format,
            'address' => $address,
            'command' => $command
        ]);
        $repository->organization()->associate($organization);
        $repository->save();

        $campaign = Campaign::make();
        $campaign->package()->associate($package);
        $campaign->repository()->associate($repository);
        $campaign->save();

        return $campaign;
    }

    public function asController(ActionRequest $request)
    {
        $data = $request->all();
        $data['channel'] = ChannelEnum::from($data['channel']);
        $data['format'] = FormatEnum::from($data['format']);
        $data['package'] = Package::where(['code' => $data['package']])->first();

        return $this->handle($request->user(), ...$data);
    }
}
