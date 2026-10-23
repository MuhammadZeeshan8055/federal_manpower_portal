<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Support\PaymentReceipts;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentReceiptController extends Controller
{
    public function print(Payment $payment)
    {
        $user = auth()->user();

        if (! $user || ! PaymentReceipts::userCanAccess($user)) {
            abort(403);
        }

        $payment->load(['client', 'method', 'account', 'recorder']);

        $logo = $this->imageAsBase64('images/federal-group-logo.png');

        $pdf = Pdf::loadView('pdfs.payment-receipt', [
            'payment' => $payment,
            'receiptNo' => 'RCP-'.str_pad((string) $payment->id, 5, '0', STR_PAD_LEFT),
            'logo' => $logo,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('receipt-'.$payment->id.'.pdf');
    }

    private function imageAsBase64(string $relativePath): ?string
    {
        $fullPath = public_path($relativePath);

        if (! is_file($fullPath)) {
            return null;
        }

        $mime = mime_content_type($fullPath) ?: 'image/png';
        $bytes = file_get_contents($fullPath);

        if ($bytes === false) {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode($bytes);
    }
}
