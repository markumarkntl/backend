<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // ── GET /products ─────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        // FIX: Wrap search in a closure so it doesn't break the is_active filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku',  'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data'    => $products,
        ]);
    }

    // ── GET /products/{id} ───────────────────────────────────────────────────
    public function show($id)
    {
        $product = Product::where('is_active', true)->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $product,
        ]);
    }

    // ── POST /products ────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'price'    => 'required|numeric|min:0',
            'stock'    => 'required|integer|min:0',
            'category' => 'required|string|max:100',
            'sku'      => 'nullable|string|max:100|unique:products,sku',
            'description' => 'nullable|string',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name'        => $request->name,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'category'    => $request->category,
            'sku'         => $request->sku ?? $this->generateSku($request->category),
            'description' => $request->description,
            'is_active'   => true,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan.',
            'data'    => $product,
        ], 201);
    }

    // ── POST /products/{id}?_method=PUT ──────────────────────────────────────
    public function update(Request $request, $id)
    {
        $product = Product::where('is_active', true)->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'price'    => 'sometimes|required|numeric|min:0',
            'stock'    => 'sometimes|required|integer|min:0',
            'category' => 'sometimes|required|string|max:100',
            'sku'      => 'nullable|string|max:100|unique:products,sku,' . $id,
            'description' => 'nullable|string',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name', 'price', 'stock', 'category', 'sku', 'description']);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image) {
                $this->deleteImage($product->image);
            }
            $data['image'] = $this->uploadImage($request->file('image'));
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui.',
            'data'    => $product->fresh(),
        ]);
    }

    // ── DELETE /products/{id} ─────────────────────────────────────────────────
    public function destroy($id)
    {
        $product = Product::where('is_active', true)->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        // Soft delete: set is_active = false, bukan hapus permanen
        $product->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus.',
        ]);
    }

    // ── Helper: Upload Image ──────────────────────────────────────────────────
    private function uploadImage(\Illuminate\Http\UploadedFile $file): string
    {
        $filename = 'products/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('', $filename, 'public');
        return asset('storage/' . $filename);
    }

    // ── Helper: Delete Image ──────────────────────────────────────────────────
    private function deleteImage(string $imageUrl): void
    {
        // Ekstrak path relatif dari URL (hapus base URL + /storage/)
        $relativePath = ltrim(str_replace(url('/storage'), '', $imageUrl), '/');
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    // ── Helper: Auto-generate SKU ─────────────────────────────────────────────
    private function generateSku(string $category): string
    {
        $prefix = match (strtolower($category)) {
            'makanan' => 'MKN',
            'minuman' => 'MNM',
            'snack'   => 'SNK',
            default   => strtoupper(substr($category, 0, 3)),
        };

        $count = Product::where('category', $category)->withoutGlobalScopes()->count() + 1;
        return $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}