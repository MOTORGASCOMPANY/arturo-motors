<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\PageMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function uploadMedia(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|image|max:5120',
                'section_id' => 'required|integer|exists:page_sections,id',
            ]);

            $path = $request->file('file')->store('cms', 'public');

            $media = Media::create([
                'name' => $request->file('file')->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => 'image',
                'mime_type' => $request->file('file')->getMimeType(),
                'file_size' => $request->file('file')->getSize(),
            ]);

            PageMedia::create([
                'page_section_id' => $request->section_id,
                'media_id' => $media->id,
                'usage' => 'image',
                'sort_order' => 0,
            ]);

            return response()->json(['success' => true, 'path' => $path]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }
}
