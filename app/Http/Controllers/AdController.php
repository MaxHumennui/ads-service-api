<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\VisitorAdImpression;
use App\Models\Visitor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\StoreAdRequest;
use App\Http\Requests\UpdateAdRequest;
use Illuminate\Support\Facades\Config;

class AdController extends Controller
{
    private function storeImage($image)
    {
        $filename = Str::random(10) . '_' . time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('ads', $filename, 'public');
        
        $hostUrl = Config::get('app.url');

        return $hostUrl . Storage::url($path);
    }

    public function index()
    {
        $ads = Ad::all();
        return response()->json(['data' => $ads], 200);
    }

    public function statistics()
    {
        $stats = Ad::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(impressions) as total_impressions'),
            DB::raw('SUM(clicks) as total_clicks')
        )
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

        $visitorImpressions = VisitorAdImpression::join('visitors', 'visitor_ad_impressions.visitor_id', '=', 'visitors.id')
            ->select(
                'visitors.country',
                'visitors.region',
                'visitors.city',
                DB::raw('SUM(visitor_ad_impressions.impressions) as total_impressions')
            )
            ->groupBy('visitors.country', 'visitors.region', 'visitors.city')
            ->get();

        $visitors = Visitor::all();

        $countryImpressions = $visitorImpressions->groupBy('country')->map(function ($group) use ($visitors) {
            $country = $group->first()->country;
            $total_clicks = $visitors->where('country', $country)->sum(function ($visitor) {
                $adClicks = $visitor->ad_clicks;
                if (is_string($adClicks)) {
                    $adClicks = json_decode($adClicks, true);
                }
                return is_array($adClicks) ? array_sum($adClicks) : 0;
            });
            return [
                'country' => $country,
                'total_impressions' => $group->sum('total_impressions'),
                'total_clicks' => $total_clicks
            ];
        })->values();

        $regionImpressions = $visitorImpressions->groupBy('region')->map(function ($group) use ($visitors) {
            $region = $group->first()->region;
            $total_clicks = $visitors->where('region', $region)->sum(function ($visitor) {
                $adClicks = $visitor->ad_clicks;
                if (is_string($adClicks)) {
                    $adClicks = json_decode($adClicks, true);
                }
                return is_array($adClicks) ? array_sum($adClicks) : 0;
            });
            return [
                'region' => $region,
                'total_impressions' => $group->sum('total_impressions'),
                'total_clicks' => $total_clicks
            ];
        })->values();

        $cityImpressions = $visitorImpressions->groupBy('city')->map(function ($group) use ($visitors) {
            $city = $group->first()->city;
            $total_clicks = $visitors->where('city', $city)->sum(function ($visitor) {
                $adClicks = $visitor->ad_clicks;
                if (is_string($adClicks)) {
                    $adClicks = json_decode($adClicks, true);
                }
                return is_array($adClicks) ? array_sum($adClicks) : 0;
            });
            return [
                'city' => $city,
                'total_impressions' => $group->sum('total_impressions'),
                'total_clicks' => $total_clicks
            ];
        })->values();

        return response()->json([
            'data' => [
                'ads_stats' => $stats,
                'country_impressions' => $countryImpressions,
                'region_impressions' => $regionImpressions,
                'city_impressions' => $cityImpressions,
            ]
        ], 200);
    }
    
    public function store(StoreAdRequest $request)
    {
        $imagePath = $request->hasFile('image') ? $this->storeImage($request->file('image')) : null;

        $ad = Ad::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath ?? null,
        ]);

        return response()->json(['message' => 'Ad created successfully', 'data' => $ad], 201);
    }

    public function update(UpdateAdRequest $request, $id)
    {
        $ad = Ad::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($ad->image) {
                $oldImagePath = str_replace(Config::get('app.url') . '/storage/', '', $ad->image);
                Storage::disk('public')->delete($oldImagePath);
            }
            $imagePath = $this->storeImage($request->file('image'));
            $ad->image = $imagePath;
        }

        $ad->update($request->except('image'));

        return response()->json(['message' => 'Ad updated successfully', 'data' => $ad], 200);
    }

    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);

        if ($ad->image) {
            $oldImagePath = str_replace(Config::get('app.url') . '/storage/', '', $ad->image);
            Storage::disk('public')->delete($oldImagePath);
        }

        $ad->delete();

        return response()->json(['message' => 'Ad deleted successfully'], 200);
    }
}
