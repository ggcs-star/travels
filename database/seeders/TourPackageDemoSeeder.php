<?php

namespace Database\Seeders;

use App\Models\TourCategory;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TourPackageDemoSeeder extends Seeder
{
    private const TERMS = "1. Booking is confirmed only after receipt of advance payment.\n2. Itinerary may change due to weather, local conditions or authority guidelines.\n3. Rates are subject to change during peak/festival season.\n4. Company is not responsible for delays due to natural calamities, strikes or political unrest.";

    private const CANCELLATION = "- 30+ days before departure: 25% cancellation charge\n- 15-29 days before departure: 50% cancellation charge\n- 7-14 days before departure: 75% cancellation charge\n- Less than 7 days: No refund\nProcessing fees and non-refundable third-party bookings (train/flight/hotel) are additional.";

    private const PRIVACY = "Personal information collected during booking (name, contact, ID proof) is used solely for travel arrangement, statutory compliance and communication, and is never shared with third parties except vendors directly involved in service delivery.";

    public function run(): void
    {
        $admin = User::query()->where('role', 'admin')->first()
            ?? User::query()->firstOrFail();

        $categories = TourCategory::query()
            ->pluck('id', 'slug');

        $today = Carbon::today();

        $packages = [

            /* ---------------------------------------------------------
             | Spiritual & Pilgrimage
             |---------------------------------------------------------*/
            [
                'category' => 'spiritual-pilgrimage',
                'package_code' => 'CHARDHAM-2026',
                'name' => 'Char Dham Yatra – Kedarnath, Badrinath, Gangotri & Yamunotri',
                'destination' => 'Uttarakhand, India',
                'starting_city' => 'Haridwar',
                'ending_city' => 'Haridwar',
                'short_description' => 'An 11-day guided pilgrimage covering all four sacred Char Dham temples with comfortable stays and dedicated puja arrangements.',
                'description' => 'Journey through the Himalayas to seek blessings at Yamunotri, Gangotri, Kedarnath and Badrinath. This all-inclusive yatra is designed for comfort and devotion, with experienced pilgrimage guides, home-style vegetarian meals, and well-planned acclimatisation stops for high-altitude travel.',
                'duration_days' => 11,
                'duration_nights' => 10,
                'difficulty_level' => 'moderate',
                'age_min' => 8,
                'age_max' => 70,
                'best_time' => 'May to June, September to October',
                'featured' => true,
                'highlights' => ['Darshan at all four Char Dham temples', 'Optional helicopter service for Kedarnath', 'Experienced pilgrimage guide throughout', 'Comfortable hotel & dharamshala stays', 'Daily satvik vegetarian meals'],
                'included_items' => ['AC transport throughout the yatra', '10 nights hotel/dharamshala accommodation', 'Daily breakfast and dinner', 'All temple entry and puja arrangements', 'Experienced tour manager and driver'],
                'excluded_items' => ['Train/flight fare to and from Haridwar', 'Lunch', 'Personal expenses and tips', 'Travel insurance', 'Optional helicopter tickets', 'Pony/palki charges at Kedarnath'],
                'important_notes' => 'High-altitude travel — carry warm clothing, personal medication and valid ID proof. Senior citizens and pilgrims with health conditions should carry a fitness certificate.',
                'itinerary' => [
                    ['title' => 'Arrival in Haridwar', 'location' => 'Haridwar', 'description' => 'Arrive in Haridwar, check-in and attend the evening Ganga Aarti at Har Ki Pauri.', 'activities' => ['Hotel check-in', 'Ganga Aarti']],
                    ['title' => 'Haridwar to Barkot', 'location' => 'Barkot', 'description' => 'Drive to Barkot via Mussoorie, overnight stay.', 'activities' => ['Scenic drive', 'Overnight stay']],
                    ['title' => 'Yamunotri Darshan', 'location' => 'Yamunotri', 'description' => 'Trek to Yamunotri temple, perform puja and return to Barkot.', 'activities' => ['Trek to temple', 'Puja darshan']],
                    ['title' => 'Barkot to Uttarkashi', 'location' => 'Uttarkashi', 'description' => 'Drive to Uttarkashi, visit Vishwanath Temple.', 'activities' => ['Temple visit', 'Local sightseeing']],
                    ['title' => 'Gangotri Darshan', 'location' => 'Gangotri', 'description' => 'Day trip to Gangotri temple and back to Uttarkashi.', 'activities' => ['Gangotri darshan', 'Return drive']],
                    ['title' => 'Uttarkashi to Guptkashi', 'location' => 'Guptkashi', 'description' => 'Long scenic drive towards Guptkashi, base for Kedarnath.', 'activities' => ['Scenic drive', 'Rest and acclimatisation']],
                    ['title' => 'Kedarnath Trek/Helicopter', 'location' => 'Kedarnath', 'description' => 'Trek or take optional helicopter to Kedarnath, darshan and overnight stay.', 'activities' => ['Trek/heli transfer', 'Kedarnath darshan']],
                    ['title' => 'Return to Guptkashi', 'location' => 'Guptkashi', 'description' => 'Descend from Kedarnath and drive back to Guptkashi.', 'activities' => ['Trek down', 'Rest']],
                    ['title' => 'Guptkashi to Badrinath', 'location' => 'Badrinath', 'description' => 'Drive to Badrinath via Joshimath, evening aarti at the temple.', 'activities' => ['Scenic drive', 'Evening aarti']],
                    ['title' => 'Badrinath Darshan & Rudranath', 'location' => 'Badrinath', 'description' => 'Morning darshan at Badrinath, visit Mana village, drive towards Rudraprayag.', 'activities' => ['Temple darshan', 'Mana village visit']],
                    ['title' => 'Departure from Haridwar', 'location' => 'Haridwar', 'description' => 'Drive back to Haridwar, tour concludes with drop at railway station/airport.', 'activities' => ['Return drive', 'Departure']],
                ],
                'price' => 26999,
                'sale_price' => 23999,
                'capacity' => 25,
            ],
            [
                'category' => 'spiritual-pilgrimage',
                'package_code' => 'KASHI-BODHGAYA-2026',
                'name' => 'Varanasi & Bodh Gaya Spiritual Retreat',
                'destination' => 'Uttar Pradesh & Bihar, India',
                'starting_city' => 'Varanasi',
                'ending_city' => 'Varanasi',
                'short_description' => 'A peaceful 5-day retreat through Varanasi\'s sacred ghats and the Buddhist pilgrimage site of Bodh Gaya.',
                'description' => 'Experience the spiritual heart of India — witness the mesmerising Ganga Aarti at Varanasi, explore ancient temples, and travel to Bodh Gaya to visit the Mahabodhi Temple where Buddha attained enlightenment.',
                'duration_days' => 5,
                'duration_nights' => 4,
                'difficulty_level' => 'easy',
                'age_min' => 5,
                'age_max' => 80,
                'best_time' => 'October to March',
                'featured' => false,
                'highlights' => ['Sunrise boat ride on the Ganges', 'Evening Ganga Aarti at Dashashwamedh Ghat', 'Mahabodhi Temple visit in Bodh Gaya', 'Sarnath Buddhist heritage tour'],
                'included_items' => ['4 nights hotel accommodation', 'Daily breakfast and dinner', 'AC vehicle for sightseeing', 'Boat ride on the Ganges', 'Local guide'],
                'excluded_items' => ['Flights/train tickets', 'Lunch', 'Camera fees at monuments', 'Personal expenses'],
                'important_notes' => 'Comfortable walking shoes recommended for ghat visits. Modest clothing advised for temple visits.',
                'itinerary' => [
                    ['title' => 'Arrival in Varanasi', 'location' => 'Varanasi', 'description' => 'Check-in, evening Ganga Aarti at Dashashwamedh Ghat.', 'activities' => ['Hotel check-in', 'Ganga Aarti']],
                    ['title' => 'Varanasi Sightseeing', 'location' => 'Varanasi & Sarnath', 'description' => 'Sunrise boat ride, visit Kashi Vishwanath Temple and Sarnath.', 'activities' => ['Boat ride', 'Temple visit', 'Sarnath tour']],
                    ['title' => 'Drive to Bodh Gaya', 'location' => 'Bodh Gaya', 'description' => 'Drive to Bodh Gaya, evening visit to Mahabodhi Temple.', 'activities' => ['Scenic drive', 'Mahabodhi Temple']],
                    ['title' => 'Bodh Gaya Exploration', 'location' => 'Bodh Gaya', 'description' => 'Visit monasteries, Bodhi Tree and local markets.', 'activities' => ['Monastery visits', 'Local market']],
                    ['title' => 'Return to Varanasi & Departure', 'location' => 'Varanasi', 'description' => 'Drive back to Varanasi, tour concludes with drop at station/airport.', 'activities' => ['Return drive', 'Departure']],
                ],
                'price' => 12999,
                'sale_price' => 10999,
                'capacity' => 30,
            ],

            /* ---------------------------------------------------------
             | Holidays
             |---------------------------------------------------------*/
            [
                'category' => 'holidays',
                'package_code' => 'GOA-BEACH-2026',
                'name' => 'Goa Beach Holiday Getaway',
                'destination' => 'Goa, India',
                'starting_city' => 'Goa',
                'ending_city' => 'Goa',
                'short_description' => 'A relaxed 5-day beach holiday covering North and South Goa\'s best beaches, forts and nightlife.',
                'description' => 'Unwind on the sun-kissed beaches of Goa with a well-balanced mix of beach time, water sports, heritage forts, and vibrant nightlife. Perfect for families, friends and couples alike.',
                'duration_days' => 5,
                'duration_nights' => 4,
                'difficulty_level' => 'easy',
                'age_min' => 3,
                'age_max' => 65,
                'best_time' => 'November to February',
                'featured' => true,
                'highlights' => ['Baga & Calangute beach visits', 'Water sports at Candolim', 'Fort Aguada sunset view', 'South Goa\'s quiet beaches', 'Optional river cruise with dinner'],
                'included_items' => ['4 nights resort accommodation', 'Daily breakfast', 'Airport/railway transfers', 'North & South Goa sightseeing', 'AC vehicle throughout'],
                'excluded_items' => ['Flights/train tickets', 'Lunch and dinner', 'Water sports charges', 'Entry fees at monuments'],
                'important_notes' => 'Carry sunscreen, swimwear and valid photo ID. Water sports subject to weather conditions.',
                'itinerary' => [
                    ['title' => 'Arrival & North Goa Beaches', 'location' => 'North Goa', 'description' => 'Arrive, check-in, evening visit to Baga and Calangute beach.', 'activities' => ['Hotel check-in', 'Beach visit']],
                    ['title' => 'Water Sports & Fort Aguada', 'location' => 'Candolim', 'description' => 'Morning water sports, evening sunset at Fort Aguada.', 'activities' => ['Water sports', 'Fort visit']],
                    ['title' => 'South Goa Exploration', 'location' => 'South Goa', 'description' => 'Visit Colva and Palolem beaches, relaxed day by the sea.', 'activities' => ['Beach hopping', 'Leisure time']],
                    ['title' => 'Old Goa Heritage & Local Markets', 'location' => 'Old Goa', 'description' => 'Visit Basilica of Bom Jesus, Se Cathedral and local markets.', 'activities' => ['Church visits', 'Shopping']],
                    ['title' => 'Departure', 'location' => 'Goa', 'description' => 'Leisure morning, transfer to airport/railway station.', 'activities' => ['Leisure', 'Departure']],
                ],
                'price' => 15999,
                'sale_price' => 13499,
                'capacity' => 20,
            ],
            [
                'category' => 'holidays',
                'package_code' => 'KERALA-HONEYMOON-2026',
                'name' => 'Kerala Backwaters & Munnar Honeymoon Package',
                'destination' => 'Kochi, Munnar & Alleppey, Kerala',
                'starting_city' => 'Kochi',
                'ending_city' => 'Kochi',
                'short_description' => 'A romantic 6-day escape through Kerala\'s tea gardens, misty hills and tranquil backwaters.',
                'description' => 'Designed for couples, this package combines the cool hills of Munnar with an overnight private houseboat stay on the Alleppey backwaters, along with candlelight dinners and couple-friendly stays.',
                'duration_days' => 6,
                'duration_nights' => 5,
                'difficulty_level' => 'easy',
                'age_min' => 18,
                'age_max' => 60,
                'best_time' => 'September to March',
                'featured' => true,
                'highlights' => ['Overnight private houseboat stay', 'Munnar tea garden tour', 'Candlelight dinner by the backwaters', 'Thekkady spice plantation visit', 'Couple-friendly resort stays'],
                'included_items' => ['5 nights accommodation incl. 1 night houseboat', 'Daily breakfast and dinner', 'One candlelight dinner', 'AC vehicle throughout', 'Airport transfers'],
                'excluded_items' => ['Flights/train tickets', 'Lunch', 'Spice plantation entry fees', 'Personal expenses'],
                'important_notes' => 'Houseboat check-in typically at noon; carry a valid photo ID for both travellers.',
                'itinerary' => [
                    ['title' => 'Arrival in Kochi', 'location' => 'Kochi', 'description' => 'Arrive in Kochi, visit Fort Kochi and Chinese fishing nets.', 'activities' => ['Fort Kochi tour', 'Hotel check-in']],
                    ['title' => 'Drive to Munnar', 'location' => 'Munnar', 'description' => 'Scenic drive to Munnar through tea estates and waterfalls.', 'activities' => ['Scenic drive', 'Waterfall stop']],
                    ['title' => 'Munnar Sightseeing', 'location' => 'Munnar', 'description' => 'Visit tea museum, Mattupetty Dam and Echo Point.', 'activities' => ['Tea museum', 'Sightseeing']],
                    ['title' => 'Munnar to Thekkady', 'location' => 'Thekkady', 'description' => 'Drive to Thekkady, visit a spice plantation.', 'activities' => ['Spice plantation tour']],
                    ['title' => 'Thekkady to Alleppey Houseboat', 'location' => 'Alleppey', 'description' => 'Drive to Alleppey, board a private houseboat for an overnight backwater cruise with candlelight dinner.', 'activities' => ['Houseboat check-in', 'Backwater cruise', 'Candlelight dinner']],
                    ['title' => 'Return to Kochi & Departure', 'location' => 'Kochi', 'description' => 'Disembark, drive back to Kochi for departure.', 'activities' => ['Return drive', 'Departure']],
                ],
                'price' => 24999,
                'sale_price' => 21999,
                'capacity' => 15,
            ],

            /* ---------------------------------------------------------
             | School & College
             |---------------------------------------------------------*/
            [
                'category' => 'school-college',
                'package_code' => 'RAJASTHAN-EDU-2026',
                'name' => 'Rajasthan Heritage Educational Tour',
                'destination' => 'Jaipur, Jodhpur & Udaipur, Rajasthan',
                'starting_city' => 'Jaipur',
                'ending_city' => 'Udaipur',
                'short_description' => 'A 6-day educational trip through Rajasthan\'s forts and palaces, curated for school and college groups.',
                'description' => 'A curriculum-friendly tour covering Rajasthan\'s history, architecture and culture, with guided educational sessions at major forts and palaces, ideal for school excursions and college study tours.',
                'duration_days' => 6,
                'duration_nights' => 5,
                'difficulty_level' => 'easy',
                'age_min' => 10,
                'age_max' => 22,
                'best_time' => 'October to March',
                'featured' => false,
                'highlights' => ['Guided educational tour of Amber Fort', 'Mehrangarh Fort history session', 'City Palace Udaipur visit', 'Group activities and cultural evening', 'Dedicated teacher/chaperone coordination'],
                'included_items' => ['5 nights dormitory/hotel accommodation', 'All meals (breakfast, lunch, dinner)', 'AC coach for group travel', 'Entry tickets to all monuments', 'Accompanying tour coordinator', 'First-aid support'],
                'excluded_items' => ['Travel to/from Jaipur', 'Personal expenses', 'Optional shopping'],
                'important_notes' => 'Minimum group size 20 students. Teacher-to-student ratio and ID documentation required as per school travel policy.',
                'itinerary' => [
                    ['title' => 'Arrival in Jaipur', 'location' => 'Jaipur', 'description' => 'Arrive in Jaipur, orientation session and city introduction.', 'activities' => ['Check-in', 'Orientation']],
                    ['title' => 'Amber Fort & City Palace', 'location' => 'Jaipur', 'description' => 'Guided educational visit to Amber Fort and City Palace with history session.', 'activities' => ['Fort tour', 'History session']],
                    ['title' => 'Jaipur to Jodhpur', 'location' => 'Jodhpur', 'description' => 'Travel to Jodhpur, visit Mehrangarh Fort.', 'activities' => ['Travel', 'Fort visit']],
                    ['title' => 'Jodhpur Exploration', 'location' => 'Jodhpur', 'description' => 'Explore the Blue City, Jaswant Thada and local markets.', 'activities' => ['City walk', 'Cultural session']],
                    ['title' => 'Jodhpur to Udaipur', 'location' => 'Udaipur', 'description' => 'Travel to Udaipur, evening boat ride on Lake Pichola.', 'activities' => ['Travel', 'Boat ride']],
                    ['title' => 'City Palace & Departure', 'location' => 'Udaipur', 'description' => 'Visit City Palace Udaipur, group photo session, departure.', 'activities' => ['Palace visit', 'Departure']],
                ],
                'price' => 9999,
                'sale_price' => 8499,
                'capacity' => 45,
            ],
            [
                'category' => 'school-college',
                'package_code' => 'MANALI-ADVENTURE-CAMP-2026',
                'name' => 'Manali Adventure Camp for Students',
                'destination' => 'Manali, Himachal Pradesh',
                'starting_city' => 'Delhi',
                'ending_city' => 'Delhi',
                'short_description' => 'A 5-day adventure and team-building camp in Manali designed for college and senior school groups.',
                'description' => 'Combining outdoor adventure with team-building, this camp includes river rafting, trekking, campfire nights and instructor-led activities in the mountains of Manali, ideal for college excursions.',
                'duration_days' => 5,
                'duration_nights' => 4,
                'difficulty_level' => 'moderate',
                'age_min' => 15,
                'age_max' => 25,
                'best_time' => 'March to June, September to November',
                'featured' => false,
                'highlights' => ['River rafting on the Beas', 'Guided trek to Solang Valley', 'Campfire and team-building games', 'Professional adventure instructors', 'Group accommodation with common areas'],
                'included_items' => ['4 nights camp/hotel accommodation', 'All meals', 'Adventure activity instructors and safety gear', 'Delhi-Manali-Delhi transport', 'Campfire and evening activities'],
                'excluded_items' => ['Personal trekking gear', 'Personal expenses', 'Any activity opted outside the itinerary'],
                'important_notes' => 'A signed parental/guardian consent form is required for all participants under 18. Basic fitness needed for trekking activities.',
                'itinerary' => [
                    ['title' => 'Delhi to Manali', 'location' => 'Manali', 'description' => 'Overnight/day travel from Delhi to Manali, evening check-in.', 'activities' => ['Travel', 'Check-in']],
                    ['title' => 'River Rafting', 'location' => 'Kullu', 'description' => 'Guided river rafting session on the Beas river with instructors.', 'activities' => ['River rafting', 'Safety briefing']],
                    ['title' => 'Solang Valley Trek', 'location' => 'Solang Valley', 'description' => 'Guided trek and outdoor team-building activities at Solang Valley.', 'activities' => ['Trekking', 'Team games']],
                    ['title' => 'Local Sightseeing & Campfire', 'location' => 'Manali', 'description' => 'Visit Hadimba Temple and Old Manali, evening campfire with music.', 'activities' => ['Sightseeing', 'Campfire night']],
                    ['title' => 'Return to Delhi', 'location' => 'Delhi', 'description' => 'Morning departure from Manali, arrive back in Delhi.', 'activities' => ['Return travel']],
                ],
                'price' => 11499,
                'sale_price' => 9999,
                'capacity' => 40,
            ],

            /* ---------------------------------------------------------
             | Business Trips
             |---------------------------------------------------------*/
            [
                'category' => 'business-trips',
                'package_code' => 'MUMBAI-BIZ-2026',
                'name' => 'Mumbai Corporate Business Tour',
                'destination' => 'Mumbai, Maharashtra',
                'starting_city' => 'Mumbai',
                'ending_city' => 'Mumbai',
                'short_description' => 'A 3-day efficient business travel package for corporate meetings and client visits in Mumbai.',
                'description' => 'Tailored for corporate travellers, this package includes premium airport transfers, business-class hotel stays near business districts, and flexible daytime scheduling for meetings.',
                'duration_days' => 3,
                'duration_nights' => 2,
                'difficulty_level' => 'easy',
                'age_min' => 18,
                'age_max' => 65,
                'best_time' => 'Year round',
                'featured' => false,
                'highlights' => ['Business-class hotel near BKC/Nariman Point', 'Priority airport transfers', 'Dedicated local coordinator', 'Flexible meeting-friendly schedule', 'High-speed Wi-Fi enabled stay'],
                'included_items' => ['2 nights business hotel accommodation', 'Airport pickup and drop', 'Daily breakfast', 'Local cab on call', 'Dedicated travel coordinator'],
                'excluded_items' => ['Flights/train tickets', 'Lunch and dinner', 'Meeting venue charges', 'Personal expenses'],
                'important_notes' => 'Corporate ID/company letter may be required for GST-billed invoices. Late check-out available on request.',
                'itinerary' => [
                    ['title' => 'Arrival & Client Meetings', 'location' => 'Mumbai (BKC)', 'description' => 'Airport pickup, check-in, afternoon free for client meetings.', 'activities' => ['Airport transfer', 'Check-in']],
                    ['title' => 'Business Day', 'location' => 'Mumbai', 'description' => 'Full day available for meetings with on-call cab service.', 'activities' => ['Meetings', 'Local transport on call']],
                    ['title' => 'Departure', 'location' => 'Mumbai', 'description' => 'Check-out and transfer to airport/railway station.', 'activities' => ['Check-out', 'Departure transfer']],
                ],
                'price' => 13999,
                'sale_price' => null,
                'capacity' => 12,
            ],
            [
                'category' => 'business-trips',
                'package_code' => 'BLR-TECHSUMMIT-2026',
                'name' => 'Bengaluru Tech Summit Business Package',
                'destination' => 'Bengaluru, Karnataka',
                'starting_city' => 'Bengaluru',
                'ending_city' => 'Bengaluru',
                'short_description' => 'A 4-day package for professionals attending conferences, summits and business meetings in Bengaluru.',
                'description' => 'Ideal for delegates attending tech and business conferences, this package bundles convenient stays near major convention centres with transport and concierge support.',
                'duration_days' => 4,
                'duration_nights' => 3,
                'difficulty_level' => 'easy',
                'age_min' => 18,
                'age_max' => 65,
                'best_time' => 'Year round',
                'featured' => false,
                'highlights' => ['Stay near major convention centres', 'Daily airport/venue transfers', 'Concierge and event support', 'Co-working lounge access', 'Networking dinner arrangement on request'],
                'included_items' => ['3 nights hotel accommodation', 'Daily breakfast', 'Venue and airport transfers', 'Concierge support', 'Local SIM/data assistance'],
                'excluded_items' => ['Flights/train tickets', 'Conference registration fees', 'Lunch and dinner', 'Personal expenses'],
                'important_notes' => 'Conference/event registration to be arranged separately by the traveller unless pre-booked.',
                'itinerary' => [
                    ['title' => 'Arrival in Bengaluru', 'location' => 'Bengaluru', 'description' => 'Airport pickup, check-in near the summit venue.', 'activities' => ['Airport transfer', 'Check-in']],
                    ['title' => 'Summit Day 1', 'location' => 'Bengaluru', 'description' => 'Transfer to venue for conference/summit sessions.', 'activities' => ['Venue transfer', 'Conference attendance']],
                    ['title' => 'Summit Day 2 & Networking', 'location' => 'Bengaluru', 'description' => 'Continue summit sessions, optional evening networking dinner.', 'activities' => ['Conference attendance', 'Networking dinner']],
                    ['title' => 'Departure', 'location' => 'Bengaluru', 'description' => 'Check-out and transfer to airport/railway station.', 'activities' => ['Check-out', 'Departure transfer']],
                ],
                'price' => 17999,
                'sale_price' => 15999,
                'capacity' => 15,
            ],

            /* ---------------------------------------------------------
             | Monthly Tours
             |---------------------------------------------------------*/
            [
                'category' => 'monthly-tours',
                'package_code' => 'KASHMIR-MONTHLY-2026',
                'name' => 'Kashmir Valley Monthly Special',
                'destination' => 'Srinagar, Gulmarg & Pahalgam, Kashmir',
                'starting_city' => 'Srinagar',
                'ending_city' => 'Srinagar',
                'short_description' => 'A curated 6-day monthly departure tour through Kashmir\'s valleys, meadows and iconic houseboats.',
                'description' => 'A fixed monthly-departure tour through the "Paradise on Earth" — houseboat stay on Dal Lake, cable car ride in Gulmarg and the scenic meadows of Pahalgam, guaranteed departure every month.',
                'duration_days' => 6,
                'duration_nights' => 5,
                'difficulty_level' => 'easy',
                'age_min' => 5,
                'age_max' => 70,
                'best_time' => 'April to October',
                'featured' => true,
                'highlights' => ['One night houseboat stay on Dal Lake', 'Gondola cable car ride in Gulmarg', 'Betaab Valley & Pahalgam sightseeing', 'Shikara ride at sunset', 'Guaranteed monthly departures'],
                'included_items' => ['5 nights accommodation incl. houseboat', 'Daily breakfast and dinner', 'AC vehicle throughout', 'Gondola ride (Phase 1)', 'Shikara ride'],
                'excluded_items' => ['Flights/train tickets', 'Lunch', 'Gondola Phase 2 charges', 'Personal expenses'],
                'important_notes' => 'Fixed monthly departure dates — early booking recommended as seats are limited. Warm clothing advised for Gulmarg.',
                'itinerary' => [
                    ['title' => 'Arrival in Srinagar', 'location' => 'Srinagar', 'description' => 'Arrive, transfer to houseboat, evening Shikara ride on Dal Lake.', 'activities' => ['Houseboat check-in', 'Shikara ride']],
                    ['title' => 'Gulmarg Excursion', 'location' => 'Gulmarg', 'description' => 'Day trip to Gulmarg, Gondola cable car ride.', 'activities' => ['Gondola ride', 'Sightseeing']],
                    ['title' => 'Srinagar to Pahalgam', 'location' => 'Pahalgam', 'description' => 'Drive to Pahalgam, visit Betaab Valley en route.', 'activities' => ['Scenic drive', 'Valley visit']],
                    ['title' => 'Pahalgam Sightseeing', 'location' => 'Pahalgam', 'description' => 'Explore Aru Valley and Chandanwari.', 'activities' => ['Local sightseeing']],
                    ['title' => 'Return to Srinagar & Mughal Gardens', 'location' => 'Srinagar', 'description' => 'Drive back to Srinagar, visit Mughal Gardens.', 'activities' => ['Garden visit', 'Local market']],
                    ['title' => 'Departure', 'location' => 'Srinagar', 'description' => 'Transfer to airport for departure.', 'activities' => ['Departure transfer']],
                ],
                'price' => 19999,
                'sale_price' => 17499,
                'capacity' => 25,
            ],
            [
                'category' => 'monthly-tours',
                'package_code' => 'HIMACHAL-MONSOON-2026',
                'name' => 'Himachal Monsoon Monthly Tour',
                'destination' => 'Shimla & Manali, Himachal Pradesh',
                'starting_city' => 'Chandigarh',
                'ending_city' => 'Chandigarh',
                'short_description' => 'A refreshing 5-day monthly-departure tour through misty hills, waterfalls and pine forests of Himachal.',
                'description' => 'Escape to the cool hills of Shimla and Manali on this guaranteed monthly departure tour, featuring scenic waterfalls, colonial architecture and orchard valleys — best enjoyed during the lush monsoon greenery.',
                'duration_days' => 5,
                'duration_nights' => 4,
                'difficulty_level' => 'easy',
                'age_min' => 5,
                'age_max' => 70,
                'best_time' => 'July to September',
                'featured' => false,
                'highlights' => ['Mall Road & Ridge walk in Shimla', 'Kufri excursion', 'Manali orchard valley drive', 'Waterfall photo stops', 'Guaranteed monthly departures'],
                'included_items' => ['4 nights hotel accommodation', 'Daily breakfast and dinner', 'AC vehicle throughout', 'Chandigarh transfers', 'Local sightseeing as per itinerary'],
                'excluded_items' => ['Flights/train tickets to Chandigarh', 'Lunch', 'Kufri activities (horse riding etc.)', 'Personal expenses'],
                'important_notes' => 'Monsoon travel — occasional landslide-related route changes possible; itinerary may be adjusted for safety.',
                'itinerary' => [
                    ['title' => 'Chandigarh to Shimla', 'location' => 'Shimla', 'description' => 'Pickup from Chandigarh, drive to Shimla, evening Mall Road walk.', 'activities' => ['Transfer', 'Mall Road walk']],
                    ['title' => 'Shimla to Kufri', 'location' => 'Kufri', 'description' => 'Day excursion to Kufri, back to Shimla by evening.', 'activities' => ['Kufri excursion']],
                    ['title' => 'Shimla to Manali', 'location' => 'Manali', 'description' => 'Scenic drive to Manali with waterfall photo stops.', 'activities' => ['Scenic drive', 'Photo stops']],
                    ['title' => 'Manali Sightseeing', 'location' => 'Manali', 'description' => 'Visit Hadimba Temple, Old Manali and orchard valley.', 'activities' => ['Local sightseeing']],
                    ['title' => 'Return to Chandigarh', 'location' => 'Chandigarh', 'description' => 'Morning departure from Manali, drop at Chandigarh.', 'activities' => ['Return drive', 'Departure']],
                ],
                'price' => 14499,
                'sale_price' => 12999,
                'capacity' => 30,
            ],
        ];

        foreach ($packages as $index => $data) {
            $categoryId = $categories[$data['category']] ?? null;

            $slug = Str::slug($data['name']);

            $itinerary = collect($data['itinerary'])
                ->values()
                ->map(function (array $day, int $dayIndex) {
                    return [
                        'day' => $dayIndex + 1,
                        'title' => $day['title'],
                        'location' => $day['location'],
                        'description' => $day['description'],
                        'image' => null,
                        'activities' => $day['activities'],
                    ];
                })
                ->all();

            $tour = TourPackage::updateOrCreate(
                ['package_code' => $data['package_code']],
                [
                    'category_id' => $categoryId,
                    'name' => $data['name'],
                    'slug' => $slug,
                    'destination' => $data['destination'],
                    'starting_city' => $data['starting_city'],
                    'ending_city' => $data['ending_city'],
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'duration_days' => $data['duration_days'],
                    'duration_nights' => $data['duration_nights'],
                    'difficulty_level' => $data['difficulty_level'],
                    'age_min' => $data['age_min'],
                    'age_max' => $data['age_max'],
                    'best_time' => $data['best_time'],
                    'highlights' => $data['highlights'],
                    'included_items' => $data['included_items'],
                    'excluded_items' => $data['excluded_items'],
                    'itinerary' => $itinerary,
                    'important_notes' => $data['important_notes'],
                    'terms_conditions' => self::TERMS,
                    'cancellation_policy' => self::CANCELLATION,
                    'privacy_policy' => self::PRIVACY,
                    'cover_image' => "https://picsum.photos/seed/{$slug}/1200/800",
                    'featured' => $data['featured'],
                    'status' => TourPackage::STATUS_PUBLISHED,
                    'seo_key' => $slug,
                    'meta_title' => $data['name'],
                    'meta_description' => $data['short_description'],
                    'meta_keywords' => implode(', ', $data['highlights']),
                    'robots' => 'index,follow',
                    'created_by' => $admin->id,
                ]
            );

            // Gallery images
            if ($tour->images()->count() === 0) {
                foreach (range(1, 3) as $i) {
                    $tour->images()->create([
                        'image_path' => "https://picsum.photos/seed/{$slug}-{$i}/1000/700",
                        'alt_text' => $data['name'],
                        'sort_order' => $i,
                    ]);
                }
            }

            // Two guaranteed-open, future-dated departures per package.
            $departureOffsets = [21, 45];

            foreach ($departureOffsets as $offset) {
                $departureDate = $today->copy()->addDays($offset + $index);
                $returnDate = $departureDate->copy()->addDays(max(0, $data['duration_days'] - 1));

                $tour->departures()->updateOrCreate(
                    ['departure_date' => $departureDate->toDateString()],
                    [
                        'return_date' => $returnDate->toDateString(),
                        'capacity' => $data['capacity'],
                        'price' => $data['price'],
                        'sale_price' => $data['sale_price'],
                        'currency' => 'INR',
                        'meeting_point' => $data['starting_city'] . ' — main pickup point',
                        'status' => 'open',
                    ]
                );
            }
        }
    }
}
