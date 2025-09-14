<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->validate([
            'search'   => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $myUuid = Auth::user()->uuid;

        $query = User::where('uuid', '!=', $myUuid)
                     ->whereNotNull('email_verified_at');

        if (!empty($data['search'])) {
            $q = $data['search'];
            $query->where(function ($sub) use ($q) {
                $sub->where('username', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $perPage = $data['per_page'] ?? 10;
        $users = $query->orderBy('fname')->paginate($perPage);

        return response()->json([
            'message' => 'users paginated (verified only)',
            'data' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
