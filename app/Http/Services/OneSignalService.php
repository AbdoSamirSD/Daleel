<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;


class OneSignalService
{
    protected string $apiUrl = 'https://api.onesignal.com/notifications';

    public function sendToAllDevices(string $title, string $message)
    {
        $payload = [
            'app_id' => config('services.onesignal.app_id'),
            'included_segments' => ['All'],
            'headings' => ['ar' => $title],
            'contents' => ['ar' => $message],
            'big_picture' => asset( $message),
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . config('services.onesignal.api_key'),
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, $payload);

        return $response;
    }
}