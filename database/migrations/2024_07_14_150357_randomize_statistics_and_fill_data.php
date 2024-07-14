<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RandomizeStatisticsAndFillData extends Migration
{
    public function up()
    {
        $this->randomizeTimes();
        
        $this->populateClicksAndViews();
        
        $this->updateAdStatistics();
    }

    private function randomizeTimes()
    {
        $ads = DB::table('ads')->get();
        foreach ($ads as $ad) {
            $randomDate = Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            DB::table('ads')->where('id', $ad->id)->update(['created_at' => $randomDate, 'updated_at' => $randomDate]);
        }

        $impressions = DB::table('visitor_ad_impressions')->get();
        foreach ($impressions as $impression) {
            $randomDate = Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            DB::table('visitor_ad_impressions')->where('id', $impression->id)->update(['created_at' => $randomDate, 'updated_at' => $randomDate]);
        }
    }

    private function populateClicksAndViews()
    {
        $ads = DB::table('ads')->pluck('id')->toArray();
        $visitors = DB::table('visitors')->get();

        foreach ($visitors as $visitor) {
            $adClicks = $visitor->ad_clicks ? json_decode($visitor->ad_clicks, true) : [];
            $adImpressions = DB::table('visitor_ad_impressions')->where('visitor_id', $visitor->id)->pluck('ad_id')->toArray();

            foreach ($ads as $adId) {
                $clicks = rand(0, 5);
                $impressions = rand(1, 10);

                $adClicks[$adId] = ($adClicks[$adId] ?? 0) + $clicks;

                if (!in_array($adId, $adImpressions)) {
                    DB::table('visitor_ad_impressions')->insert([
                        'visitor_id' => $visitor->id,
                        'ad_id' => $adId,
                        'impressions' => $impressions,
                        'created_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }

            DB::table('visitors')->where('id', $visitor->id)->update([
                'ad_clicks' => json_encode($adClicks),
                'updated_at' => Carbon::now()
            ]);
        }
    }

    private function updateAdStatistics()
    {
        $ads = DB::table('ads')->get();

        foreach ($ads as $ad) {
            $totalClicks = 0;
            $totalImpressions = DB::table('visitor_ad_impressions')->where('ad_id', $ad->id)->sum('impressions');

            $visitors = DB::table('visitors')->get();
            foreach ($visitors as $visitor) {
                $adClicks = $visitor->ad_clicks ? json_decode($visitor->ad_clicks, true) : [];
                if (isset($adClicks[$ad->id])) {
                    $totalClicks += $adClicks[$ad->id];
                }
            }

            DB::table('ads')->where('id', $ad->id)->update([
                'clicks' => $totalClicks,
                'impressions' => $totalImpressions
            ]);
        }
    }

    public function down()
    {
        // This migration is irreversible
    }
}
