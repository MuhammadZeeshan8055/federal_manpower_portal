<?php

namespace App\Http\Controllers;

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
        return view('website.jobs', ['jobs' => $this->jobsData()]);
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
        ];
    }

    private function jobsData(): array
    {
        return [
            ['construction', 'Construction Worker', 'construction', 'uae', 'United Arab Emirates', '2+ years', 'junior', 'Full time', 'full-time', '$500 - $700 / month', 'Featured', 'images/website/job-cards-strip.png'],
            ['healthcare', 'Staff Nurse', 'healthcare', 'saudi-arabia', 'Saudi Arabia', '3+ years', 'mid', 'Full time', 'full-time', '$900 - $1,200 / month', 'New', 'images/website/job-cards-strip.png'],
            ['driving', 'Heavy Vehicle Driver', 'driving', 'qatar', 'Qatar', '3+ years', 'mid', 'Contract', 'contract', '$700 - $850 / month', 'Popular', 'images/website/job-cards-strip.png'],
            ['engineering', 'Mechanical Engineer', 'engineering', 'kuwait', 'Kuwait', '4+ years', 'senior', 'Full time', 'full-time', '$1,200 - $1,600 / month', 'Featured', 'images/website/job-cards-strip.png'],
            ['hospitality', 'Hotel Supervisor', 'hospitality', 'oman', 'Oman', '3+ years', 'mid', 'Contract', 'contract', '$750 - $950 / month', 'New', 'images/website/recruitment-team-banner.png'],
        ];
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
