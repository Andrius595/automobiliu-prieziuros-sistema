<?php

namespace Tests\Unit;

use App\Actions\Record\CreateNewRecord;
use App\Actions\Record\ListRecords;
use App\Models\Car;
use App\Models\Record;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\TestsHelper;

class RecordTest extends TestCase
{
    use TestsHelper;

    //CreateNewRecord
    public function test_if_record_is_created(): void
    {
        $car = Car::factory()->create();
        $appointment = $this->createConfirmedAppointmentForCarId($car->id);

        $newRecord = new CreateNewRecord();
        $record = $newRecord->create([
            'appointment_id' => $appointment->id,
            'short_description' => 'Title',
            'description' => 'Description',
        ]);

        $this->assertNotNull($record);
    }

    public function test_if_appointment_records_are_listed_list(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create([
            'owner_id' => $user->id,
        ]);
        $appointment1 = $this->createConfirmedAppointmentForCarId($car->id);
        $appointment2 = $this->createConfirmedAppointmentForCarId($car->id);

        Record::factory()->create([
            'appointment_id' => $appointment1->id,
            'short_description' => 'Title',
            'description' => 'Description',
        ]);
        Record::factory()->create([
            'appointment_id' => $appointment2->id,
            'short_description' => 'Title',
            'description' => 'Description',
        ]);

        $listRecords = new ListRecords();

        $records = $listRecords->list(['appointment_id' => $appointment1->id]);

        $this->assertEquals(1, $records->total());
    }
}
