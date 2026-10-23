<?php

namespace App\Http\Controllers;

use App\Models\SalarySlip;
use Barryvdh\DomPDF\Facade\Pdf;

class SalarySlipPrintController extends Controller
{
    public function print(SalarySlip $salarySlip)
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('attendance', 'salary-slips') && ! $user->canManage('attendance', 'salary-slips'))) {
            abort(403);
        }

        $salarySlip->load(['user', 'generator']);

        $logo = $this->imageAsBase64('images/federal-group-logo.png');

        $pdf = Pdf::loadView('pdfs.salary-slip', [
            'slip' => $salarySlip,
            'slipNo' => 'SLP-'.str_pad((string) $salarySlip->id, 5, '0', STR_PAD_LEFT),
            'logo' => $logo,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('salary-slip-'.$salarySlip->id.'.pdf');
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
