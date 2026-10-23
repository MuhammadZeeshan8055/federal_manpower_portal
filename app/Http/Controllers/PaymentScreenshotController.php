<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Support\PaymentReceipts;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentScreenshotController extends Controller
{
    public function show(Payment $payment): StreamedResponse|Response
    {
        $user = auth()->user();

        if (! $user || ! PaymentReceipts::userCanAccess($user)) {
            abort(403);
        }

        $path = $payment->screenshot_path;

        if (! PaymentReceipts::pathBelongsToPayment($payment->id, $path)) {
            abort(404);
        }

        if (! PaymentReceipts::exists($path)) {
            abort(404);
        }

        return Storage::disk(PaymentReceipts::disk())->response($path, basename($path));
    }
}
