<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecruitedAgentRequest;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Http\RedirectResponse;
use App\Models\Organization;
use App\Models\Voucher;
use Inertia\Response;
use Inertia\Inertia;
use App\Models\User;

class RecruitedAgentController extends Controller
{
    /**
     * payload includes campaign (package and repository) and metadata for messaging
     * it should trigger an VoucherRedeemed event
     *
     * @var Voucher
     */
    protected Voucher $voucher;

    /**
     * deep property coming from the voucher->campaign->repository->organization
     * for display purposes in the registration form
     *
     * @var Organization
     */
    protected Organization $organization;

    /**
     * new registered user
     * redeemer of campaign voucher
     *
     * @var User
     */
    protected User $agent;

    /**
     * @param Voucher $voucher
     * @return Response
     */
    public function create(Voucher $voucher): Response
    {
        $this->setOrganization($voucher);

        return Inertia::render('Auth/Recruit', [
            'voucher' => $voucher,
            'organization' => $this->organization
        ]);
    }

    /**
     * @param Voucher $voucher
     * @param StoreRecruitedAgentRequest $request
     * @return RedirectResponse
     */
    public function store(Voucher $voucher, StoreRecruitedAgentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this
            ->persistAgent($validated)
            ->redeemVoucher($voucher)
//            ->loginAgent()
        ;

        return redirect()->route('dashboard');

//        return redirect()->route('dashboard')->with('success', "Account successfully registered.");
    }

    /**
     * @param Voucher $voucher
     * @return $this
     */
    protected function setOrganization(Voucher $voucher): self
    {
        $this->organization = $voucher->campaigns()->first()->repository->organization;

        return $this;
    }

    /**
     * @param $request
     * @return $this
     */
    protected function persistAgent($request): self
    {
        $this->agent = app(CreateNewUser::class)->create($request);

        return $this;
    }

    /**
     * @param Voucher $voucher
     * @return $this
     */
    protected function redeemVoucher(Voucher $voucher): self
    {
        $this->agent->redeem($voucher, $voucher->campaign);

        return $this;
    }

    /**
     * @return $this
     */
    protected function loginAgent(): self
    {
        auth()->login($this->agent);

        return $this;
    }
}
