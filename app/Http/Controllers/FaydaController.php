<?php

namespace App\Http\Controllers;

use App\Services\FaydaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaydaController extends Controller
{
    public function __construct(protected FaydaService $faydaService) {}

    public function redirect()
    {
        return redirect($this->faydaService->getAuthorizationUrl());
    }

    public function callback(Request $request)
    {
        if ($request->state !== session('fayda_state')) {
            return redirect()->route('fayda.status')
                ->with('error', 'Invalid state. Please try again.');
        }

        if ($request->has('error')) {
            return redirect()->route('fayda.status')
                ->with('error', 'Fayda verification was cancelled.');
        }

        $tokenData = $this->faydaService->getAccessToken($request->code);

        if (!isset($tokenData['access_token'])) {
            return redirect()->route('fayda.status')
                ->with('error', 'Failed to get access token from Fayda.');
        }

        $userInfo = $this->faydaService->getUserInfo($tokenData['access_token']);

        $fin = $userInfo['sub'] ?? $userInfo['national_id'] ?? null;

        if (!$fin) {
            return redirect()->route('fayda.status')
                ->with('error', 'Could not retrieve FIN from Fayda.');
        }

        Auth::user()->update([
            'fayda_fin'         => $fin,
            'fayda_verified'    => true,
            'fayda_verified_at' => now(),
        ]);

        return redirect()->route('fayda.status')
            ->with('success', '✅ Fayda ID verified successfully! Your FIN has been saved.');
    }

    public function status()
    {
        return view('fayda.status');
    }

    public function mockPage(Request $request)
    {
        return view('fayda.mock', ['state' => $request->state]);
    }

    public function mockLogin(Request $request)
    {
        $code = 'mock_code_' . \Illuminate\Support\Str::random(20);

        return redirect()->route('fayda.callback', [
            'code'  => $code,
            'state' => $request->state,
        ]);
    }
}