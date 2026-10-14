<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventsSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Annual International STEM & Robotics Exposition',
                'slug' => 'annual-international-stem-robotics-exposition-2026',
                'category' => 'Symposium',
                'description' => 'A showcase of autonomous rovers, machine vision applications, and biomedical simulations developed by faculty and student cohorts.',
                'event_date' => Carbon::parse('2026-10-24 09:30:00'),
                'end_date' => Carbon::parse('2026-10-24 16:30:00'),
                'venue' => 'Grand STEM Quadrangle & Main Auditorium',
                'banner_image' => 'scholar/assets/images/event-01.jpg',
                'organizer' => 'Faculty of Engineering & Computing',
                'registration_link' => 'https://scholar.edu/events/stem-expo-2026',
                'is_published' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'Fall Term Parent-Teacher Academic Consultation Forum',
                'slug' => 'fall-term-parent-teacher-consultation-forum-2026',
                'category' => 'Academic',
                'description' => 'One-on-one progress evaluation sessions between guardians, faculty heads, and student advisors covering term continuous assessments.',
                'event_date' => Carbon::parse('2026-11-06 14:00:00'),
                'end_date' => Carbon::parse('2026-11-06 18:00:00'),
                'venue' => 'Central Conference Pavilion & Zoom Hybrid',
                'banner_image' => 'scholar/assets/images/event-02.jpg',
                'organizer' => 'Office of Academic Affairs',
                'registration_link' => null,
                'is_published' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'Inter-Collegiate Varsity Swimming & Aquatics Gala',
                'slug' => 'inter-collegiate-varsity-swimming-gala-2026',
                'category' => 'Sports',
                'description' => 'Regional championship heats across freestyle, butterfly, and relay categories in our Olympic-standard indoor aquatics center.',
                'event_date' => Carbon::parse('2026-11-18 10:00:00'),
                'end_date' => Carbon::parse('2026-11-18 15:00:00'),
                'venue' => 'Championship Aquatics Pavilion',
                'banner_image' => 'scholar/assets/images/banner-item-01.jpg',
                'organizer' => 'Department of Athletics & Varsity Sports',
                'registration_link' => null,
                'is_published' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'Winter Chamber Symphony & Fine Arts Gala',
                'slug' => 'winter-chamber-symphony-fine-arts-gala-2026',
                'category' => 'Arts & Culture',
                'description' => 'A classical and contemporary musical evening showcasing the student philharmonic orchestra, choral ensembles, and digital visual art.',
                'event_date' => Carbon::parse('2026-12-10 18:30:00'),
                'end_date' => Carbon::parse('2026-12-10 21:30:00'),
                'venue' => 'Symphony Concert Hall',
                'banner_image' => 'scholar/assets/images/event-03.jpg',
                'organizer' => 'School of Creative & Performing Arts',
                'registration_link' => 'https://scholar.edu/events/winter-gala-2026',
                'is_published' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'Career Horizons & Global University Admissions Fair',
                'slug' => 'career-horizons-global-university-admissions-fair-2027',
                'category' => 'Workshop',
                'description' => 'Delegates from 35 international universities presenting scholarship pathways, college application protocols, and industry internships.',
                'event_date' => Carbon::parse('2027-01-15 11:00:00'),
                'end_date' => Carbon::parse('2027-01-15 16:00:00'),
                'venue' => 'Executive Exhibition Hall',
                'banner_image' => 'scholar/assets/images/course-01.jpg',
                'organizer' => 'Career Development & Guidance Office',
                'registration_link' => null,
                'is_published' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($events as $eventData) {
            Event::updateOrCreate(
                ['slug' => $eventData['slug']],
                $eventData
            );
        }
    }
}
