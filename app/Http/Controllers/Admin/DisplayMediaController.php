<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DisplayMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DisplayMediaController extends Controller
{
    private string $slidesDir = 'display-slides';
    private string $videoDir  = 'display-video';

    public function index()
    {
        $slides   = $this->getSlides();
        $videoUrl = $this->getVideoUrl();
        $videoRecord = DisplayMedia::where('type', 'video')->latest()->first();

        return view('admin.display-media', compact('slides', 'videoUrl', 'videoRecord'));
    }

    public function uploadSlide(Request $request)
    {
        $request->validate([
            'slide' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $file     = $request->file('slide');
        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $filename = 'slide_' . time() . '_' . $safeName;
        $path     = $this->slidesDir . '/' . $filename;

        $file->storeAs($this->slidesDir, $filename, 'public');

        // Determine next sort order
        $maxOrder = DisplayMedia::where('type', 'slide')->max('sort_order') ?? 0;

        DisplayMedia::create([
            'type'          => 'slide',
            'original_name' => $file->getClientOriginalName(),
            'stored_path'   => $path,
            'sort_order'    => $maxOrder + 1,
        ]);

        return back()->with('success', 'Slide uploaded successfully.');
    }

    public function deleteSlide(Request $request, int $id)
    {
        $record = DisplayMedia::where('type', 'slide')->findOrFail($id);

        if (Storage::disk('public')->exists($record->stored_path)) {
            Storage::disk('public')->delete($record->stored_path);
        }

        $record->delete();

        return back()->with('success', 'Slide deleted.');
    }

    public function uploadVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,webm,ogg|max:204800',
        ]);

        // Delete old video file(s) and DB records
        foreach (DisplayMedia::where('type', 'video')->get() as $old) {
            if (Storage::disk('public')->exists($old->stored_path)) {
                Storage::disk('public')->delete($old->stored_path);
            }
            $old->delete();
        }

        $file      = $request->file('video');
        $ext       = $file->getClientOriginalExtension();
        $filename  = 'advertisement_' . time() . '.' . $ext;
        $path      = $this->videoDir . '/' . $filename;

        $file->storeAs($this->videoDir, $filename, 'public');

        DisplayMedia::create([
            'type'          => 'video',
            'original_name' => $file->getClientOriginalName(),
            'stored_path'   => $path,
            'sort_order'    => 0,
        ]);

        return back()->with('success', 'Video replaced successfully.');
    }

    // ─── Helpers (called from WindowController) ───────────────────────────────

    public function getSlides(): array
    {
        $records = DisplayMedia::where('type', 'slide')->orderBy('sort_order')->get();

        if ($records->isNotEmpty()) {
            return $records->map(fn ($r) => [
                'id'       => $r->id,
                'url'      => $r->url,
                'filename' => $r->original_name,
                'custom'   => true,
            ])->all();
        }

        // Fallback to bundle defaults
        return [
            ['id' => null, 'url' => asset('images/NU-adv1.jpg'), 'filename' => 'NU-adv1.jpg', 'custom' => false],
            ['id' => null, 'url' => asset('images/NU-adv2.jpg'), 'filename' => 'NU-adv2.jpg', 'custom' => false],
        ];
    }

    public function getVideoUrl(): string
    {
        $record = DisplayMedia::where('type', 'video')->latest()->first();

        if ($record) {
            return $record->url;
        }

        return asset('videos/nu-advertisment.mp4');
    }
}
