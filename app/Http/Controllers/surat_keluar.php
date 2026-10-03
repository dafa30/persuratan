<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Surat;

class surat_keluar extends Controller
{
    public function index(Request $request)
    {
        $query = Surat::with(['user:id_users,name', 'penerima:id_users,name'])
            ->where('jenis_surat', 'keluar');
        $years = (clone $query)->selectRaw('YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        if ($request->filled('year')) $query->whereYear('created_at', $request->input('year'));
        if ($request->filled('month')) $query->whereMonth('created_at', $request->input('month'));
        if ($request->filled('day')) $query->whereDay('created_at', $request->input('day'));

        $data = $query->latest()->get();

        return view('surat.surat_keluar', compact('data', 'years'));
    }
}
