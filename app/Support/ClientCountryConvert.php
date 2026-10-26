<?php

namespace App\Support;

use App\Models\Client;

class ClientCountryConvert
{
    /**
     * @return array{country_id: ?int, converted_from_country_id?: int}
     */
    public static function fields(Client $client, ?int $newCountryId, bool $markAsConverted): array
    {
        $oldCountryId = $client->country_id ? (int) $client->country_id : null;
        $newCountryId = $newCountryId ? (int) $newCountryId : null;

        $fields = [
            'country_id' => $newCountryId,
        ];

        if (
            $markAsConverted
            && $oldCountryId
            && $newCountryId
            && $oldCountryId !== $newCountryId
        ) {
            $fields['converted_from_country_id'] = $oldCountryId;
        }

        return $fields;
    }
}
