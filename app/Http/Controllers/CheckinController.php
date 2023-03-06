<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Propaganistas\LaravelPhone\Rules\Phone;
use Illuminate\Http\Request;
use App\Actions\NewCheckin;
use App\Models\Checkin;
use Inertia\Response;
use Inertia\Inertia;

class CheckinController extends Controller
{
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mobile' => ['nullable', (new Phone)->mobile()->country('PH')]
        ]);

        NewCheckin::run(auth()->user(), Arr::get($validated, 'mobile', null));

        return redirect(route('checkins.index'));
    }
}
