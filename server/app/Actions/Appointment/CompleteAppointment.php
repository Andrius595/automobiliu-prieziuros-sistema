<?php

namespace App\Actions\Appointment;

use App\Jobs\WriteAppointmentDataToBlockchainJob;
use App\Models\Appointment;

class CompleteAppointment
{

    public function __construct(private readonly UpdateAppointment $updateAppointment)
    {
    }

    public function complete(Appointment $appointment): void
    {
        $this->updateAppointment->update($appointment, ['completed_at' => now()]);

        WriteAppointmentDataToBlockchainJob::dispatch($appointment);
    }
}
