<?php

namespace App\Services;

use App\Models\Address;

class AddressService
{
    public function save(array $data) {
        $authService = new AuthService();
        $user = $authService->getAuth();
        $result = Address::query()
            ->updateOrCreate(
                [
                    'user_id' => $user->user_id,
                ],
                [
                    'barangay_id' => $data['barangay_id'],
                    'other_details' => $data['other_details'],
                    'contact_number' => $data['contact_number'],
                    'postal_code' => $data['postal_code'],
                ]
            );
        
        return $result;
    }

    public function find(array $data) {
        $authService = new AuthService();
        $user = $authService->getAuth();

        return Address::query()
            ->with(['barangay.municity.province.region.islandGroup'])
            ->where('user_id', $user->user_id)
            ->first();
    }
}
