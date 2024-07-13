<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\StoreAdRequest;
use App\Http\Requests\UpdateAdRequest;

class AdController extends Controller
{
    private function storeImage($image)
    {
        $filename = Str::random(10) . '_' . time() . '.' . $image->getClientOriginalExtension();
        return $image->storeAs('ads', $filename, 'public');
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

        return response()->json(['data' => $stats], 200);
    }

    public function store(StoreAdRequest $request)
    {
        $imagePath = $request->hasFile('image') ? $this->storeImage($request->file('image')) : null;

        $ad = Ad::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath ? Storage::url($imagePath) : null,
        ]);

        return response()->json(['message' => 'Ad created successfully', 'data' => $ad], 201);
    }

    public function update(UpdateAdRequest $request, $id)
    {
        $ad = Ad::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($ad->image) {
                $oldImagePath = str_replace('/storage/', '', $ad->image);
                Storage::disk('public')->delete($oldImagePath);
            }
            $imagePath = $this->storeImage($request->file('image'));
            $ad->image = Storage::url($imagePath);
        }

        $ad->update($request->except('image'));

        return response()->json(['message' => 'Ad updated successfully', 'data' => $ad], 200);
    }

    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);

        if ($ad->image) {
            $imagePath = str_replace('/storage/', '', $ad->image);
            Storage::disk('public')->delete($imagePath);
        }

        $ad->delete();

        return response()->json(['message' => 'Ad deleted successfully'], 200);
    }
}
