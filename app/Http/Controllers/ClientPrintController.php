<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Support\ClientPrivateFiles;
use App\Support\ClientPublicLink;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ClientPrintController extends Controller
{
    public function preview(Client $client)
    {
        $this->mustBeAllowed();

        return view('pdfs.client-biodata', $this->viewData($client));
    }

    public function print(Client $client)
    {
        $this->mustBeAllowed();

        $pdf = Pdf::loadView('pdfs.client-biodata', $this->viewData($client));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('biodata-'.$client->id.'.pdf');
    }

    private function viewData(Client $client): array
    {
        $client->load(['country', 'trade', 'company', 'careOff', 'processStatus']);

        return [
            'client' => $client,
            'logo' => $this->imageAsBase64('images/federal-manpower-logo.png'),
            'mark' => $this->imageAsBase64('images/federal-mark.png'),
            'photo' => $this->clientPhotoAsBase64($client),
            'qr' => ClientPublicLink::qrDataUri($client),
            'iconLocation' => $this->imageAsBase64('images/print/location.svg'),
            'iconGlobe' => $this->imageAsBase64('images/print/globe.svg'),
            'iconLandline' => $this->imageAsBase64('images/print/landline.svg'),
            'iconPhone' => $this->imageAsBase64('images/print/phone.svg'),
            'iconMail' => $this->imageAsBase64('images/print/mail.svg'),
        ];
    }

    private function mustBeAllowed(): void
    {
        $user = auth()->user();

        if (! $user || ! ClientPrivateFiles::userCanAccess($user)) {
            abort(403);
        }
    }

    private function imageAsBase64(string $relativePath): ?string
    {
        $fullPath = public_path($relativePath);

        if (! is_file($fullPath)) {
            return null;
        }

        $mime = mime_content_type($fullPath) ?: 'image/png';
        $bytes = file_get_contents($fullPath);

        return 'data:'.$mime.';base64,'.base64_encode($bytes);
    }

    private function clientPhotoAsBase64(Client $client): ?string
    {
        $path = $client->photo_path;

        if (! ClientPrivateFiles::pathBelongsToClient($client->id, $path)) {
            return null;
        }

        if (! ClientPrivateFiles::exists($path)) {
            return null;
        }

        $bytes = Storage::disk(ClientPrivateFiles::disk())->get($path);

        if (! $bytes) {
            return null;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($ext === 'png') {
            $mime = 'image/png';
        } elseif ($ext === 'webp') {
            $mime = 'image/webp';
        } else {
            $mime = 'image/jpeg';
        }

        return 'data:'.$mime.';base64,'.base64_encode($bytes);
    }
}
