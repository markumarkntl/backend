<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| Channel 'pos' bersifat publik — semua user yang sudah login boleh
| mendengarkan event. Jika kelak butuh private/presence channel,
| daftarkan di sini dengan closure yang return true/false.
*/

// Channel publik tidak memerlukan otorisasi, tapi kita daftarkan
// sebagai referensi. Private channel contoh (untuk notif per-kasir):
//
// Broadcast::channel('pos.user.{userId}', function ($user, $userId) {
//     return (int) $user->id === (int) $userId;
// });