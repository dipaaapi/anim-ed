<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\WatchRead;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Show admin dashboard overview.
     */
    public function index()
    {
        $totalViews = Ad::sum('views');
        $totalClicks = Ad::sum('clicks');
        $totalAds = Ad::count();
        $totalActivities = WatchRead::count();

        // Recent activity
        $recentActivities = WatchRead::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Ad performance chart / breakdown
        $ads = Ad::orderBy('views', 'desc')->get();

        return view('admin.dashboard', compact('totalViews', 'totalClicks', 'totalAds', 'totalActivities', 'recentActivities', 'ads'));
    }

    /**
     * Show ads list and manager form.
     */
    public function ads()
    {
        $ads = Ad::orderBy('created_at', 'desc')->get();
        return view('admin.ads', compact('ads'));
    }

    /**
     * Store new advertisement.
     */
    public function storeAd(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'image_url' => 'required|url',
            'target_url' => 'required|url',
            'type' => 'required|in:watch,read,both',
        ]);

        Ad::create($data);

        return redirect()->route('admin.ads')->with('success', 'Advertisement created successfully.');
    }

    /**
     * Toggle advertisement status (active / inactive).
     */
    public function toggleAd(Ad $ad)
    {
        $ad->update([
            'is_active' => !$ad->is_active
        ]);

        return redirect()->route('admin.ads')->with('success', 'Advertisement status updated.');
    }

    /**
     * Delete advertisement.
     */
    public function deleteAd(Ad $ad)
    {
        $ad->delete();
        return redirect()->route('admin.ads')->with('success', 'Advertisement deleted.');
    }
}
