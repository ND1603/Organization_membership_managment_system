<?php

namespace App\Services;

use Illuminate\Support\Str;

class FaydaService
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('services.fayda');
    }

    /**
     * Build the URL to redirect the member to Fayda login.
     * In production this goes to the real Fayda OIDC endpoint.
     * In mock mode we redirect to our own simulation page.
     */
    public function getAuthorizationUrl(): string
    {
        $state = Str::random(40);
        session(['fayda_state' => $state]);

        if (config('app.env') === 'local') {
            // Mock mode — redirect to our own fake Fayda page
            return route('fayda.mock', ['state' => $state]);
        }

        // Production — real Fayda OIDC URL
        $params = http_build_query([
            'response_type' => 'code',
            'client_id'     => $this->config['client_id'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'scope'         => 'openid profile national_id',
            'state'         => $state,
        ]);

        return $this->config['base_url'] . '/oauth2/authorize?' . $params;
    }

    /**
     * Exchange authorization code for access token.
     * Mocked in local mode.
     */
    public function getAccessToken(string $code): array
    {
        if (config('app.env') === 'local') {
            return ['access_token' => 'mock_access_token_' . $code];
        }

        $response = \Illuminate\Support\Facades\Http::asForm()
            ->post($this->config['base_url'] . '/oauth2/token', [
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => $this->config['redirect_uri'],
                'client_id'     => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
            ]);

        return $response->json();
    }

    /**
     * Get the member's profile from Fayda using the access token.
     * Returns their FIN (national ID number).
     * Mocked in local mode.
     */
    public function getUserInfo(string $accessToken): array
    {
        if (config('app.env') === 'local') {
            // Return a fake FIN for testing
            return [
                'sub'         => 'ETH-FIN-' . strtoupper(Str::random(8)),
                'name'        => 'Mock Fayda User',
                'national_id' => 'ETH' . rand(10000000, 99999999),
            ];
        }

        $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->get($this->config['base_url'] . '/oauth2/userinfo');

        return $response->json();
    }
}