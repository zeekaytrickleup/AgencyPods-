<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithPods;
use App\Models\Attachment;
use App\Models\GoalSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    use InteractsWithPods;

    private const DISK = 'local';

    public function store(Request $request, GoalSection $section)
    {
        $this->authorizeSection($section);

        $request->validate([
            'file' => ['required', 'file', 'max:20480'], // 20 MB
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments', self::DISK);

        $section->attachments()->create([
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'file_type' => $this->detectType($file->getClientOriginalExtension()),
            'uploaded_by' => $request->user()->id,
        ]);

        return $this->backToGoal($section, 'File uploaded.');
    }

    public function download(Attachment $attachment)
    {
        $this->authorizeSection($attachment->section);
        $this->assertFileExists($attachment);

        return Storage::disk(self::DISK)->download($attachment->stored_path, $attachment->original_name);
    }

    public function preview(Attachment $attachment)
    {
        $this->authorizeSection($attachment->section);
        $this->assertFileExists($attachment);

        return response()->file(
            Storage::disk(self::DISK)->path($attachment->stored_path),
            ['Content-Disposition' => 'inline; filename="'.addslashes($attachment->original_name).'"']
        );
    }

    public function destroy(Attachment $attachment)
    {
        $this->authorizeSection($attachment->section);

        $section = $attachment->section;

        if ($attachment->stored_path && Storage::disk(self::DISK)->exists($attachment->stored_path)) {
            Storage::disk(self::DISK)->delete($attachment->stored_path);
        }

        $attachment->delete();

        return $this->backToGoal($section, 'File removed.');
    }

    private function assertFileExists(Attachment $attachment): void
    {
        abort_unless(
            $attachment->stored_path && Storage::disk(self::DISK)->exists($attachment->stored_path),
            404,
            'This is sample data with no stored file.'
        );
    }

    private function detectType(string $extension): string
    {
        $extension = strtolower($extension);

        return match (true) {
            $extension === 'pdf' => 'pdf',
            in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'bmp'], true) => 'img',
            in_array($extension, ['xls', 'xlsx', 'csv'], true) => 'xls',
            default => 'doc',
        };
    }

    private function backToGoal(GoalSection $section, string $status)
    {
        $goal = $section->goal;

        return redirect()->route('dashboard', [
            'pod' => $goal->client->pod_id,
            'client' => $goal->client_id,
            'open' => $goal->id,
            'tab' => $section->type,
        ])->with('status', $status);
    }
}
