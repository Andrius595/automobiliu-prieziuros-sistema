<?php

namespace App\Actions\Appointment;

use App\Jobs\WriteAppointmentDataToBlockchainJob;
use App\Models\Appointment;
use App\Services\LambdaService;

class CompleteAppointment
{

    public function __construct()
    {
    }

    public function complete(Appointment $appointment): void
    {
        $appointment->update(['completed_at' => now()]);

        WriteAppointmentDataToBlockchainJob::dispatch($appointment);
    }
}
