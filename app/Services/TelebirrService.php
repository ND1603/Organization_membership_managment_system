<?php

namespace App\Services;

use Illuminate\Support\Str;

class TelebirrService
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('services.telebirr');
    }

    /**
     * In production this would call the real Telebirr API.
     * For now we simulate the response so we can test the full flow.
     */
    public function createPayment(string $outTradeNo, float $amount, string $description): array
    {
        // MOCK MODE — simulate what Telebirr API would return
        // In production, replace this entire method body with the real API call
        if (config('app.env') === 'local') {
            return [
                'code' => '0',
                'msg'  => 'success',
                'data' => [
                    // This is the URL we'd normally redirect the member to on Telebirr's site
                    // In mock mode we redirect to our own simulation page
                    'toPayUrl' => route('telebirr.mock', [
                        'out_trade_no' => $outTradeNo,
                        'amount'       => $amount,
                    ]),
                ],
            ];
        }

        // PRODUCTION — real Telebirr API call goes here
        // (uncomment and fill in when you have real credentials)
        /*
        $timestamp = now()->valueOf();
        $nonce     = Str::random(32);

        $rawRequest = [
            'appId'          => $this->config['app_id'],
            'shortCode'      => $this->config['short_code'],
            'outTradeNo'     => $outTradeNo,
            'subject'        => $description,
            'totalAmount'    => number_format($amount, 2, '.', ''),
            'timeoutExpress' => '30',
            'notifyUrl'      => $this->config['notify_url'],
            'returnUrl'      => $this->config['return_url'],
            'nonce'          => $nonce,
            'timestamp'      => $timestamp,
        ];

        $encryptedRequest = $this->encrypt(json_encode($rawRequest));

        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->post($this->config['base_url'] . '/payment/index/h5pay', [
                'appid' => $this->config['app_id'],
                'sign'  => $this->sign(['appId' => $this->config['app_id'], 'nonce' => $nonce, 'timestamp' => $timestamp]),
                'ussd'  => $encryptedRequest,
            ]);

        return $response->json();
        */

        return ['code' => '-1', 'msg' => 'Production mode not configured'];
    }

    /**
     * Verify the signature on Telebirr's notify callback.
     * In mock mode we skip signature verification.
     */
    public function verifyNotify(array $data): bool
    {
        if (config('app.env') === 'local') {
            return true; // skip verification in mock mode
        }

        // Production signature verification goes here
        return false;
    }
}