<?php

namespace App\Http\Controllers\api\v1\aureliya; // FIX: Lowercase namespace

use App\Http\Controllers\Controller;
use App\Models\aureliya\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // FIX: Added Str helper

class ReviewController extends Controller
{
    public function index()
    {
        return Review::all();
    }

    public function show($id)
    {
        return Review::findOrFail($id);
    }

    public function store(Request $request)
    {
        $review = Review::create([
            // FIX: Use Str::uuid() instead of uuid_create()
            '_id'        => (string) Str::uuid(),
            'property_id' => $request->property_id,
            'user_id'    => $request->user_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return $review;
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $review->update($request->only(['rating', 'comment']));
        return $review;
    }

    public function destroy()
    {
        return response()->json(['error' => 'Forbidden'], 403);
    }
}