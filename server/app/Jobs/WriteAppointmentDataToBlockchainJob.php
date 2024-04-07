<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\LambdaService;
use Aws\Lambda\LambdaClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WriteAppointmentDataToBlockchainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private LambdaClient $client;

    public function __construct(
        private readonly Appointment   $appointment,
    )
    {
        $this->client = new LambdaClient([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function handle(): void
    {
        $appointment = $this->appointment;

        $car = $appointment->car;
        $records = $appointment->records;

        $hash_string = $car->vin . '_' . $appointment->current_mileage . '_' . $appointment->completed_at . '_' . count($records);

        foreach ($records as $record) {
            $hash_string .= '_' . $record->short_description;
        }

        $hashed = hash('sha256', $hash_string);

        $awsResults = $this->client->invoke([
            'FunctionName' => 'sendTransactionTest',
            'Payload' => json_encode(['hash' => $hashed]),
        ]);

        $results = json_decode($awsResults->get('Payload')?->getContents() ?? '{}', true, 512, JSON_THROW_ON_ERROR);

        $appointment->update(['transaction_hash' => $results['body']['txHash'] ?? null]);
    }
}
