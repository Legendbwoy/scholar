<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Display listing of institutional events.
     */
    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            } elseif ($request->status === 'featured') {
                $query->where('is_featured', true);
            }
        }

        $events = $query->orderBy('event_date', 'asc')->paginate(10)->withQueryString();

        $categories = ['Academic', 'Sports', 'Arts & Culture', 'Symposium', 'Ceremony', 'Workshop'];

        $stats = [
            'total' => Event::count(),
            'upcoming' => Event::where('event_date', '>=', now()->startOfDay())->count(),
            'published' => Event::where('is_published', true)->count(),
            'featured' => Event::where('is_featured', true)->count(),
        ];

        return view('portals.content.events.index', compact('events', 'categories', 'stats'));
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:event_date',
            'venue' => 'required|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'registration_link' => 'nullable|url|max:255',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'banner_image' => 'nullable|string|max:255',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $bannerPath = $request->banner_image ?? 'scholar/assets/images/event-01.jpg';

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $filename = 'event_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('events', $filename, 'public');
            $bannerPath = 'storage/' . $path;
        }

        $event = Event::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'event_date' => $validated['event_date'],
            'end_date' => $validated['end_date'] ?? null,
            'venue' => $validated['venue'],
            'organizer' => $validated['organizer'] ?? 'Office of Academic Affairs',
            'registration_link' => $validated['registration_link'] ?? null,
            'banner_image' => $bannerPath,
            'is_published' => $request->has('is_published'),
            'is_featured' => $request->has('is_featured'),
        ]);

        AuditTrail::log(
            'EVENT_CREATED',
            "Created institutional event '{$event->title}' scheduled for {$event->event_date->format('M d, Y')} at {$event->venue}",
            'events',
            $event->id
        );

        return redirect()->route('content.events.index')->with('success', "Event '{$event->title}' created successfully.");
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:event_date',
            'venue' => 'required|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'registration_link' => 'nullable|url|max:255',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'banner_image' => 'nullable|string|max:255',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $bannerPath = $event->banner_image;

        if ($request->hasFile('banner')) {
            // Delete old uploaded image if it resides in storage/events
            if ($bannerPath && str_starts_with($bannerPath, 'storage/events/')) {
                $oldFile = str_replace('storage/', '', $bannerPath);
                Storage::disk('public')->delete($oldFile);
            }

            $file = $request->file('banner');
            $filename = 'event_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('events', $filename, 'public');
            $bannerPath = 'storage/' . $path;
        } elseif ($request->filled('banner_image')) {
            $bannerPath = $request->banner_image;
        }

        $event->update([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'event_date' => $validated['event_date'],
            'end_date' => $validated['end_date'] ?? null,
            'venue' => $validated['venue'],
            'organizer' => $validated['organizer'] ?? $event->organizer,
            'registration_link' => $validated['registration_link'] ?? null,
            'banner_image' => $bannerPath,
            'is_published' => $request->has('is_published'),
            'is_featured' => $request->has('is_featured'),
        ]);

        AuditTrail::log(
            'EVENT_UPDATED',
            "Modified event parameters for '{$event->title}' (Category: {$event->category}, Date: {$event->event_date->format('M d, Y')})",
            'events',
            $event->id
        );

        return redirect()->back()->with('success', "Event '{$event->title}' updated successfully.");
    }

    /**
     * Delete the specified event.
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        if ($event->banner_image && str_starts_with($event->banner_image, 'storage/events/')) {
            $oldFile = str_replace('storage/', '', $event->banner_image);
            Storage::disk('public')->delete($oldFile);
        }

        $title = $event->title;
        $event->delete();

        AuditTrail::log(
            'EVENT_DELETED',
            "Removed event '{$title}' from institutional directory",
            'events',
            $id
        );

        return redirect()->back()->with('success', "Event '{$title}' removed successfully.");
    }

    /**
     * Quick toggle published status.
     */
    public function togglePublish($id)
    {
        $event = Event::findOrFail($id);
        $event->is_published = !$event->is_published;
        $event->save();

        $statusStr = $event->is_published ? 'published and visible on landing page' : 'unpublished and hidden';
        AuditTrail::log(
            'EVENT_VISIBILITY_TOGGLED',
            "Event '{$event->title}' status set to: {$statusStr}",
            'events',
            $event->id
        );

        return redirect()->back()->with('success', "Event '{$event->title}' is now {$statusStr}.");
    }
}
