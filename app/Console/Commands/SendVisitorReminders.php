<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserNotification;
use App\Models\Visitor;
use Illuminate\Console\Command;

class SendVisitorReminders extends Command
{
    protected $signature = 'visitors:send-reminders';

    protected $description = 'Create notifications for visitor reminders due today';

    public function handle(): int
    {
        $today = now()->toDateString();

        $visitors = Visitor::query()
            ->whereDate('remind_on', $today)
            ->get();

        $created = 0;

        foreach ($visitors as $visitor) {
            $title = 'Visitor reminder due: '.$visitor->name;
            $body = 'Follow up today — '.$visitor->purpose
                .' (meet with '.$visitor->meet_with.')';

            $userIds = [];

            if ($visitor->created_by) {
                $userIds[] = (int) $visitor->created_by;
            }

            $meetUser = User::query()
                ->where('role', '!=', 'client')
                ->where('name', $visitor->meet_with)
                ->first();

            if ($meetUser) {
                $userIds[] = (int) $meetUser->id;
            }

            $userIds = array_unique($userIds);

            foreach ($userIds as $userId) {
                $row = UserNotification::query()->firstOrCreate(
                    [
                        'user_id' => $userId,
                        'type' => 'visitor_reminder_due',
                        'module_key' => 'visitors',
                        'feature_key' => (string) $visitor->id.'-'.$today,
                    ],
                    [
                        'title' => $title,
                        'body' => $body,
                        'read_at' => null,
                    ]
                );

                if ($row->wasRecentlyCreated) {
                    $created++;
                }
            }
        }

        $this->info('Visitor reminders processed. New notifications: '.$created);

        return self::SUCCESS;
    }
}
