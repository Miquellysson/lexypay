<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Models\User;
use App\Payments\Support\ApiKeyHasher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $plainApiKey = Str::random(40);

        $merchant = Merchant::create([
            'name' => 'Demo Merchant',
            'api_key_hash' => ApiKeyHasher::hash($plainApiKey),
            'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'Demo Admin',
            'email' => 'demo@demo.com',
            'password' => Hash::make('password'),
            'merchant_id' => $merchant->id,
            'role' => 'admin',
        ]);

        $payments = [];

        for ($i = 0; $i < 50; $i++) {
            $status = $i % 3 === 0 ? 'pending' : 'paid';
            $createdAt = now()->subDays(rand(0, 13))->subMinutes(rand(0, 1440));

            $payment = Payment::create([
                'merchant_id' => $merchant->id,
                'amount' => rand(500, 50000),
                'currency' => 'USD',
                'status' => $status,
                'provider' => 'stripe',
                'idempotency_key' => Str::random(16),
                'paid_at' => $status === 'paid' ? $createdAt->copy()->addMinutes(rand(1, 120)) : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'metadata' => [
                    'order_id' => 'ORDER-'.$i,
                ],
            ]);

            PaymentEvent::create([
                'payment_id' => $payment->id,
                'type' => 'created',
                'payload_json' => [
                    'seeded' => true,
                ],
                'created_at' => $createdAt,
            ]);

            if ($status === 'paid') {
                PaymentEvent::create([
                    'payment_id' => $payment->id,
                    'type' => 'status_changed',
                    'payload_json' => [
                        'from' => 'pending',
                        'to' => 'paid',
                    ],
                    'created_at' => $payment->paid_at,
                ]);
            }

            $payments[] = $payment;
        }

        if ($this->command) {
            $this->command->info('Demo user created: '.$user->email.' / password');
            $this->command->info('API Key (store it now): '.$plainApiKey);
        }
    }
}
