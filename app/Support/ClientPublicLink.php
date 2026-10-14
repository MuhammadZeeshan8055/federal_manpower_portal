<?php

namespace App\Support;

use App\Models\Client;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;

class ClientPublicLink
{
    // Make sure the client has a secret token for the public status page
    public static function ensureToken(Client $client): string
    {
        if (filled($client->public_token)) {
            return $client->public_token;
        }

        $token = Str::random(40);

        $client->forceFill(['public_token' => $token])->save();

        return $token;
    }

    public static function statusUrl(Client $client): string
    {
        $token = self::ensureToken($client);

        return route('client.status', ['token' => $token]);
    }

    // QR image as data URI for DomPDF
    public static function qrDataUri(Client $client): string
    {
        $writer = new PngWriter;

        $result = $writer->write(new QrCode(
            data: self::statusUrl($client),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 220,
            margin: 4,
        ));

        return 'data:image/png;base64,'.base64_encode($result->getString());
    }
}
