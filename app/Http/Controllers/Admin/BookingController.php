<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        if (Auth::user()->is_admin === 1) {
            $bookings = Booking::with(['user', 'student'])->latest()->get();
        } else {
            $bookings = Booking::with(['user', 'student'])
                ->where('user_id', Auth::user()->id)
                ->latest()
                ->get();
        }

        return view('admin.booking.index', compact('bookings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alternative_id' => 'required|exists:cars,id',
            'phone' => 'required|string|max:20',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'type' => 'required|in:test_drive,reservasi',
        ]);

        $datetime = Carbon::parse("{$request->date} {$request->time}");
        $now = Carbon::now();

        if ($datetime->lessThan($now)) {
            return redirect()->route('calculation.user')->with('error', 'Tanggal dan jam booking tidak boleh kurang dari waktu sekarang')->withInput();
        }

        if ($datetime->hour < 8 || $datetime->hour > 17) {
            return redirect()->route('calculation.user')->with('error', 'Booking hanya diperbolehkan antara jam 08:00 sampai 17:00')->withInput();
        }

        $exists = Booking::where('car_id', $request->alternative_id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($exists) {
            return redirect()->route('calculation.user')->with('error', 'Mobil ini sudah dibooking pada tanggal dan jam tersebut')->withInput();
        }

        // Hitung waktu 1 jam sebelum dan sesudah
        $startRange = $datetime->copy()->subHour()->format('H:i');
        $endRange = $datetime->copy()->addHour()->format('H:i');

        // Cek apakah ada booking dalam rentang waktu tersebut
        $exists = Booking::where('car_id', $request->alternative_id)
            ->where('date', $request->date)
            ->where('status', '!=', 'rejected')
            ->whereIn('time', [$startRange, $request->time, $endRange])
            ->exists();

        if ($exists) {
            return redirect()->route('calculation.user')->with('error', 'Mobil ini sudah dibooking pada waktu tersebut atau dalam waktu 1 jam sebelum/sesudah')->withInput();
        }

        Booking::create([
            'user_id' => Auth::id(),
            'car_id' => $request->alternative_id,
            'phone' => $request->phone,
            'date' => $request->date,
            'time' => $request->time,
            'type' => $request->type,
            'status' => 'pending',
        ]);

        return redirect()->route('calculation.user')->with('success', 'Booking berhasil dikirim');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();

        return redirect()->route('booking.index')->with('success', 'Status booking berhasil diubah');
    }
}
