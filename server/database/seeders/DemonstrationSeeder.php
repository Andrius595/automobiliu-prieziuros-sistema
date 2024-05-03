<?php

namespace Database\Seeders;

use App\Config\PermissionsConfig;
use App\Models\Appointment;
use App\Models\Car;
use App\Models\City;
use App\Models\Record;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DemonstrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemAdmin = Role::create(['name' => PermissionsConfig::SYSTEM_ADMIN_ROLE]);
        $client = Role::create(['name' => PermissionsConfig::CLIENT_ROLE]);
        $serviceEmployee = Role::create(['name' => PermissionsConfig::SERVICE_EMPLOYEE_ROLE]);
        $serviceAdmin = Role::create(['name' => PermissionsConfig::SERVICE_ADMIN_ROLE]);

        // Cities
        $kaunas = City::create([
            'name' => 'Kaunas',
        ]);
        $vilnius = City::create([
            'name' => 'Vilnius',
        ]);
        $klaipeda = City::create([
            'name' => 'Klaipėda',
        ]);

        ServiceCategory::create([
            'name' => 'Tepalų keitimas variklyje',
        ]);
        ServiceCategory::create([
            'name' => 'Tepalų keitimas pavarų dėžėje',
        ]);
        ServiceCategory::create([
            'name' => 'Priekinio stiklo keitimas',
        ]);
        ServiceCategory::create([
            'name' => 'Ratų balansavimas',
        ]);
        ServiceCategory::create([
            'name' => 'Ratų/padangų montavimas',
        ]);
        ServiceCategory::create([
            'name' => 'Automobilio techninė apžiūra',
        ]);
        ServiceCategory::create([
            'name' => 'Turbinos remontas',
        ]);
        ServiceCategory::create([
            'name' => 'Kondicionieriaus pildymas',
        ]);
        ServiceCategory::create([
            'name' => 'Centrinio užrakto remontas',
        ]);

        $service = Service::create([
            'title' => 'Greitųjų Ratų Remontas',
            'address' => 'Taikos pr. 1',
            'city_id' => $kaunas->id,
            'description' => 'Specializuojamės greito ir efektyvaus automobilių ratų remonto paslaugose. Su mūsų profesionalia komanda jūsų automobilio ratų bėdos bus išspręstos greitai ir patikimai.',
        ]);

        $clientUser = User::factory()->create([
            'first_name' => 'Jonas',
            'last_name' => 'Jonauskas',
            'email' => 'client@example.com',
            'service_id' => null,
        ]);
        $clientUser->assignRole($client);

        $serviceEmployeeUser = User::factory()->create([
            'first_name' => 'Petras',
            'last_name' => 'Petrauskas',
            'email' => 'employee@example.com',
            'service_id' => $service->id,
        ]);
        $serviceEmployeeUser->assignRole($serviceEmployee);

        $serviceAdminUser = User::factory()->create([
            'first_name' => 'Antanas',
            'last_name' => 'Antanaitis',
            'email' => 'admin@example.com',
            'service_id' => $service->id,
        ]);
        $serviceAdminUser->assignRole($serviceAdmin);

        $systemAdminUser = User::factory()->create([
            'first_name' => 'Adminas',
            'last_name' => 'Adminaitis',
            'email' => 'system@example.com',
            'service_id' => null,
        ]);
        $systemAdminUser->assignRole($systemAdmin);

        $clientUser = User::first();

        $clientCar1 = $clientUser->cars()->create([
            'make' => 'Audi',
            'model' => 'A4',
            'vin' => 'WAUAFAFL9AN123456',
            'year_of_manufacture' => 2010,
            'plate_no' => 'ABC123',
            'mileage_type' => Car::MILEAGE_TYPE_KILOMETERS,
            'owner_id' => $clientUser->id,
            'owner_confirmed_at' => now(),
            'registration_document' => 'A123123',
        ]);

        $appointment1 = Appointment::create([
            'car_id' => $clientCar1->id,
            'service_id' => $service->id,
            'confirmed_at' => Carbon::parse('2024-05-01 17:49:53'),
            'completed_at' => Carbon::parse('2024-05-01 17:53:22'),
            'transaction_hash' => '0xed77f8808f6c057705e503a8d27d0fafa47309fbb1753dfe1dff16bf6af0b581',
            'mileage_type' => Car::MILEAGE_TYPE_KILOMETERS,
            'current_mileage' => 1,
        ]);
        $record11 = Record::create([
            'appointment_id' => $appointment1->id,
            'short_description' => 'Padangų keitimas',
            'description' => 'Pakeistos padangos, subalansuoti ratai.',
        ]);
        $record12 = Record::create([
            'appointment_id' => $appointment1->id,
            'short_description' => 'Tepalų keitimas',
            'description' => 'Pakeisti variklio tepalai.',
        ]);

        $appointment2 = Appointment::create([
            'car_id' => $clientCar1->id,
            'service_id' => $service->id,
            'confirmed_at' => Carbon::parse('2024-05-01 18:26:27'),
            'completed_at' => Carbon::parse('2024-05-01 18:27:02'),
            'transaction_hash' => '0x83ac83e8146870374eafed4cd54f383ee15e33ec5b1398e463566e5c6d9883ff',
            'mileage_type' => Car::MILEAGE_TYPE_KILOMETERS,
            'current_mileage' => 20000,
        ]);

        $record21 = Record::create([
            'appointment_id' => $appointment2->id,
            'short_description' => 'Pavarų dėžės remontas',
            'description' => 'Pavarų dėžė papildyta tepalais, atlikta adaptacija.',
        ]);

        $appointment3 = Appointment::create([
            'car_id' => $clientCar1->id,
            'service_id' => $service->id,
            'confirmed_at' => Carbon::parse('2024-05-01 18:34:54'),
            'completed_at' => Carbon::parse('2024-05-01 18:37:08'),
            'transaction_hash' => '0xa6666f355a0d88e12488e176b75cb82a84b9f7a34d67a2eb9d6e0984a331fc32',
            'mileage_type' => Car::MILEAGE_TYPE_KILOMETERS,
            'current_mileage' => 25000,
        ]);

        $record31 = Record::create([
            'appointment_id' => $appointment3->id,
            'short_description' => 'Priekinio stiklo keitimas',
            'description' => 'Pakeistas priekinis stiklas ir sutrūkinėjusios senos gumos aplink stiklą.',
        ]);

        $clientCar2 = $clientUser->cars()->create([
            'make' => 'BMW',
            'model' => 'X5',
            'vin' => '5UXKR0C54F0K12345',
            'owner_id' => $clientUser->id,
            'owner_confirmed_at' => now(),
            'year_of_manufacture' => 2015,
            'plate_no' => 'XYZ123',
            'mileage_type' => Car::MILEAGE_TYPE_KILOMETERS,
            'registration_document' => 'B123123',
        ]);
    }
}
