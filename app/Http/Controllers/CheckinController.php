<?php

namespace App\Http\Controllers;

use Propaganistas\LaravelPhone\Rules\Phone;
use App\Actions\Checkin\AutoRemoteCheckin;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreCheckin;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Checkin;
use Inertia\Response;
use Inertia\Inertia;

class CheckinController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Checkins/Index', [
            'checkins' => app(Checkin::class)
                ->with('agent:id,name')
                ->with('person:id,mobile,handle')
                ->whereBelongsTo(auth()->user(), 'agent')
                ->latest()
                ->get()
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(StoreCheckin $request): RedirectResponse
    {
        AutoRemoteCheckin::run(auth()->user(), $request->validated('mobile'));

        return redirect(route('checkins.index'));
    }
}
