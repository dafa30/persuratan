<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Surat;

class surat_keluar extends Controller
{
    public function index()
    {
        $suratKeluar = Surat::where('jenis_surat', 'keluar')->get();
        $data = Surat::all(); // Mengambil semua data
        return view('surat.surat_keluar', ['data' => $suratKeluar]);
    }
}
