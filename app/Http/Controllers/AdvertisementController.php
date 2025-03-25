<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advertisement;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
    public function index(){
        $advertisements = Advertisement::all();
        return view('admin.advertisement.index', compact('advertisements'));
    }

    public function edit($id)
    {
        $advertisements = Advertisement::findOrFail($id);
        return response()->json($advertisements);
    }

    public function update(Request $request, $id)
    {
        $advertisement = Advertisement::findOrFail($id);

        $request->validate([
            'adImage' => 'required|array|min:3|max:5',
            'adImage.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'adImage.required' => 'You must upload at least 3 images.',
            'adImage.min' => 'A minimum of 3 images must be uploaded.',
            'adImage.max' => 'You can upload a maximum of 5 images.',
            'adImage.*.image' => 'Each file must be an image.',
            'adImage.*.mimes' => 'Allowed image formats are: jpeg, png, jpg, gif, svg.',
            'adImage.*.max' => 'The maximum image size allowed is 2MB.',
        ]);

        $uploadedImages = [];

        // Handle new images
        if ($request->hasFile('adImage')) {
            foreach ($request->file('adImage') as $image) {
                $imagePath = $image->store('advertisement', 'public');
                $uploadedImages[] = $imagePath;
            }
        }

        // Delete existing images if they were replaced
        if ($advertisement->adImage) {
            $existingImages = explode(', ', $advertisement->adImage);
            foreach ($existingImages as $oldImage) {
                if (!in_array($oldImage, $uploadedImages)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        }

        // Update advertisement with new images
        $advertisement->adImage = implode(', ', $uploadedImages);
        $advertisement->save();

        return response()->json(['message' => 'Advertisement updated successfully']);
    }



}
