<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\LambdaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WriteAppointmentDataToBlockchainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    cupublic function __construct(
        private readonly Appointment   $appointment,
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $lambdaService = new LambdaService();

        $appointment = $this->appointment;

        $car = $appointment->car;
        $records = $appointment->records;

        $hash_string = $car->vin . '_' . $appointment->current_mileage . '_' . $appointment->completed_at . '_' . count($records);

        foreach ($records as $record) {
            $hash_string .= '_' . $record->short_description;
        }

        $hashed = hash('sha256', $hash_string);

        // TODO dispatch this in the background
        $awsResults = $lambdaService->invoke([
            'FunctionName' => 'sendTransactionTest',
            'Payload' => json_encode(['hash' => $hashed]),
        ]);

        $results = json_decode($awsResults->get('Payload')->getContents(), true);

        $appointment->update(['transaction_hash' => $results['body']['txHash']]);
    }
}
