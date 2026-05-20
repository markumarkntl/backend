<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // ── POST /transactions ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'paid_amount'        => 'required|numeric|min:0',
            'payment_method'     => 'required|in:cash,qris',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $itemsData   = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$product->name} tidak mencukupi.",
                    ], 422);
                }

                $subtotal     = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                    'subtotal'   => $subtotal,
                ];

                $product->decrement('stock', $item['quantity']);
            }

            if ($request->paid_amount < $totalAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah bayar kurang dari total belanja.',
                ], 422);
            }

            $transaction = Transaction::create([
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'user_id'        => $request->user()->id,
                'total_amount'   => $totalAmount,
                'paid_amount'    => $request->paid_amount,
                'change_amount'  => $request->paid_amount - $totalAmount,
                'payment_method' => $request->payment_method,
                'status'         => 'success',
            ]);

            foreach ($itemsData as $item) {
                $item['transaction_id'] = $transaction->id;
                TransactionItem::create($item);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil.',
                'data'    => [
                    'invoice_number' => $transaction->invoice_number,
                    'total_amount'   => $transaction->total_amount,
                    'paid_amount'    => $transaction->paid_amount,
                    'change_amount'  => $transaction->change_amount,
                    'payment_method' => $transaction->payment_method,
                    'cashier'        => $request->user()->name,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── GET /transactions/history ─────────────────────────────────────────────
    // Admin  → semua transaksi (tanpa batas)
    // Kasir  → hanya milik sendiri, maksimal 100 terakhir
    public function history(Request $request)
    {
        $user  = $request->user();
        $query = Transaction::with('items.product')
                            ->orderBy('created_at', 'desc');

        if ($user->role === 'admin') {
            // Admin: semua transaksi, sertakan info kasir
            $query->with('user:id,name,nip');
        } else {
            // Kasir: hanya transaksi milik sendiri
            $query->where('user_id', $user->id)->take(100);
        }

        $transactions = $query->get();

        return response()->json([
            'success' => true,
            'data'    => $transactions,
        ]);
    }
}