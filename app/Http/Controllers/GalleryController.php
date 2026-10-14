<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GalleryItem;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    /**
     * Display gallery management dashboard.
     */
    public function index(Request $request)
    {
        $query = GalleryItem::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('caption', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        $galleryItems = $query->orderBy('is_featured', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        $categories = ['Campus Life', 'Laboratories', 'Sports', 'Arts & Culture', 'Events'];

        $stats = [
            'total' => GalleryItem::count(),
            'featured' => GalleryItem::where('is_featured', true)->count(),
            'categories_count' => GalleryItem::distinct('category')->count(),
        ];

        return view('portals.content.gallery.index', compact('galleryItems', 'categories', 'stats'));
    }

    /**
     * Store a new gallery media item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'caption' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'image_path' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
        ]);

        $imagePath = $request->image_path ?? 'scholar/assets/images/banner-item-01.jpg';

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'gallery_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('gallery', $filename, 'public');
            $imagePath = 'storage/' . $path;
        }

        $item = GalleryItem::create([
            'title' => $request->title,
            'category' => $request->category,
            'image_path' => $imagePath,
            'caption' => $request->caption,
            'is_featured' => $request->has('is_featured'),
        ]);

        AuditTrail::log(
            'GALLERY_ITEM_ADDED',
            "Uploaded media item '{$item->title}' under category {$item->category}",
            'gallery_items',
            $item->id
        );

        return redirect()->route('content.gallery.index')->with('success', "Gallery media '{$item->title}' added successfully.");
    }

    /**
     * Update an existing gallery item.
     */
    public function update(Request $request, $id)
    {
        $item = GalleryItem::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'caption' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'image_path' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
        ]);

        $imagePath = $item->image_path;

        if ($request->hasFile('image')) {
            if ($imagePath && str_starts_with($imagePath, 'storage/gallery/')) {
                $oldFile = str_replace('storage/', '', $imagePath);
                Storage::disk('public')->delete($oldFile);
            }

            $file = $request->file('image');
            $filename = 'gallery_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('gallery', $filename, 'public');
            $imagePath = 'storage/' . $path;
        } elseif ($request->filled('image_path')) {
            $imagePath = $request->image_path;
        }

        $item->update([
            'title' => $request->title,
            'category' => $request->category,
            'image_path' => $imagePath,
            'caption' => $request->caption,
            'is_featured' => $request->has('is_featured'),
        ]);

        AuditTrail::log(
            'GALLERY_ITEM_UPDATED',
            "Updated media item details for '{$item->title}'",
            'gallery_items',
            $item->id
        );

        return redirect()->back()->with('success', "Gallery item '{$item->title}' updated successfully.");
    }

    /**
     * Delete a gallery item.
     */
    public function destroy($id)
    {
        $item = GalleryItem::findOrFail($id);

        if ($item->image_path && str_starts_with($item->image_path, 'storage/gallery/')) {
            $oldFile = str_replace('storage/', '', $item->image_path);
            Storage::disk('public')->delete($oldFile);
        }

        $title = $item->title;
        $item->delete();

        AuditTrail::log(
            'GALLERY_ITEM_DELETED',
            "Removed gallery item '{$title}'",
            'gallery_items',
            $id
        );

        return redirect()->back()->with('success', "Gallery item '{$title}' removed successfully.");
    }

    /**
     * Quick toggle featured status.
     */
    public function toggleFeatured($id)
    {
        $item = GalleryItem::findOrFail($id);
        $item->is_featured = !$item->is_featured;
        $item->save();

        $statusStr = $item->is_featured ? 'highlighted on landing page' : 'removed from landing page highlights';
        AuditTrail::log(
            'GALLERY_ITEM_FEATURED_TOGGLED',
            "Gallery item '{$item->title}' status set to: {$statusStr}",
            'gallery_items',
            $item->id
        );

        return redirect()->back()->with('success', "Media '{$item->title}' is now {$statusStr}.");
    }
}
