<?php

namespace App\Http\Controllers;

use App\Models\PaymentVerification;
use App\Services\OcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentVerificationController extends Controller
{
    protected OcrService $ocrService;

    public function __construct(OcrService $ocrService)
    {
        $this->ocrService = $ocrService;
        
    }

    /**
     * Show the upload form (member view)
     */
    public function create()
    {
        // Get this user's previous submissions
        $submissions = PaymentVerification::where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        return view('payment_verification.create', compact('submissions'));
    }

    /**
     * Handle the screenshot upload
     */
    public function store(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'screenshot' => [
                'required',
                'image',                        // must be an image
                'mimes:jpeg,jpg,png,webp',      // accepted formats
                'max:5120',                     // max 5MB
            ],
        ], [
            'screenshot.required' => 'Please upload a screenshot of your payment.',
            'screenshot.image'    => 'The file must be an image.',
            'screenshot.mimes'    => 'Only JPEG, PNG, or WebP images are accepted.',
            'screenshot.max'      => 'Image must be smaller than 5MB.',
        ]);

        // Save the image to storage/app/public/payment_screenshots/
        $path = $request->file('screenshot')->store('payment_screenshots', 'public');

        // Get the full path on disk for Tesseract to read
        $fullPath = Storage::disk('public')->path($path);

        // Run OCR
        $extracted = $this->ocrService->processPaymentScreenshot($fullPath);

        // Save to database
        $verification = PaymentVerification::create([
            'user_id'                    => Auth::id(),
            'screenshot_path'            => $path,
            'extracted_transaction_id'   => $extracted['transaction_id'],
            'extracted_amount'           => $extracted['amount'],
            'extracted_date'             => $extracted['date'],
            'raw_ocr_text'               => $extracted['raw_text'],
            'status'                     => 'pending',
        ]);

        return redirect()->route('payment-verification.show', $verification)
            ->with('success', 'Screenshot uploaded successfully! Your payment is being reviewed.');
    }

    /**
     * Show a single verification result to the member
     */
    public function show(PaymentVerification $paymentVerification)
    {
        // Security: members can only see their own submissions
        if ($paymentVerification->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        return view('payment_verification.show', compact('paymentVerification'));
    }

    /**
     * Admin: list all pending verifications
     */
    public function adminIndex()
    {
        $verifications = PaymentVerification::with('user')
            ->latest()
            ->paginate(20);

        return view('payment_verification.admin.index', compact('verifications'));
    }

    /**
     * Admin: approve a payment
     */
    public function approve(PaymentVerification $paymentVerification)
{
    // Generate a unique invoice number
    $invoiceNumber = 'INV-' . now()->format('Y') . '-' . str_pad($paymentVerification->id, 5, '0', STR_PAD_LEFT);

    // Generate the PDF invoice
    $pdf = Pdf::loadView('payment_verification.invoice', [
        'verification' => $paymentVerification,
        'invoiceNumber' => $invoiceNumber,
    ]);

    // Store the PDF in storage/app/private/invoices/
    $invoicePath = 'invoices/' . $invoiceNumber . '.pdf';
    Storage::disk('local')->put($invoicePath, $pdf->output());

    // Update the payment record
    $paymentVerification->update([
        'status'         => 'approved',
        'verified_by'    => Auth::id(),
        'verified_at'    => now(),
        'invoice_number' => $invoiceNumber,
        'invoice_path'   => $invoicePath,
    ]);

    return back()->with('success', 'Payment approved. Invoice ' . $invoiceNumber . ' generated.');
}

    /**
 * Download the invoice PDF for an approved payment.
 */
public function downloadInvoice(PaymentVerification $paymentVerification)
{
    // Only the member who owns it or an admin can download
    if ($paymentVerification->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
        abort(403, 'Unauthorized');
    }

    if (!$paymentVerification->invoice_path || !Storage::disk('local')->exists($paymentVerification->invoice_path)) {
        return back()->with('error', 'Invoice not found.');
    }

    return Storage::disk('local')->download(
        $paymentVerification->invoice_path,
        $paymentVerification->invoice_number . '.pdf'
    );
}   


    /**
     * Admin: reject a payment
     */
    public function reject(Request $request, PaymentVerification $paymentVerification)
    {
        $request->validate(['admin_note' => 'required|string|max:500']);

        $paymentVerification->update([
            'status'      => 'rejected',
            'admin_note'  => $request->admin_note,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return back()->with('error', 'Payment rejected.');
    }
}