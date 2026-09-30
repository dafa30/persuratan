<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman muka.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Logika tambahan bisa ditambahkan di sini jika diperlukan
        // Misalnya, mengambil data tertentu dari database untuk ditampilkan di halaman utama.

        return view('UI_Frontend.halaman_muka'); // pastikan file view 'home.blade.php' tersedia di resources/views
    }
}