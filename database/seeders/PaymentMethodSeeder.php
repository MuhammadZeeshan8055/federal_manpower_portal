<?php

namespace Database\Seeders;

use App\Models\PaymentAccount;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        // Rename old "Cash" if it still exists
        PaymentMethod::query()
            ->whereRaw('LOWER(name) = ?', ['cash'])
            ->update(['name' => 'Office cash']);

        $tree = [
            'Office cash' => [
                ['name' => 'Office cash', 'details' => null],
            ],
            'Bank transfer' => [
                ['name' => 'Meezan Bank', 'details' => null],
                ['name' => 'Bank Alfalah', 'details' => null],
            ],
            'JazzCash' => [
                ['name' => '03001234567', 'details' => null],
                ['name' => '03121234567', 'details' => null],
            ],
            'EasyPaisa' => [
                ['name' => '03009876543', 'details' => null],
                ['name' => '03129876543', 'details' => null],
            ],
        ];

        foreach ($tree as $methodName => $accounts) {
            $method = PaymentMethod::query()->firstOrCreate(
                ['name' => $methodName],
                ['is_active' => true]
            );

            foreach ($accounts as $account) {
                PaymentAccount::query()->firstOrCreate(
                    [
                        'payment_method_id' => $method->id,
                        'name' => $account['name'],
                    ],
                    [
                        'details' => $account['details'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
