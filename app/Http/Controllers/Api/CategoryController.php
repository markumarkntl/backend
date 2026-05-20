<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // ── GET /categories ───────────────────────────────────────────────────────
    // Ambil daftar kategori unik dari tabel products yang aktif.
    // Format response: [{ id, name }] — id adalah index urutan alfabet.
    public function index()
    {
        $categories = Product::where('is_active', true)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->values()
            ->map(fn ($name, $idx) => [
                'id'   => $idx + 1,
                'name' => $name,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }
}