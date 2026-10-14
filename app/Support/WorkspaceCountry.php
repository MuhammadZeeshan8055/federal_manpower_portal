<?php

namespace App\Support;

class WorkspaceCountry
{
    // Country selected in the header (or null = all countries)
    public static function id()
    {
        $id = session('workspace_country_id');

        if (! $id) {
            return null;
        }

        return (int) $id;
    }

    // Add country filter to a clients query when a country is selected
    public static function filterClients($query)
    {
        $id = self::id();

        if ($id) {
            $query->where('country_id', $id);
        }

        return $query;
    }

    // Add country filter to companies / universities query
    public static function filterCompanies($query)
    {
        $id = self::id();

        if ($id) {
            $query->where('country_id', $id);
        }

        return $query;
    }
}
