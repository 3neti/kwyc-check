<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Actions\RegisterOrganization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Enums\ChannelEnum;
use App\Enums\FormatEnum;
use App\Models\Package;
use Inertia\Response;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Organizations/Index', [
            'organizations' => Organization::with('admin:id,name')
                ->where('admin_id', auth()->id())
                ->latest()->get(),
            'channels' => ChannelEnum::values(),
            'formats' => FormatEnum::values(),
            'pkgs' => Package::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required'],
            'channel' => ['required', new Enum(ChannelEnum::class)],
            'format' => ['required', new Enum(FormatEnum::class)],
            'address' => ['required'],
            'command' => ['required'],
            'pkg' => ['required', 'array']
        ]);

        $name = $channel = $format = $address = $command = $pkg = null;

        extract($validated);

        $channel = ChannelEnum::from($channel);
        $format = FormatEnum::from($format);
        $pkg = Package::where(['code' => $pkg['code']])->first();

        RegisterOrganization::run($request->user(), $name, $channel, $format, $address, $command, $pkg);

        return redirect(route('organizations.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function edit(Organization $organization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organization $organization)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization)
    {
        //
    }
}
