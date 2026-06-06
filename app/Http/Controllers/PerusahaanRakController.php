<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PerusahaanRakController extends Controller
{
    private $filePath = 'public/perusahaan-rak.json';

    public function index()
    {
        $data = json_decode(File::get(base_path($this->filePath)), true);
        return view('perusahaan-rak.index', compact('data'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'data' => 'required|string',
        ]);

        $data = json_decode($request->data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['data' => 'Invalid JSON format.']);
        }

        File::put(base_path($this->filePath), json_encode($data, JSON_PRETTY_PRINT));
        return redirect()->route('perusahaan-rak.index')->with('success', 'Data updated successfully.');
    }
}
