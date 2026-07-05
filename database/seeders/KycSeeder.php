<?php

namespace Database\Seeders;

use App\Models\Kyc;
use Illuminate\Database\Seeder;

class KycSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kyc_data = [
            [
                'name' => 'NID',
                'fields' => [
                    '1' => [
                        'name' => 'Front Image',
                        'type' => 'file',
                        'validation' => 'required',
                    ],
                ],
            ],
            [
                'name' => 'Identity',
                'fields' => [
                    '1' => [
                        'name' => 'a',
                        'type' => 'text',
                        'validation' => 'required',
                    ],
                    '2' => [
                        'name' => 'b',
                        'type' => 'textarea',
                        'validation' => 'required',
                    ],
                    '3' => [
                        'name' => 'c',
                        'type' => 'text',
                        'validation' => 'required',
                    ],
                ],
            ],
        ];

        foreach ($kyc_data as $kyc) {
            Kyc::create([
                'name' => $kyc['name'],
                'fields' => json_encode($kyc['fields']),
                'status' => 1,
            ]);
        }
    }
}
