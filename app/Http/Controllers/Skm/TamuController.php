<?php

namespace App\Http\Controllers\Skm;

use App\Models\Tamu;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class TamuController extends Controller
{
    /**
     * Store a new guest record.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
        ]);

        // Create a new guest record in the database
        $tamu = Tamu::create($validatedData);

        // Return a response indicating success
        return ResponseFormatter::success(
            $tamu,
            'Data tamu berhasil disimpan'
        );
    }
}
