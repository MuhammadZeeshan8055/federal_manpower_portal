<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Country;
use App\Models\JobPost;
use App\Models\Trade;

class JobApplyPrefill
{
    public static function approvedJob(int $jobId): ?JobPost
    {
        return JobPost::query()
            ->approved()
            ->with('employer')
            ->whereKey($jobId)
            ->first();
    }

    /** @return array{trade_id:?int,country_id:?int,company_id:?int} */
    public static function matchIds(JobPost $job): array
    {
        return [
            'trade_id' => self::matchTradeId($job->category, $job->title),
            'country_id' => self::matchCountryId($job->country),
            'company_id' => self::matchCompanyId($job->employer?->company_name),
        ];
    }

    private static function matchCountryId(?string $name): ?int
    {
        $name = trim((string) $name);

        if ($name === '') {
            return null;
        }

        $exact = Country::query()
            ->where('is_active', true)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->value('id');

        if ($exact) {
            return (int) $exact;
        }

        return Country::query()
            ->where('is_active', true)
            ->where('name', 'like', '%'.$name.'%')
            ->orderBy('name')
            ->value('id');
    }

    private static function matchTradeId(?string $category, ?string $title): ?int
    {
        $needles = array_filter([
            trim((string) $category),
            trim((string) $title),
        ]);

        foreach ($needles as $needle) {
            $exact = Trade::query()
                ->where('is_active', true)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($needle)])
                ->value('id');

            if ($exact) {
                return (int) $exact;
            }
        }

        foreach ($needles as $needle) {
            $like = Trade::query()
                ->where('is_active', true)
                ->where('name', 'like', '%'.$needle.'%')
                ->orderBy('name')
                ->value('id');

            if ($like) {
                return (int) $like;
            }
        }

        return null;
    }

    private static function matchCompanyId(?string $companyName): ?int
    {
        $companyName = trim((string) $companyName);

        if ($companyName === '') {
            return null;
        }

        $exact = Company::query()
            ->where('type', 'employer')
            ->where('is_active', true)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($companyName)])
            ->value('id');

        if ($exact) {
            return (int) $exact;
        }

        return Company::query()
            ->where('type', 'employer')
            ->where('is_active', true)
            ->where('name', 'like', '%'.$companyName.'%')
            ->orderBy('name')
            ->value('id');
    }
}
