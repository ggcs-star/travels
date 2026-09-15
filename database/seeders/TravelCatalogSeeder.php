<?php

namespace Database\Seeders;

use App\Models\TourCategory;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Database\Seeder;

class TravelCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();

        $categories = collect([
            ['name' => 'Spiritual & Pilgrimage', 'slug' => 'spiritual', 'description' => 'Meaningful journeys to India’s sacred destinations.'],
            ['name' => 'Holidays', 'slug' => 'holidays', 'description' => 'Relaxed, expertly organised holiday experiences.'],
            ['name' => 'School & College', 'slug' => 'school-college', 'description' => 'Safe and engaging educational travel.'],
        ])->mapWithKeys(function (array $data, int $index) use ($admin) {
            $category = TourCategory::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    ...$data,
                    'status' => TourCategory::STATUS_ACTIVE,
                    'featured' => $index === 0,
                    'sort_order' => $index + 1,
                    'created_by' => $admin->id,
                ]
            );

            return [$data['slug'] => $category];
        });

        $tours = [
            [
                'package_code' => 'KASHI-ESSENCE',
                'name' => 'Kashi Spiritual Essence',
                'category' => 'spiritual',
                'destination' => 'Varanasi, Uttar Pradesh',
                'starting_city' => 'Varanasi',
                'tour_type' => 'group',
                'short_description' => 'A gentle, immersive pilgrimage through Varanasi, Prayagraj, and the timeless Ganga ghats.',
                'description' => 'Experience the spiritual heart of India with guided temple visits, a peaceful Ganga aarti, and carefully planned local travel.',
                'duration_days' => 5,
                'duration_nights' => 4,
                'best_time' => 'October to March',
                'featured' => true,
                'included_items' => ['Accommodation on twin sharing', 'Breakfast and dinner', 'Local guided sightseeing', 'Airport or railway-station transfers'],
                'excluded_items' => ['Flights or train tickets', 'Personal expenses', 'Travel insurance'],
                'departure_days' => [21, 49],
                'price' => '18999.00',
            ],
            [
                'package_code' => 'KERALA-SLOW',
                'name' => 'Kerala Backwater Escape',
                'category' => 'holidays',
                'destination' => 'Kochi, Munnar & Alleppey',
                'starting_city' => 'Kochi',
                'tour_type' => 'group',
                'short_description' => 'Tea gardens, coastal culture, and a serene night on Kerala’s backwaters.',
                'description' => 'A balanced Kerala itinerary for travellers who want beautiful landscapes, comfortable stays, and unhurried time together.',
                'duration_days' => 6,
                'duration_nights' => 5,
                'best_time' => 'September to March',
                'featured' => true,
                'included_items' => ['Hotels and houseboat stay', 'Daily breakfast', 'Private local transport', 'Sightseeing as per itinerary'],
                'excluded_items' => ['Airfare', 'Lunches and dinners', 'Optional activities'],
                'departure_days' => [28, 56],
                'price' => '24999.00',
            ],
            [
                'package_code' => 'RAJ-LEARN',
                'name' => 'Rajasthan Heritage Study Tour',
                'category' => 'school-college',
                'destination' => 'Jaipur, Ajmer & Udaipur',
                'starting_city' => 'Jaipur',
                'tour_type' => 'group',
                'short_description' => 'An educational journey through Rajasthan’s living history, architecture, and culture.',
                'description' => 'Designed for student groups with clear supervision, curated learning stops, and comfortable transport throughout.',
                'duration_days' => 5,
                'duration_nights' => 4,
                'best_time' => 'October to February',
                'featured' => false,
                'included_items' => ['Group accommodation', 'Meals as per itinerary', 'Coach transport', 'Tour manager support'],
                'excluded_items' => ['Travel to Jaipur', 'Personal shopping', 'Medical expenses'],
                'departure_days' => [35],
                'price' => '15999.00',
            ],
        ];

        foreach ($tours as $tourData) {
            $category = $categories->get($tourData['category']);
            $departureDays = $tourData['departure_days'];
            unset($tourData['category'], $tourData['departure_days']);

            $tour = TourPackage::updateOrCreate(
                ['package_code' => $tourData['package_code']],
                [
                    ...$tourData,
                    'category_id' => $category->id,
                    'slug' => \Illuminate\Support\Str::slug($tourData['name']),
                    'status' => TourPackage::STATUS_PUBLISHED,
                    'created_by' => $admin->id,
                ]
            );

            foreach ($departureDays as $days) {
                $departureDate = today()->addDays($days);

                $tour->departures()->updateOrCreate(
                    ['departure_date' => $departureDate],
                    [
                        'return_date' => $departureDate->copy()->addDays($tour->duration_days - 1),
                        'capacity' => 24,
                        'price' => $tourData['price'],
                        'currency' => 'INR',
                        'meeting_point' => $tour->starting_city.' central meeting point',
                        'status' => 'open',
                    ]
                );
            }
        }
    }
}
