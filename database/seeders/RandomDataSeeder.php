<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Backend\Merchant;
use App\Models\MerchantShops;
use App\Models\Backend\MerchantDeliveryCharge;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\Parcel;
use App\Models\Backend\ParcelEvent;
use App\Models\Backend\Account;
use App\Models\Backend\Hub;
use App\Models\Backend\DeliveryCharge;
use App\Models\Backend\Deliverycategory;
use App\Models\Backend\Packaging;
use App\Enums\Status;
use App\Enums\UserType;
use App\Enums\ParcelStatus;
use App\Enums\DeliveryTime;

class RandomDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        
        echo "Starting random data generation...\n";
        
        // Generate random merchants
        $this->generateMerchants($faker);
        
        // Generate random delivery men
        $this->generateDeliveryMen($faker);
        
        // Generate random parcels
        $this->generateParcels($faker);
        
        // Generate random financial data
        $this->generateFinancialData($faker);
        
        echo "Random data generation completed!\n";
    }
    
    private function generateMerchants($faker)
    {
        echo "Generating merchants...\n";
        
        for ($i = 0; $i < 15; $i++) {
            // Create merchant user
            $merchantUser = new User();
            $merchantUser->name = $faker->company;
            $merchantUser->mobile = $this->generateLibyaPhone($faker);
            $merchantUser->email = $faker->unique()->companyEmail;
            $merchantUser->address = $this->randomLibyaAddress($faker);
            $merchantUser->password = Hash::make('12345678');
            $merchantUser->user_type = UserType::MERCHANT;
            $merchantUser->hub_id = $faker->numberBetween(1, 6);
            $merchantUser->image_id = $faker->numberBetween(1, 10);
            $merchantUser->save();
            
            // Create merchant profile
            $merchant = new Merchant();
            $merchant->user_id = $merchantUser->id;
            $merchant->business_name = $faker->company;
            $merchant->merchant_unique_id = $faker->unique()->numberBetween(100000, 999999);
            $merchant->current_balance = $faker->randomFloat(2, 0, 10000);
            $merchant->opening_balance = $faker->randomFloat(2, 0, 5000);
            $merchant->cod_charges = [
                'inside_city' => $faker->numberBetween(1, 3),
                'sub_city' => $faker->numberBetween(2, 5),
                'outside_city' => $faker->numberBetween(3, 8),
            ];
            $merchant->nid_id = $faker->numberBetween(1, 10);
            $merchant->trade_license = $faker->numberBetween(1, 10);
            $merchant->address = $this->randomLibyaAddress($faker);
            $merchant->save();
            
            // Create merchant shops
            $shopCount = $faker->numberBetween(1, 3);
            for ($j = 0; $j < $shopCount; $j++) {
                $shop = new MerchantShops();
                $shop->merchant_id = $merchant->id;
                $shop->name = $faker->company . ' Branch ' . ($j + 1);
                $shop->contact_no = $this->generateLibyaPhone($faker);
                $shop->address = $this->randomLibyaAddress($faker);
                $shop->save();
            }
            
            // Create merchant delivery charges
            $deliveryCharges = DeliveryCharge::with('category')->orderBy('position')->get();
            if (!blank($deliveryCharges)) {
                foreach ($deliveryCharges as $delivery) {
                    $merchantDeliveryCharge = new MerchantDeliveryCharge();
                    $merchantDeliveryCharge->merchant_id = $merchant->id;
                    $merchantDeliveryCharge->delivery_charge_id = $delivery->id;
                    $merchantDeliveryCharge->category_id = $delivery->category_id;
                    $merchantDeliveryCharge->weight = $delivery->weight;
                    $merchantDeliveryCharge->same_day = $delivery->same_day + $faker->numberBetween(-5, 10);
                    $merchantDeliveryCharge->next_day = $delivery->next_day + $faker->numberBetween(-5, 10);
                    $merchantDeliveryCharge->sub_city = $delivery->sub_city + $faker->numberBetween(-5, 10);
                    $merchantDeliveryCharge->outside_city = $delivery->outside_city + $faker->numberBetween(-5, 10);
                    $merchantDeliveryCharge->status = Status::ACTIVE;
                    $merchantDeliveryCharge->save();
                }
            }
        }
    }
    
    private function generateDeliveryMen($faker)
    {
        echo "Generating delivery men...\n";
        
        for ($i = 0; $i < 20; $i++) {
            // Create delivery man user
            $deliveryUser = new User();
            $deliveryUser->name = $faker->name;
            $deliveryUser->mobile = $this->generateLibyaPhone($faker);
            $deliveryUser->email = $faker->unique()->email;
            $deliveryUser->address = $this->randomLibyaAddress($faker);
            $deliveryUser->hub_id = $faker->numberBetween(1, 6);
            $deliveryUser->password = Hash::make('12345678');
            $deliveryUser->user_type = UserType::DELIVERYMAN;
            $deliveryUser->salary = $faker->numberBetween(5000, 15000);
            $deliveryUser->image_id = $faker->numberBetween(1, 10);
            $deliveryUser->save();
            
            // Create delivery man profile
            $deliveryMan = new DeliveryMan();
            $deliveryMan->user_id = $deliveryUser->id;
            $deliveryMan->status = $faker->randomElement([Status::ACTIVE, Status::INACTIVE]);
            $deliveryMan->delivery_charge = $faker->numberBetween(20, 50);
            $deliveryMan->pickup_charge = $faker->numberBetween(10, 30);
            $deliveryMan->return_charge = $faker->numberBetween(5, 20);
            $deliveryMan->current_balance = $faker->randomFloat(2, 0, 5000);
            $deliveryMan->opening_balance = $faker->randomFloat(2, 0, 2000);
            $deliveryMan->driving_license_image_id = $faker->numberBetween(1, 10);
            $deliveryMan->save();
        }
    }
    
    private function generateParcels($faker)
    {
        echo "Generating parcels...\n";
        
        $merchants = Merchant::with('merchantShops')->get();
        $deliveryMen = DeliveryMan::all();
        $categories = Deliverycategory::all();
        $packagings = Packaging::all();
        
        // Filter merchants that have shops
        $merchantsWithShops = $merchants->filter(function($merchant) {
            return $merchant->merchantShops->count() > 0;
        });
        
        if ($merchantsWithShops->count() == 0) {
            echo "No merchants with shops found. Skipping parcel generation.\n";
            return;
        }
        
        for ($i = 0; $i < 100; $i++) {
            $merchant = $faker->randomElement($merchantsWithShops);
            $shop = $faker->randomElement($merchant->merchantShops);
            $category = $faker->randomElement($categories);
            $packaging = $faker->randomElement($packagings);
            
            // Skip if any required data is missing
            if (!$merchant || !$shop || !$category || !$packaging) {
                continue;
            }
            
            // Ensure we have a valid category_id
            if (!$category) {
                $category = Deliverycategory::first();
                if (!$category) {
                    continue; // Skip if no categories exist
                }
            }
            
            $parcel = new Parcel();
            $parcel->merchant_id = $merchant->id;
            $parcel->merchant_shop_id = $shop->id;
            $parcel->pickup_address = $this->randomLibyaAddress($faker);
            $parcel->pickup_phone = $this->generateLibyaPhone($faker);
            $parcel->customer_name = $faker->name;
            $parcel->customer_phone = $this->generateLibyaPhone($faker);
            $parcel->customer_address = $this->randomLibyaAddress($faker);
            $parcel->invoice_no = $faker->unique()->numberBetween(100000, 999999);
            $parcel->category_id = $category->id;
            $parcel->weight = $faker->numberBetween(1, 20);
            $parcel->delivery_type_id = $faker->numberBetween(1, 4); // 1=SAMEDAY, 2=NEXTDAY, 3=SUBCITY, 4=OUTSIDECITY
            $parcel->packaging_id = $packaging->id;
            $parcel->cash_collection = $faker->numberBetween(100, 5000);
            $parcel->selling_price = $faker->numberBetween(100, 5000);
            $parcel->liquid_fragile_amount = $faker->numberBetween(0, 50);
            $parcel->packaging_amount = $faker->numberBetween(10, 100);
            $parcel->delivery_charge = $faker->numberBetween(30, 200);
            $parcel->cod_charge = $faker->numberBetween(1, 5);
            $parcel->priority_type_id = $faker->numberBetween(1, 3);
            $parcel->cod_amount = $faker->numberBetween(5, 50);
            $parcel->vat = 10;
            $parcel->vat_amount = $faker->randomFloat(2, 5, 100);
            $parcel->total_delivery_amount = $faker->randomFloat(2, 50, 500);
            $parcel->current_payable = $faker->randomFloat(2, 100, 1000);
            $parcel->tracking_id = 'WE' . substr(strtotime(date('H:i:s')), 1) . 'C' . $faker->numberBetween(1, 999) . $i;
            $parcel->note = $faker->sentence;
            $parcel->status = $faker->randomElement([
                ParcelStatus::PENDING,
                ParcelStatus::PICKUP_ASSIGN,
                ParcelStatus::RECEIVED_BY_PICKUP_MAN,
                ParcelStatus::RECEIVED_WAREHOUSE,
                ParcelStatus::DELIVERY_MAN_ASSIGN,
                ParcelStatus::DELIVERY_RE_SCHEDULE,
                ParcelStatus::RETURN_TO_COURIER,
                ParcelStatus::PARTIAL_DELIVERED,
                ParcelStatus::DELIVERED,
                ParcelStatus::RETURNED_MERCHANT
            ]);
            
            // Set dates based on status
            $parcel->pickup_date = $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d');
            if (in_array($parcel->status, [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])) {
                $parcel->delivery_date = $faker->dateTimeBetween($parcel->pickup_date, 'now')->format('Y-m-d');
            }
            
            $parcel->save();
            
            // Generate parcel events
            $this->generateParcelEvents($parcel, $faker, $deliveryMen);
        }
    }
    
    private function generateParcelEvents($parcel, $faker, $deliveryMen)
    {
        $userId = User::orderBy('id')->value('id') ?? 1;
        $statuses = [
            ParcelStatus::PENDING => 'Parcel created',
            ParcelStatus::PICKUP_ASSIGN => 'Pickup assigned to delivery man',
            ParcelStatus::RECEIVED_BY_PICKUP_MAN => 'Parcel received by pickup man',
            ParcelStatus::RECEIVED_WAREHOUSE => 'Parcel received at warehouse',
            ParcelStatus::DELIVERY_MAN_ASSIGN => 'Delivery assigned to delivery man',
            ParcelStatus::DELIVERY_RE_SCHEDULE => 'Delivery rescheduled',
            ParcelStatus::RETURN_TO_COURIER => 'Parcel returned to courier',
            ParcelStatus::PARTIAL_DELIVERED => 'Parcel partially delivered',
            ParcelStatus::DELIVERED => 'Parcel delivered successfully',
            ParcelStatus::RETURNED_MERCHANT => 'Parcel returned to merchant'
        ];
        
        $currentStatus = $parcel->status;
        $eventCount = $faker->numberBetween(1, 5);
        
        for ($i = 0; $i < $eventCount; $i++) {
            $event = new ParcelEvent();
            $event->parcel_id = $parcel->id;
            $event->delivery_man_id = $faker->randomElement($deliveryMen)->id;
            $event->pickup_man_id = $faker->randomElement($deliveryMen)->id;
            $event->hub_id = $faker->numberBetween(1, 6);
            $event->transfer_delivery_man_id = $faker->randomElement($deliveryMen)->id;
            $event->note = $faker->sentence;
            $event->parcel_status = $faker->randomElement(array_keys($statuses));
            $event->created_by = $userId;
            $event->created_at = $faker->dateTimeBetween($parcel->created_at, 'now');
            $event->save();
        }
    }
    
    private function generateFinancialData($faker)
    {
        echo "Generating financial data...\n";
        
        // Generate accounts
        for ($i = 0; $i < 10; $i++) {
            $account = new Account();
            $account->type = $faker->randomElement([1, 2]); // 1=Admin, 2=User
            $account->user_id = User::inRandomOrder()->value('id') ?? User::orderBy('id')->value('id');
            $account->gateway = $faker->numberBetween(1, 3);
            $account->balance = $faker->randomFloat(2, 0, 100000);
            $account->account_holder_name = $faker->name;
            $account->account_no = $faker->unique()->bankAccountNumber;
            $account->bank = $faker->numberBetween(1, 5);
            $account->branch_name = $faker->city;
            $account->opening_balance = $faker->randomFloat(2, 0, 50000);
            $account->mobile = $faker->phoneNumber;
            $account->account_type = $faker->numberBetween(1, 3);
            $account->status = Status::ACTIVE;
            $account->save();
        }
        
        echo "Financial data generation completed.\n";
    }

    // Libya-specific helpers
    private function generateLibyaPhone($faker)
    {
        // Common Libyan mobile prefixes: 091, 092, 094
        $prefixes = ['091', '092', '094'];
        $prefix = $faker->randomElement($prefixes);
        // 7 remaining digits
        return $prefix . $faker->numerify('#######');
    }

    private function randomLibyaAddress($faker)
    {
        $cities = ['Tripoli', 'Benghazi', 'Misrata', 'Sabha', 'Zawiya', 'Zliten'];
        $city = $faker->randomElement($cities);
        return $city . ', Libya';
    }
}
