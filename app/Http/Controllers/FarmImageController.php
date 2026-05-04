<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\FarmImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FarmImageController extends Controller
{
    /**
     * Store a newly created image for a farm.
     */
    public function store(Request $request, Farm $farm)
    {
        $this->authorizeOwnership($farm);

        $validated = $request->validate([
            'image' => 'required|image|max:5120|mimes:jpeg,png,jpg,gif,webp',
            'caption' => 'nullable|string|max:255',
        ]);

        $path = $request->file('image')->store('farm-images', 'public');

        $image = $farm->images()->create([
            'image_path' => $path,
            'caption' => $validated['caption'],
        ]);

        // If farm has no main image set, make this the main image
        if (!$farm->main_image_id) {
            $farm->update(['main_image_id' => $image->id]);
        }

        return back()->with('success', 'Farm image uploaded successfully.');
    }

    /**
     * Set this image as the farm's main image.
     */
    public function setMain(Request $request, Farm $farm, FarmImage $image)
    {
        $this->authorizeOwnership($farm);

        if ($image->farm_id !== $farm->id) {
            abort(403, 'Unauthorized');
        }

        $farm->update(['main_image_id' => $image->id]);

        return back()->with('success', 'Main image updated.');
    }

    /**
     * Remove the specified image from storage.
     */
    public function destroy(Request $request, Farm $farm, FarmImage $image)
    {
        $this->authorizeOwnership($farm);

        // Ensure image belongs to this farm
        if ($image->farm_id !== $farm->id) {
            abort(403, 'Unauthorized');
        }

        // If this was the main image, unset it
        if ($farm->main_image_id === $image->id) {
            $farm->update(['main_image_id' => null]);
        }

        // Delete file
        Storage::disk('public')->delete($image->image_path);

        $image->delete();

        return back()->with('success', 'Image deleted successfully.');
    }

    /**
     * Authorize farm ownership.
     */
    private function authorizeOwnership(Farm $farm): void
    {
        if ($farm->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }
}
