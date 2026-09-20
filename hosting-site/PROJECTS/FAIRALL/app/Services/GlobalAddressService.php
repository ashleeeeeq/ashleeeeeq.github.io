<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class GlobalAddressService
{
    public function normalize(array $validated): array
    {
        return [
            'address_line' => trim((string) ($validated['address_line'] ?? '')),
            'city' => trim((string) ($validated['city'] ?? '')),
            'province' => trim((string) ($validated['province'] ?? '')),
            'zip' => trim((string) ($validated['zip'] ?? '')),
            'country' => trim((string) ($validated['country'] ?? '')),
        ];
    }

    public function validateLocation(array $validated): array
    {
        $location = $this->normalize($validated);

        $country = $this->resolveCountry($location['country']);
        $province = $this->resolveProvince($location['province'], $country);
        $city = $this->resolveCity($location['city'], $province, $country);

        return [
            'address_line' => $location['address_line'],
            'city' => $city,
            'province' => $province,
            'zip' => $location['zip'],
            'country' => $country,
        ];
    }

    public function buildAddress(array $address): string
    {
        $parts = array_filter([
            trim((string) ($address['address_line'] ?? '')),
            trim((string) ($address['city'] ?? '')),
            trim((string) ($address['province'] ?? '')),
            trim((string) ($address['zip'] ?? '')),
            trim((string) ($address['country'] ?? '')),
        ]);

        return implode(', ', $parts);
    }

    public function parseAddress(?string $address): array
    {
        $defaults = [
            'address_line' => '',
            'city' => '',
            'province' => '',
            'zip' => '',
            'country' => '',
        ];

        if (blank($address)) {
            return $defaults;
        }

        $parts = array_map('trim', explode(',', $address));

        if (count($parts) < 4) {
            $defaults['address_line'] = trim($address);
            return $defaults;
        }

        if (count($parts) === 4) {
            $defaults['address_line'] = $parts[0] ?? '';
            $defaults['city'] = $parts[1] ?? '';
            $defaults['province'] = $parts[2] ?? '';
            $defaults['country'] = $parts[3] ?? '';
            return $defaults;
        }

        $defaults['address_line'] = implode(', ', array_slice($parts, 0, -4));
        $defaults['city'] = $parts[count($parts) - 4] ?? '';
        $defaults['province'] = $parts[count($parts) - 3] ?? '';
        $defaults['zip'] = $parts[count($parts) - 2] ?? '';
        $defaults['country'] = $parts[count($parts) - 1] ?? '';

        return $defaults;
    }

    private function queryNominatim(string $query, string $type): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => config('app.name', 'FAIRALL') . '/1.0',
                'Accept-Language' => 'en',
            ])
                ->timeout(10)
                ->get('https://nominatim.openstreetmap.org/search', [
                    'format' => 'jsonv2',
                    'addressdetails' => 1,
                    'limit' => 1,
                    'q' => $query,
                ])
                ->throw()
                ->json();

            if (empty($response)) {
                return null;
            }

            $address = $response[0]['address'] ?? [];

            // Check type matches the requested level
            if ($type === 'country' && !empty($address['country'])) {
                return ['value' => $address['country']];
            }

            if ($type === 'province') {
                // Check for province/state variations
                $provinceKeys = ['state', 'province', 'region', 'county'];
                foreach ($provinceKeys as $key) {
                    if (!empty($address[$key])) {
                        return ['value' => $address[$key]];
                    }
                }
            }

            if ($type === 'city') {
                $cityKeys = ['city', 'town', 'village', 'municipality', 'hamlet', 'suburb'];
                foreach ($cityKeys as $key) {
                    if (!empty($address[$key])) {
                        return ['value' => $address[$key]];
                    }
                }
            }

            return null;
        } catch (Throwable) {
            return null;
        }
    }

    public function resolveCountry(string $country): string
    {
        $country = trim($country);

        if ($country === '') {
            throw ValidationException::withMessages([
                'country' => 'Country is required.',
            ]);
        }

        $result = $this->queryNominatim($country, 'country');

        if ($result === null) {
            throw ValidationException::withMessages([
                'country' => 'Country may be incorrect or not found.',
            ]);
        }

        return $result['value'];
    }

    public function resolveProvince(string $province, string $country = ''): string
    {
        $province = trim($province);

        if ($province === '') {
            throw ValidationException::withMessages([
                'province' => 'State or province is required.',
            ]);
        }

        // Try with country context first
        $query = trim(implode(', ', array_filter([$province, $country])));
        $result = $this->queryNominatim($query, 'province');

        // If that fails, try province alone
        if ($result === null && $country !== '') {
            $result = $this->queryNominatim($province, 'province');
        }

        if ($result === null) {
            throw ValidationException::withMessages([
                'province' => 'State or province may be incorrect or not found.',
            ]);
        }

        return $result['value'];
    }

    public function resolveCity(string $city, string $province = '', string $country = ''): string
    {
        $city = trim($city);

        if ($city === '') {
            throw ValidationException::withMessages([
                'city' => 'City is required.',
            ]);
        }

        // Try with full context first
        $query = trim(implode(', ', array_filter([$city, $province, $country])));
        $result = $this->queryNominatim($query, 'city');

        // If that fails, try with just province and country (omit the resolved province name)
        if ($result === null && $country !== '') {
            $query = trim(implode(', ', array_filter([$city, $country])));
            $result = $this->queryNominatim($query, 'city');
        }

        // If that fails, try city alone
        if ($result === null) {
            $result = $this->queryNominatim($city, 'city');
        }

        if ($result === null) {
            throw ValidationException::withMessages([
                'city' => 'The selected city may be incorrect or not found.',
            ]);
        }

        return $result['value'];
    }
}