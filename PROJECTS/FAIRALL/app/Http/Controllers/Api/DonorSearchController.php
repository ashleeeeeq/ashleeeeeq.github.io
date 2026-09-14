<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use Illuminate\Http\Request;

class DonorSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->query('q', '');

        $results = Donor::query()
            ->with('user')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('organization_name', 'like', "%{$q}%")
                    ->orWhere('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('email', 'like', "%{$q}%");
                    });
            })
            ->limit(20)
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'display_name' => $d->display_name,
                    'email' => $d->user?->email,
                ];
            });

        return response()->json($results);
    }
}
