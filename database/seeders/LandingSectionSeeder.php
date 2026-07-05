<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use Illuminate\Database\Seeder;

class LandingSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Hero Section',
                'theme' => 'default',
                'code' => 'hero',
                'data' => json_encode([
                    'hero_title' => 'Empower Your Financial Journey with MoneyChain',
                    'hero_description' => 'Cryptocurrency is a decentralized digital currency using blockchain technology for secure transactions.',
                    'bonus_text' => 'Sign Up now & get up to $5000',
                    'bubble_text' => 'Our client satisfy',
                ]),
                'status' => 1,
                'sort' => 1,
                'locale' => 'en',
            ],
        ];

        LandingPage::insert($data);
    }
}
