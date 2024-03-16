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

        $car = $this->appointment->car;
        $records = $this->appointment->records;

        $hash_string = $car->vin . '_' . count($records);

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

        $this->appointment->update(['transaction_hash' => $results['body']['txHash']]);
    }
}
