<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General / Branding
            [
                'key' => 'school_name',
                'value' => 'Scholar Academy of Advanced Sciences',
                'group' => 'general',
                'type' => 'string',
            ],
            [
                'key' => 'school_short_name',
                'value' => 'Scholar Academy',
                'group' => 'general',
                'type' => 'string',
            ],
            [
                'key' => 'school_address',
                'value' => '100 University Boulevard, Academic District, Suite 500',
                'group' => 'general',
                'type' => 'string',
            ],
            [
                'key' => 'school_phone',
                'value' => '+1 (555) 234-5678 / +1 (555) 234-8900',
                'group' => 'general',
                'type' => 'string',
            ],
            [
                'key' => 'school_email',
                'value' => 'admissions@scholar-academy.edu',
                'group' => 'general',
                'type' => 'string',
            ],
            [
                'key' => 'school_principal',
                'value' => 'Dr. Evelyn Vance, Ph.D.',
                'group' => 'general',
                'type' => 'string',
            ],
            [
                'key' => 'school_principal_title',
                'value' => 'Dean of Examination & Academic Affairs',
                'group' => 'general',
                'type' => 'string',
            ],
            [
                'key' => 'school_license',
                'value' => 'License #SCH-9941',
                'group' => 'general',
                'type' => 'string',
            ],

            // Academic
            [
                'key' => 'admission_number_prefix',
                'value' => 'SCH-',
                'group' => 'academic',
                'type' => 'string',
            ],

            // Marketing / Landing Stats Overrides (optional dynamic boosters or real mode)
            [
                'key' => 'landing_stats_awards',
                'value' => '28',
                'group' => 'marketing',
                'type' => 'integer',
            ],
            [
                'key' => 'landing_stats_attendance',
                'value' => '98.6%',
                'group' => 'marketing',
                'type' => 'string',
            ],
        ];

        foreach ($settings as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                [
                    'value' => $item['value'],
                    'group' => $item['group'],
                    'type' => $item['type'],
                ]
            );
        }
    }
}
