<?php

namespace App\Http\Controllers;

use App\Models\TelebirrPayment;
use App\Services\TelebirrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TelebirrController extends Controller
{
    public function __construct(protected TelebirrService $telebirrService) {}

    /**
     * Show the payment initiation form.
     */
    public function create()
    {
        $payments = TelebirrPayment::where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        return view('telebirr.create', compact('payments'));
    }

    /**
     * Initiate a Telebirr payment.
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'amount'      => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        // Generate a unique order ID
        $outTradeNo = 'TLB-' . strtoupper(Str::random(12));

        // Create a pending payment record
        $payment = TelebirrPayment::create([
            'user_id'     => Auth::id(),
            'out_trade_no'=> $outTradeNo,
            'amount'      => $request->amount,
            'description' => $request->description ?? 'Membership Payment',
            'status'      => 'pending',
        ]);

        // Call Telebirr API (mocked in local env)
        $result = $this->telebirrService->createPayment(
            $outTradeNo,
            $request->amount,
            $payment->description
        );

        if (isset($result['data']['toPayUrl'])) {
            // Save the pay URL
            $payment->update(['pay_url' => $result['data']['toPayUrl']]);

            // Redirect member to Telebirr payment page
            return redirect($result['data']['toPayUrl']);
        }

        // If API call failed
        $payment->update(['status' => 'failed']);
        return back()->with('error', 'Could not initiate Telebirr payment. Please try again.');
    }

    /**
     * MOCK ONLY — Simulates the Telebirr payment page.
     * In production this page would be on Telebirr's servers.
     */
    public function mockPage(Request $request)
    {
        return view('telebirr.mock', [
            'outTradeNo' => $request->out_trade_no,
            'amount'     => $request->amount,
        ]);
    }

    /**
     * MOCK ONLY — Simulate a successful payment.
     */
    public function mockPay(Request $request)
    {
        $payment = TelebirrPayment::where('out_trade_no', $request->out_trade_no)->firstOrFail();

        $payment->update([
            'status'   => 'paid',
            'trade_no' => 'MOCK-' . strtoupper(Str::random(10)),
            'msisdn'   => $request->phone ?? '+251900000000',
            'paid_at'  => now(),
        ]);

        return redirect()->route('telebirr.return', ['outTradeNo' => $payment->out_trade_no]);
    }

    /**
     * Telebirr notifies this URL when a real payment completes.
     * Must be CSRF exempt.
     */
    public function notify(Request $request)
    {
        $data = $request->all();

        if (!$this->telebirrService->verifyNotify($data)) {
            return response('SIGNATURE_FAILED', 400);
        }

        $payment = TelebirrPayment::where('out_trade_no', $data['outTradeNo'] ?? '')->first();

        if ($payment && $payment->isPending()) {
            $payment->update([
                'status'   => 'paid',
                'trade_no' => $data['tradeNo'] ?? null,
                'msisdn'   => $data['msisdn'] ?? null,
                'paid_at'  => now(),
            ]);
        }

        return response('SUCCESS', 200);
    }

    /**
     * Member is redirected here after paying on Telebirr.
     */
    public function returnUrl(Request $request)
    {
        $payment = TelebirrPayment::where('out_trade_no', $request->outTradeNo)->first();

        if ($payment && $payment->isPaid()) {
            return redirect()->route('telebirr.create')
                ->with('success', '✅ Payment of ETB ' . number_format($payment->amount, 2) . ' completed successfully! Transaction: ' . $payment->trade_no);
        }

        return redirect()->route('telebirr.create')
            ->with('info', 'Payment is being processed. You will be notified shortly.');
    }
}