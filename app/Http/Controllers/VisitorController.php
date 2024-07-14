<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitorAdImpression;
use App\Models\Ad;
use Carbon\Carbon;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Log;

class VisitorController extends Controller
{
    private function getVisitor($ip)
    {
        try {
            $location = Location::get($ip);
        } catch (\Exception $e) {
            $location = null;
            Log::error("Failed to fetch location for IP: $ip", ['error' => $e->getMessage()]);
        }

        $hashedIp = hash('sha256', $ip);

        return Visitor::firstOrCreate(
            ['ip_address' => $hashedIp],
            [
                'country' => $location->countryName ?? 'Unknown',
                'region' => $location->regionName ?? 'Unknown',
                'city' => $location->cityName ?? 'Unknown'
            ]
        );
    }

    public function trackClick(Request $request)
    {
        $this->validateRequest($request, ['ad_id' => 'required|integer']);

        $adId = $request->input('ad_id');
        $visitor = $this->getVisitor($request->ip());

        $adClicks = $visitor->ad_clicks ?? [];
        $adClicks[$adId] = ($adClicks[$adId] ?? 0) + 1;

        $visitor->ad_clicks = $adClicks;
        $visitor->updated_at = now();
        $visitor->save();

        return response()->json(['message' => 'Ad click tracked successfully'], 200);
    }

    public function cleanOldEntries()
    {
        $threshold = Carbon::now()->subWeek();
        $deleted = Visitor::where('updated_at', '<', $threshold)->delete();

        Log::info("Old visitor entries cleaned up", ['deleted_count' => $deleted]);

        return response()->json(['message' => 'Old visitors cleared successfully'], 200);
    }

    public function trackImpression(Request $request)
    {
        $this->validateRequest($request, ['ad_id' => 'required|integer']);

        $adId = $request->input('ad_id');
        $visitor = $this->getVisitor($request->ip());

        $adImpression = VisitorAdImpression::firstOrNew(
            ['visitor_id' => $visitor->id, 'ad_id' => $adId]
        );

        if (!$adImpression->exists) {
            $adImpression->impressions = 1;
        } else {
            $adImpression->impressions++;
        }

        $adImpression->save();

        $ad = Ad::find($adId);
        $ad->increment('impressions');

        return response()->json(['message' => 'Ad impression tracked successfully'], 200);
    }

    private function validateRequest(Request $request, array $rules)
    {
        $request->validate($rules);
    }
}
