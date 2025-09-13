<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Backend\Parcel;
use App\Models\Backend\CollectionPeriod;
use App\Models\Backend\CollectionSession;
use App\Models\Backend\ParcelCollectionAssignment;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\Merchant;
use App\Models\User;
use App\Models\Backend\Hub;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LibyanTestDataSeeder extends Seeder
{
    private $libyanCities = [
        'Tripoli', 'Benghazi', 'Misrata', 'Zawiya', 'Bayda', 'Gharyan',
        'Tobruk', 'Ajdabiya', 'Zliten', 'Derna', 'Sirte', 'Sabha'
    ];

    private $libyanAreas = [
        // Tripoli areas
        'Hay Andalus', 'Souq al-Juma', 'Tajoura', 'Ain Zara', 'Janzour', 'Qargaresh',
        'Fashloum', 'Dahra', 'Gurji', 'Abu Salim', 'Hadba Project', 'Khallet al-Furjan',
        
        // Benghazi areas  
        'Al-Hawari', 'Sidi Hussein', 'Al-Sabri', 'Qanfouda', 'Al-Uruba', 'Salmani',
        'Berenice', 'Al-Fuwayhat', 'Sidi Faraj', 'Al-Mahdiya',
        
        // Misrata areas
        'Central Misrata', 'Tamenhint', 'Qasr Ahmad', 'Al-Giran', 'Zliten Road'
    ];

    private $libyanNames = [
        'Ahmed Al-Mansouri', 'Fatima Benali', 'Omar Khalifa', 'Aisha Sharif',
        'Mohamed Al-Ghazzawi', 'Khadija Al-Majbri', 'Ali Ben Omar', 'Mariam Fezzani',
        'Mustafa Al-Zwai', 'Salma Belgasem', 'Ibrahim Gaddafi', 'Nadia Al-Senussi',
        'Youssef Al-Misrati', 'Amina Bourguiba', 'Khalil Al-Warfalli', 'Zeinab Shariff'
    ];

    private $libyanCompanies = [
        'Al-Madar Telecoms', 'Libyana Mobile', 'Mellitah Oil & Gas', 'Al-Waha Oil Company',
        'Sirte Oil Company', 'Zueitina Oil Company', 'Bank of Commerce & Development',
        'Wahda Bank', 'Sahara Bank', 'National Commercial Bank', 'Al-Jumhouria Bank',
        'Libya Steel Company', 'Al-Nasr Automotive', 'Tripoli International Fair',
        'Benghazi Medical Center', 'Al-Andalus University', 'Tripoli University Hospital'
    ];

    public function run(): void
    {
        $this->command->info('🇱🇾 Creating Libyan test data for collection management...');
        
        // Create Libyan delivery men
        $deliveryMen = $this->createLibyanDeliveryMen();
        
        // Create Libyan merchants and parcels
        $this->createLibyanMerchantsAndParcels();

        // Ensure collection periods exist and then create sessions
        $this->createLibyanCollectionPeriods();
        $this->createCollectionSessionsWithAssignments($deliveryMen);

        $this->command->info('✅ Libyan test data created successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Collection Periods: ' . CollectionPeriod::count());
        $this->command->info('   - Total Parcels: ' . Parcel::count());
        $this->command->info('   - Collection Sessions: ' . CollectionSession::count());
        $this->command->info('   - Delivery Men: ' . DeliveryMan::count());
    }

    private function createLibyanCollectionPeriods()
    {
        $periods = [
            [
                'name' => 'Morning Collection (صباحي)',
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'description' => 'Morning collection period before Dhuhr prayer - suitable for business areas in Tripoli and Benghazi',
                'is_active' => true
            ],
            [
                'name' => 'Afternoon Collection (بعد الظهر)',
                'start_time' => '14:30:00',
                'end_time' => '17:00:00',
                'description' => 'Afternoon collection after prayer break - covers residential areas and souqs',
                'is_active' => true
            ],
            [
                'name' => 'Evening Collection (مسائي)',
                'start_time' => '19:00:00',
                'end_time' => '21:00:00',
                'description' => 'Evening collection for urgent and express deliveries',
                'is_active' => true
            ],
            [
                'name' => 'Friday Special (جمعة)',
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'description' => 'Special Friday collection period after Jummah prayer',
                'is_active' => false
            ]
        ];

        foreach ($periods as $period) {
            CollectionPeriod::firstOrCreate(
                ['name' => $period['name']],
                $period
            );
        }

        $this->command->info('📅 Created Libyan collection periods');
    }

    private function createLibyanDeliveryMen()
    {
        $deliveryMen = [];
        
        for ($i = 0; $i < 8; $i++) {
            $name = $this->libyanNames[array_rand($this->libyanNames)];
            $email = strtolower(str_replace([' ', '-'], ['', ''], $name)) . rand(100, 999) . '@libya-post.ly';
            
            // Create user first
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('password123'),
                'role_id' => 2, // Admin role
                'user_type' => 3, // Delivery man type
                'hub_id' => Hub::inRandomOrder()->first()->id ?? 1
            ]);

            // Create delivery man with correct table structure
            $deliveryMan = DeliveryMan::create([
                'user_id' => $user->id,
                'status' => 1,
                'delivery_charge' => rand(5, 15), // LYD per delivery
                'pickup_charge' => rand(3, 10), // LYD per pickup
                'return_charge' => rand(8, 20), // LYD per return
                'current_balance' => rand(100, 1000), // Current balance in LYD
                'opening_balance' => rand(0, 500), // Opening balance
                'delivery_lat' => number_format(rand(3200, 3300) / 100, 6), // Libya latitude range
                'delivery_long' => number_format(rand(1300, 2500) / 100, 6), // Libya longitude range
            ]);

            $deliveryMen[] = $deliveryMan;
        }

        $this->command->info('🚚 Created ' . count($deliveryMen) . ' Libyan delivery men');
        return $deliveryMen;
    }

    private function createLibyanMerchantsAndParcels()
    {
        // Create some Libyan merchants
        for ($i = 0; $i < 5; $i++) {
            $companyName = $this->libyanCompanies[array_rand($this->libyanCompanies)];
            $email = strtolower(str_replace([' ', '&', '-'], ['', '', ''], $companyName)) . '@business.ly';
            
            $user = User::create([
                'name' => $companyName,
                'email' => $email,
                'password' => bcrypt('password123'),
                'role_id' => 2,
                'user_type' => 2, // Merchant type
                'hub_id' => Hub::inRandomOrder()->first()->id ?? 1
            ]);

            $merchant = Merchant::create([
                'user_id' => $user->id,
                'business_name' => $companyName,
                'current_balance' => rand(1000, 50000),
                'opening_balance' => rand(5000, 20000),
                'address' => $this->libyanAreas[array_rand($this->libyanAreas)] . ', ' . $this->libyanCities[array_rand($this->libyanCities)],
                'status' => 1
            ]);

            // Create parcels for this merchant
            $this->createParcelsForMerchant($merchant, rand(15, 30));
        }

        $this->command->info('📦 Created Libyan merchants and parcels');
    }

    private function createParcelsForMerchant($merchant, $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $recipientCity = $this->libyanCities[array_rand($this->libyanCities)];
            $recipientArea = $this->libyanAreas[array_rand($this->libyanAreas)];
            $recipientName = $this->libyanNames[array_rand($this->libyanNames)];
            
            Parcel::create([
                'merchant_id' => $merchant->id,
                'merchant_shop_id' => 1, // Default shop
                'hub_id' => $merchant->user->hub_id,
                'first_hub_id' => $merchant->user->hub_id,
                'pickup_date' => Carbon::now()->subDays(rand(0, 7)),
                'pickup_address' => $this->libyanAreas[array_rand($this->libyanAreas)] . ', ' . $this->libyanCities[array_rand($this->libyanCities)],
                'pickup_phone' => '0' . rand(90, 99) . rand(1000000, 9999999),
                'customer_name' => $recipientName,
                'customer_phone' => '0' . rand(90, 99) . rand(1000000, 9999999),
                'customer_address' => $recipientArea . ', ' . $recipientCity,
                'weight' => rand(1, 10),
                'delivery_charge' => rand(10, 50),
                'cod_charge' => rand(0, 200),
                'cod_amount' => rand(0, 200),
                'total_delivery_amount' => rand(10, 250),
                'current_payable' => rand(10, 250),
                'cash_collection' => rand(0, 200),
                'selling_price' => rand(50, 500),
                'invoice_no' => 'LY' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'tracking_id' => 'LY' . rand(100000000, 999999999),
                'note' => 'Package for ' . $recipientName . ' in ' . $recipientCity,
                'status' => rand(1, 15), // Various parcel statuses
                'delivery_type_id' => rand(1, 3),
                'packaging_id' => rand(1, 5),
                'category_id' => rand(1, 5),
                'distance_km' => rand(5, 100),
                'pricing_method' => 'distance',
                'vat' => 0,
                'vat_amount' => 0,
                'partial_delivered' => 0,
                'return_to_courier' => 0,
                'collection_status' => 'pending'
            ]);
        }
    }

    private function createCollectionSessionsWithAssignments($deliveryMen)
    {
        // Use collection_periods
        $periods = CollectionPeriod::all();
        
        foreach ($periods as $period) {
            // Create sessions for the last 5 days
            for ($day = 0; $day < 5; $day++) {
                $sessionDate = Carbon::now()->subDays($day);
                
                // Times from period
                $startTime = Carbon::parse($period->start_time)->format('H:i:s');
                $endTime = Carbon::parse($period->end_time)->format('H:i:s');
                
                $session = CollectionSession::create([
                    'collection_period_id' => $period->id,
                    'collection_date' => $sessionDate->format('Y-m-d'),
                    'started_at' => $sessionDate->copy()->setTimeFromTimeString($startTime),
                    'completed_at' => $day === 0 ? null : $sessionDate->copy()->setTimeFromTimeString($endTime),
                    'status' => $day === 0 ? 'active' : 'completed',
                    'total_parcels' => 0,
                    'assigned_parcels' => 0,
                    'unassigned_parcels' => 0,
                    'notes' => 'Collection session for ' . $period->name . ' on ' . $sessionDate->format('Y-m-d')
                ]);

                // Add some parcels to the session
                $parcels = Parcel::where('status', '!=', 15) // Not delivered
                    ->inRandomOrder()
                    ->limit(rand(5, 15))
                    ->get();

                foreach ($parcels as $parcel) {
                    $assignment = ParcelCollectionAssignment::create([
                        'parcel_id' => $parcel->id,
                        'collection_session_id' => $session->id,
                        'collected_at' => $sessionDate->copy()->addMinutes(rand(10, 120)),
                        'assignment_status' => 'collected',
                        'priority' => rand(-1, 1),
                        'notes' => 'Collected for ' . ($parcel->customer_name ?? 'customer') . ' at ' . ($parcel->customer_address ?? 'address')
                    ]);

                    // Randomly assign some parcels to delivery men
                    if (rand(0, 100) > 40) { // 60% chance of assignment
                        $deliveryMan = $deliveryMen[array_rand($deliveryMen)];
                        $assignment->assignTo($deliveryMan->id, rand(-1, 1));
                    }
                }

                // Update session counters
                $session->updateCounts();

                // Update parcel collection session references
                foreach ($session->parcelAssignments as $assignment) {
                    $assignment->parcel->update([
                        'collection_session_id' => $session->id,
                        'collected_at' => $assignment->collected_at
                    ]);
                }
            }
        }

        $this->command->info('📋 Created collection sessions with parcel assignments');
    }
}
