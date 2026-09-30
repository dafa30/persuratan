<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Surat;

class surat_masuk extends Controller
{
    public function index()
    {
        $suratMasuk = Surat::where('jenis_surat', 'masuk')->get();
        $data = Surat::all(); // Mengambil semua data
        return view('surat.surat_masuk', ['data' => $suratMasuk]);
    }
}
