<?php

namespace App\Jobs;

use App\Mail\CarReminderMail;
use App\Mail\ExchangeRatesMail;
use App\Models\Reminder;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class SendForeignExchangeRatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cache_key = now()->format('Y-m-d');
        $response = Cache::remember($cache_key, now()->addDay(), function () {
            return Http::get('https://open.er-api.com/v6/latest/EUR');
        });

        if ($response->status() !== 200) {
            Cache::forget($cache_key);
            throw new Exception();
        }


        $response = $response->json();


        Mail::to('and.cereska@gmail.com')->send(new ExchangeRatesMail($response));
    }
}
