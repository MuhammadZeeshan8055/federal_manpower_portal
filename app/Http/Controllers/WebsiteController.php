<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Support\Str;

class WebsiteController extends Controller
{
    public function home()
    {
        return view('website.home', $this->sharedData());
    }

    public function about()
    {
        return view('website.about');
    }

    public function contact()
    {
        return view('website.contact');
    }

    public function jobs()
    {
        return view('website.jobs', [
            'jobs' => $this->jobsData(),
            'jobFilterCountries' => $this->approvedJobOptions('country'),
            'jobFilterCategories' => $this->approvedJobOptions('category'),
            'jobFilterExperiences' => $this->approvedJobOptions('experience'),
            'jobFilterTypes' => $this->approvedJobOptions('employment_type'),
        ]);
    }

    public function countries()
    {
        return view('website.countries', ['destinations' => $this->destinationsData()]);
    }

    public function services()
    {
        return view('website.services');
    }

    public function employers()
    {
        return view('website.employers');
    }

    private function sharedData(): array
    {
        return [
            'jobs' => $this->jobsData(),
            'destinations' => $this->destinationsData(),
            'jobFilterCountries' => $this->approvedJobOptions('country'),
            'jobFilterCategories' => $this->approvedJobOptions('category'),
            'jobFilterExperiences' => $this->approvedJobOptions('experience'),
            'jobFilterTypes' => $this->approvedJobOptions('employment_type'),
        ];
    }

    /** @return array<string, string> slug => label from approved jobs only */
    private function approvedJobOptions(string $column): array
    {
        return JobPost::query()
            ->approved()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->mapWithKeys(function (string $label) {
                $slug = Str::slug($label);

                return $slug !== '' ? [$slug => $label] : [];
            })
            ->all();
    }

    private function jobsData(): array
    {
        return JobPost::query()
            ->approved()
            ->with('employer')
            ->orderByDesc('approved_at')
            ->orderByDesc('id')
            ->limit(24)
            ->get()
            ->map(function (JobPost $job) {
                $category = $job->category ?: 'General';
                $categorySlug = Str::slug($category) ?: 'general';
                $country = $job->country ?: 'Overseas';
                $type = $job->employment_type ?: 'Full time';
                $experience = $job->experience ?: '—';
                $experienceSlug = Str::slug($experience) ?: 'any';

                return [
                    $this->visualForCategory($categorySlug),
                    $job->title,
                    $categorySlug,
                    Str::slug($country) ?: 'overseas',
                    $country,
                    $experience,
                    $experienceSlug,
                    $type,
                    Str::slug($type) ?: 'full-time',
                    $job->salary_text ?: 'Negotiable',
                    'Open',
                    'images/website/job-cards-strip.png',
                    $job->id,
                ];
            })
            ->all();
    }

    private function visualForCategory(string $categorySlug): string
    {
        $known = ['construction', 'healthcare', 'driving', 'engineering', 'hospitality'];

        if (in_array($categorySlug, $known, true)) {
            return $categorySlug;
        }

        return 'construction';
    }

    private function destinationsData(): array
    {
        return [
            ['uae', 'UAE', 'ae.svg', 'Construction, Hospitality, Logistics'],
            ['saudi', 'Saudi Arabia', 'sa.svg', 'Healthcare, Engineering, Oil & Gas'],
            ['qatar', 'Qatar', 'qa.svg', 'Construction, Oil & Gas, Hospitality'],
            ['kuwait', 'Kuwait', 'world.svg', 'Construction, Oil & Gas, Facility Management'],
            ['oman', 'Oman', 'om.svg', 'Manufacturing, Hospitality, Logistics'],
            ['bahrain', 'Bahrain', 'world.svg', 'Finance, Hospitality, Construction'],
            ['romania', 'Romania', 'ro.svg', 'Construction, Manufacturing, Logistics'],
            ['italy', 'Italy', 'it.svg', 'Hospitality, Agriculture, Skilled Trades'],
            ['portugal', 'Portugal', 'pt.svg', 'Construction, Hospitality, Agriculture'],
            ['turkiye', 'Türkiye', 'tr.svg', 'Manufacturing, Textiles, Hospitality'],
            ['greece', 'Greece', 'gr.svg', 'Tourism, Agriculture, Construction'],
            ['cyprus', 'Cyprus', 'cy.svg', 'Hospitality, Healthcare, Services'],
        ];
    }
}
