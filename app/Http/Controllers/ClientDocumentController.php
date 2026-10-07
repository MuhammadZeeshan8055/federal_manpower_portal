<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientDocument;
use App\Support\ClientPrivateFiles;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientDocumentController extends Controller
{
    public function show(Client $client, string $docKey): StreamedResponse|Response
    {
        $user = auth()->user();

        if (! $user || ! ClientPrivateFiles::userCanAccess($user)) {
            abort(403);
        }

        $document = ClientDocument::query()
            ->where('client_id', $client->id)
            ->where('doc_key', $docKey)
            ->firstOrFail();

        $path = $document->file_path;

        if (! ClientPrivateFiles::pathBelongsToClient($client->id, $path)) {
            abort(404);
        }

        if (! ClientPrivateFiles::exists($path)) {
            abort(404);
        }

        $downloadName = $document->original_name ?: basename($path);

        return Storage::disk(ClientPrivateFiles::disk())->response($path, $downloadName);
    }

    public function photo(Client $client): StreamedResponse|Response
    {
        $user = auth()->user();

        if (! $user || ! ClientPrivateFiles::userCanAccess($user)) {
            abort(403);
        }

        $path = $client->photo_path;

        if (! ClientPrivateFiles::pathBelongsToClient($client->id, $path)) {
            abort(404);
        }

        if (! ClientPrivateFiles::exists($path)) {
            abort(404);
        }

        return Storage::disk(ClientPrivateFiles::disk())->response($path, basename($path));
    }
}
