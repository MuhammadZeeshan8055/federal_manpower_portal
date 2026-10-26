<?php

namespace Database\Seeders;

use App\Models\EmployerProfile;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployerJobSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@federal.com')->first();

        $employers = [
            [
                'email' => 'employer.gulf@demo.federal.com',
                'company_name' => 'Gulf Build Co',
                'contact_person' => 'Ahmed Al Rashid',
                'phone' => '+971 50 100 2001',
                'country' => 'UAE',
                'city' => 'Dubai',
                'address' => 'Business Bay, Dubai',
                'jobs' => [
                    [
                        'title' => 'Construction Site Supervisor',
                        'category' => 'Construction',
                        'country' => 'UAE',
                        'city' => 'Dubai',
                        'employment_type' => 'Full time',
                        'experience' => '5+ years',
                        'salary_text' => 'AED 4,500 – 5,500',
                        'vacancies' => 8,
                        'description' => 'Supervise daily construction activities, coordinate subcontractors, and ensure site safety compliance.',
                    ],
                    [
                        'title' => 'Steel Fixer',
                        'category' => 'Construction',
                        'country' => 'UAE',
                        'city' => 'Abu Dhabi',
                        'employment_type' => 'Full time',
                        'experience' => '3+ years',
                        'salary_text' => 'AED 1,800 – 2,400',
                        'vacancies' => 25,
                        'description' => 'Cut, bend and fix reinforcement steel as per drawings on commercial building projects.',
                    ],
                    [
                        'title' => 'Heavy Vehicle Driver',
                        'category' => 'Driving',
                        'country' => 'UAE',
                        'city' => 'Dubai',
                        'employment_type' => 'Full time',
                        'experience' => '2+ years',
                        'salary_text' => 'AED 2,200 – 2,800',
                        'vacancies' => 12,
                        'description' => 'Operate tipper / trailer trucks on construction logistics routes. Valid UAE-ready licence preferred.',
                    ],
                ],
            ],
            [
                'email' => 'employer.qatar@demo.federal.com',
                'company_name' => 'Qatar Facility Services',
                'contact_person' => 'Fatima Al Thani',
                'phone' => '+974 5555 1200',
                'country' => 'Qatar',
                'city' => 'Doha',
                'address' => 'West Bay, Doha',
                'jobs' => [
                    [
                        'title' => 'Facility Maintenance Technician',
                        'category' => 'Engineering',
                        'country' => 'Qatar',
                        'city' => 'Doha',
                        'employment_type' => 'Full time',
                        'experience' => '3+ years',
                        'salary_text' => 'QAR 2,500 – 3,200',
                        'vacancies' => 10,
                        'description' => 'Preventive and corrective maintenance for HVAC, electrical and plumbing in commercial buildings.',
                    ],
                    [
                        'title' => 'Hotel Housekeeping Supervisor',
                        'category' => 'Hospitality',
                        'country' => 'Qatar',
                        'city' => 'Doha',
                        'employment_type' => 'Full time',
                        'experience' => '4+ years',
                        'salary_text' => 'QAR 2,000 – 2,600',
                        'vacancies' => 6,
                        'description' => 'Lead housekeeping teams in a 4-star property. English communication required.',
                    ],
                ],
            ],
            [
                'email' => 'employer.riyadh@demo.federal.com',
                'company_name' => 'Riyadh Industrial Group',
                'contact_person' => 'Omar Al Saud',
                'phone' => '+966 50 200 3001',
                'country' => 'KSA',
                'city' => 'Riyadh',
                'address' => 'Industrial Area, Riyadh',
                'jobs' => [
                    [
                        'title' => 'Industrial Electrician',
                        'category' => 'Engineering',
                        'country' => 'KSA',
                        'city' => 'Riyadh',
                        'employment_type' => 'Full time',
                        'experience' => '4+ years',
                        'salary_text' => 'SAR 2,800 – 3,500',
                        'vacancies' => 15,
                        'description' => 'Install and maintain industrial electrical panels, motors and control circuits in factory environments.',
                    ],
                    [
                        'title' => 'Registered Nurse',
                        'category' => 'Healthcare',
                        'country' => 'KSA',
                        'city' => 'Jeddah',
                        'employment_type' => 'Full time',
                        'experience' => '2+ years',
                        'salary_text' => 'SAR 4,000 – 5,500',
                        'vacancies' => 20,
                        'description' => 'Hospital nursing roles for licensed professionals. Valid nursing credentials required.',
                    ],
                    [
                        'title' => 'Light Vehicle Driver',
                        'category' => 'Driving',
                        'country' => 'KSA',
                        'city' => 'Riyadh',
                        'employment_type' => 'Full time',
                        'experience' => '1+ years',
                        'salary_text' => 'SAR 1,800 – 2,200',
                        'vacancies' => 18,
                        'description' => 'Company car / van driving for staff transport and light deliveries within the city.',
                    ],
                ],
            ],
            [
                'email' => 'employer.cyprus@demo.federal.com',
                'company_name' => 'Cyprus Hotel Partners',
                'contact_person' => 'Elena Georgiou',
                'phone' => '+357 99 100 200',
                'country' => 'Cyprus',
                'city' => 'Limassol',
                'address' => 'Coastal Avenue, Limassol',
                'jobs' => [
                    [
                        'title' => 'Restaurant Waiter / Waitress',
                        'category' => 'Hospitality',
                        'country' => 'Cyprus',
                        'city' => 'Limassol',
                        'employment_type' => 'Full time',
                        'experience' => '1+ years',
                        'salary_text' => 'EUR 900 – 1,200',
                        'vacancies' => 30,
                        'description' => 'Serve guests in resort restaurants. Basic English required; seasonal contracts available.',
                    ],
                    [
                        'title' => 'Hotel Cook',
                        'category' => 'Hospitality',
                        'country' => 'Cyprus',
                        'city' => 'Paphos',
                        'employment_type' => 'Contract',
                        'experience' => '3+ years',
                        'salary_text' => 'EUR 1,100 – 1,400',
                        'vacancies' => 10,
                        'description' => 'Prepare meals for hotel buffet and à la carte outlets during peak tourist season.',
                    ],
                ],
            ],
            [
                'email' => 'employer.romania@demo.federal.com',
                'company_name' => 'Romania Agro Works',
                'contact_person' => 'Andrei Popescu',
                'phone' => '+40 721 100 200',
                'country' => 'Romania',
                'city' => 'Bucharest',
                'address' => 'Sector 1, Bucharest',
                'jobs' => [
                    [
                        'title' => 'General Construction Labour',
                        'category' => 'Construction',
                        'country' => 'Romania',
                        'city' => 'Bucharest',
                        'employment_type' => 'Full time',
                        'experience' => 'Fresher OK',
                        'salary_text' => 'EUR 800 – 1,000',
                        'vacancies' => 40,
                        'description' => 'General site labour for residential and commercial projects. Accommodation support available.',
                    ],
                    [
                        'title' => 'Welder (MIG / Arc)',
                        'category' => 'Construction',
                        'country' => 'Romania',
                        'city' => 'Cluj-Napoca',
                        'employment_type' => 'Full time',
                        'experience' => '2+ years',
                        'salary_text' => 'EUR 1,000 – 1,300',
                        'vacancies' => 14,
                        'description' => 'Structural and fabrication welding on industrial sites. Trade test on arrival.',
                    ],
                ],
            ],
            [
                'email' => 'employer.muscat@demo.federal.com',
                'company_name' => 'Muscat Logistics LLC',
                'contact_person' => 'Said Al Busaidi',
                'phone' => '+968 9123 4567',
                'country' => 'Oman',
                'city' => 'Muscat',
                'address' => 'Ruwi, Muscat',
                'jobs' => [
                    [
                        'title' => 'Warehouse Helper',
                        'category' => 'Construction',
                        'country' => 'Oman',
                        'city' => 'Muscat',
                        'employment_type' => 'Full time',
                        'experience' => 'Fresher OK',
                        'salary_text' => 'OMR 120 – 150',
                        'vacancies' => 22,
                        'description' => 'Loading, unloading and packing support in warehouse and logistics operations.',
                    ],
                    [
                        'title' => 'AC Technician',
                        'category' => 'Engineering',
                        'country' => 'Oman',
                        'city' => 'Muscat',
                        'employment_type' => 'Full time',
                        'experience' => '3+ years',
                        'salary_text' => 'OMR 180 – 250',
                        'vacancies' => 8,
                        'description' => 'Install and service split and package AC units for residential and commercial clients.',
                    ],
                ],
            ],
        ];

        foreach ($employers as $row) {
            $user = User::query()->updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['contact_person'],
                    'password' => Hash::make('password'),
                    'role' => 'employer',
                    'email_verified_at' => now(),
                ]
            );

            $profile = EmployerProfile::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $row['company_name'],
                    'contact_person' => $row['contact_person'],
                    'phone' => $row['phone'],
                    'country' => $row['country'],
                    'city' => $row['city'],
                    'address' => $row['address'],
                    'status' => 'approved',
                    'rejection_reason' => null,
                    'approved_by' => $admin?->id,
                    'approved_at' => now()->subDays(14),
                ]
            );

            foreach ($row['jobs'] as $job) {
                JobPost::query()->updateOrCreate(
                    [
                        'employer_profile_id' => $profile->id,
                        'title' => $job['title'],
                    ],
                    [
                        'category' => $job['category'],
                        'country' => $job['country'],
                        'city' => $job['city'],
                        'employment_type' => $job['employment_type'],
                        'experience' => $job['experience'],
                        'salary_text' => $job['salary_text'],
                        'vacancies' => $job['vacancies'],
                        'description' => $job['description'],
                        'status' => 'approved',
                        'rejection_reason' => null,
                        'approved_by' => $admin?->id,
                        'approved_at' => now()->subDays(rand(1, 12)),
                    ]
                );
            }
        }
    }
}
