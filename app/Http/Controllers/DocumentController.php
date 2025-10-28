<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentFile;
use App\Services\DocumentFileService;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    protected $documentService;
    protected $documentFileService;

    public function __construct(DocumentService $documentService, DocumentFileService $documentFileService)
    {
        $this->documentService = $documentService;
        $this->documentFileService = $documentFileService;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        if (!empty($params['type']) && $params['type'] == "-1") {
            unset($params['type']);
        }
        if (auth()->user()->hasRole('student')) {
            $params['type'] = 2; // Chỉ hiện công văn dành cho sinh viên
        } else if (auth()->user()->hasRole('teacher')) {
            $params['type'] = 1; // Chỉ hiện công văn dành cho giáo viên
        }
        if (!empty($params['files_name'])) {
            $paramsRelation['files']['name'] = $params['files_name'];
        }
        if (!empty($params['users_name'])) {
            $paramsRelation['users']['name'] = $params['users_name'];
        }
        unset($params['files_name']);
        unset($params['users_name']);

        $documents = $this->documentService->getListByFilter($params, ['files'], $paramsRelation ?? []);
        return view('documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $setting = \App\Models\UploadSetting::latest()->first();
        $maxSizeKB = ($setting->max_file_size_mb ?? 10) * 1024;
        $allowedTypes = explode(',', str_replace(' ', '', $setting->allowed_types));
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'title' => 'required|string|max:500',
                'type' => 'nullable|integer',
                'files' => 'required|array|max:5',
                'files.*' => "file|max:$maxSizeKB|mimes:" . implode(',', $allowedTypes),
            ], [
                'files.max' => 'Bạn chỉ được tải lên tối đa 5 file.',
                'files.*.max' => "Mỗi file không được vượt quá {$setting->max_file_size_mb}MB.",
                'files.*.mimes' => "Chỉ được tải các định dạng: {$setting->allowed_types}.",
            ]);
            $document = $this->documentService->create([
                'name' => $request->name,
                'title' => $request->title,
                'user_id' => auth()->id(),
                'type' => $request->type,
            ]);
            foreach ($request->file('files') as $file) {
                $name = $file->getClientOriginalName();
                $ext = strtolower($file->getClientOriginalExtension());
                $path = $file->store('documents', 'public');
                $document->files()->create([
                    'name' => $name,
                    'path' => $path,
                    'type' => $ext,
                ]);
            }

            toast("Tải công văn thành công");
            return response()->json([
                'status' => 'success',
                'document' => $document->load('files'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $messages = collect($e->errors())->flatten()->implode('<br>');
            toast($messages, 'error');

            return response()->json([
                'status' => 'error',
                'message' => $messages,
            ], 422);
        } catch (\Exception $e) {
            toast('Đã xảy ra lỗi không mong muốn!', 'error');
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi không mong muốn!',
            ], 500);
        }
    }

    public function edit($id)
    {
        $doc = $this->documentService->getByFilter(['id' => $id], ['files']);
        if (!$doc) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy công văn!']);
        }

        return response()->json(['status' => 'success', 'data' => $doc]);
    }

    public function update(Request $request, $id)
    {
        $setting = \App\Models\UploadSetting::latest()->first();
        $maxSizeKB = ($setting->max_file_size_mb ?? 10) * 1024;
        $allowedTypes = explode(',', str_replace(' ', '', $setting->allowed_types));
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'title' => 'required|string|max:500',
                'type' => 'nullable|integer',
                'files' => 'required|array|max:5',
                'files.*' => "file|max:$maxSizeKB|mimes:" . implode(',', $allowedTypes),
            ], [
                'files.max' => 'Bạn chỉ được tải lên tối đa 5 file.',
                'files.*.max' => "Mỗi file không được vượt quá {$setting->max_file_size_mb}MB.",
                'files.*.mimes' => "Chỉ được tải các định dạng: {$setting->allowed_types}.",
            ]);
            DB::beginTransaction();
            $document = $this->documentService->getByFilter(['id' => $id]);
            if (auth()->user()->hasRole('document') && $document->user_id != auth()->id()) {
                toast('Bạn không có quyền sửa công văn này!', 'error');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền sửa công văn này!'],
                    200);
            }
            if (!$document) {
                toast('Không tìm thấy công văn!', 'error');
                return response()->json(['status' => 'error', 'message' => 'Không tìm thấy công văn!'], 200);
            }
            $document->update([
                'name' => $request->name,
                'title' => $request->title,
                'type' => $request->type,
            ]);
            if ($request->hasFile('files')) {
                foreach ($document->files as $oldFile) {
                    if (Storage::disk('public')->exists($oldFile->path)) {
                        Storage::disk('public')->delete($oldFile->path);
                    }
                    $oldFile->delete();
                }
                foreach ($request->file('files') as $file) {
                    $name = $file->getClientOriginalName();
                    $ext = strtolower($file->getClientOriginalExtension());
                    $path = $file->store('documents', 'public');

                    $document->files()->create([
                        'name' => $name,
                        'path' => $path,
                        'type' => $ext,
                    ]);
                }
            }
            DB::commit();
            toast('Cập nhật công văn thành công');
            return response()->json([
                'status' => 'success',
                'document' => $document->load('files'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $messages = collect($e->errors())->flatten()->implode('<br>');
            toast($messages, 'error');

            return response()->json([
                'status' => 'error',
                'message' => $messages,
            ], 422);
        } catch (\Exception $e) {
            toast('Đã xảy ra lỗi không mong muốn!', 'error');
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi không mong muốn!',
            ], 500);
        }
    }


    public function destroy($id)
    {
        $document = $this->documentService->getByFilter(['id' => $id]);
        if (auth()->user()->hasRole('document') && $document->user_id != auth()->id()) {
            toast('Bạn không có quyền xóa công văn này!', 'error');
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có quyền xóa công văn này!'],
                200
            );
        }
        foreach ($document->files as $file) {
            Storage::disk('public')->delete($file->path);
            $file->delete();
        }
        $document->delete();
        toast("Xóa công văn file thành công");

        return response()->json(['status' => 'success']);
    }
}
