<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function markAsRead($id)
    {
        Notifikasi::findOrFail($id)->update(['status_dibaca' => true]);
        return redirect()->back();
    }
}