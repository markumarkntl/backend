<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    // GET /settings
    public function index()
    {
        $settings = DB::table('settings')->get()->keyBy('key');
        return response()->json(['success' => true, 'data' => $settings]);
    }

    // POST /settings (bulk update)
    public function update(Request $request)
    {
        $request->validate([
            'settings'       => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
        ]);

        foreach ($request->settings as $item) {
            DB::table('settings')->updateOrInsert(
                ['key' => $item['key']],
                ['value' => $item['value'], 'updated_at' => now()]
            );
        }

        return response()->json(['success' => true, 'message' => 'Pengaturan berhasil disimpan.']);
    }
}