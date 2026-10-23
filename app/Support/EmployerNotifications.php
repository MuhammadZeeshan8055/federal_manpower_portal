<?php

namespace App\Support;

use App\Models\EmployerProfile;
use App\Models\JobPost;
use App\Models\User;
use App\Models\UserNotification;

class EmployerNotifications
{
    public static function jobSubmitted(JobPost $job, bool $wasEdit = false): void
    {
        $job->loadMissing('employer');

        $company = $job->employer?->company_name ?: 'Employer';
        $title = $wasEdit ? 'Job updated — needs approval' : 'New job awaiting approval';
        $body = $job->title.' · '.$company;

        self::notifyAdmins('employer_job_pending', $title, $body, 'job-approvals');
    }

    public static function employerRegistered(EmployerProfile $profile): void
    {
        $title = 'New employer registration';
        $body = $profile->company_name.' · '.$profile->contact_person;

        self::notifyAdmins('employer_pending', $title, $body, 'approvals');
    }

    private static function notifyAdmins(string $type, string $title, string $body, string $featureKey): void
    {
        $admins = User::query()
            ->whereIn('role', ['super_admin', 'admin'])
            ->get();

        foreach ($admins as $admin) {
            UserNotification::query()->create([
                'user_id' => $admin->id,
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'module_key' => 'employers',
                'feature_key' => $featureKey,
                'read_at' => null,
            ]);
        }
    }
}
