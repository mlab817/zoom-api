<?php

namespace App\Traits;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait ZoomJWT
{
    private function generateToken(): string
    {
        $key = config('zoom.clientKey');
        $secret = config('zoom.clientSecret');

        $payload = [
            'iss'   => $key,
            'exp'   => strtotime('+1 minute'),
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    private function retrieveZoomUrl()
    {
        return config('zoom.baseUrl');
    }

    private function zoomRequest()
    {
        $jwt = $this->generateToken();

        return Http::withHeaders([
            'authorization' => 'Bearer ' . $jwt,
            'content-type'  => 'application/json',
        ]);
    }

    public function zoomGet(string $path, array $query = [])
    {
        $url = $this->retrieveZoomUrl();

        $request = $this->zoomRequest();

        return $request->get($url . $path, $query);
    }

    public function zoomPost(string $path, array $query = [])
    {
        $url = $this->retrieveZoomUrl();

        $request = $this->zoomRequest();

        return $request->post($url . $path, $query);
    }

    public function zoomPatch(string $path, array $query = [])
    {
        $url = $this->retrieveZoomUrl();

        $request = $this->zoomRequest();

        return $request->patch($url . $path, $query);
    }

    public function zoomDelete(string $path, array $query = [])
    {
        $url = $this->retrieveZoomUrl();

        $request = $this->zoomRequest();

        return $request->delete($url . $path, $query);
    }

    public function toZoomTimeFormat(string $dateTime): string
    {
        try {
            $date = new \DateTime($dateTime);
            return $date->format('Y-m-d\TH:i:s');
        } catch (\Exception $e) {
            Log::error('ZoomJWT->toZoomTimeFormat : ' . $e->getMessage());
            return '';
        }
    }

    public function toUnixTimeStamp(string $dateTime, string $timezone)
    {
        try {
            $date = new \DateTime($dateTime, new \DateTimeZone($timezone));
            return $date->getTimestamp();
        } catch (\Exception $e) {
            Log::error('ZoomJWT->toUnixTimeStamp : ' . $e->getMessage());
            return '';
        }
    }
}
