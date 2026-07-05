<?php

namespace Database\Seeders;

use App\Models\CustomCss;
use App\Models\Setting;
use App\Models\Theme;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        foreach (config('setting') as $key => $setting) {
            if (isset($setting['elements'])) {
                foreach ($setting['elements'] as $element) {
                    $data[] = [
                        'type' => $element['type'],
                        'name' => $element['name'],
                        'val' => $element['value'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        Setting::insert($data);

        Theme::create([
            'name' => 'default',
            'type' => 'site',
            'status' => 1,
        ]);

        // Custom css
        CustomCss::create([
            'css' => '//The Custom CSS will be added on the site head tag
.site-head-tag {
	margin: 0;
  	padding: 0;
}',
        ]);
    }
}
