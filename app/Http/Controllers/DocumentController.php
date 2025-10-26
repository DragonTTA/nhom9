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
        if(!empty($params['type']) && $params['type'] == "-1"){
            unset($params['type']);
        }
        if(auth()->user()->hasRole('student')){
            $params['type'] = 2; // Chỉ hiện công văn dành cho sinh viên
        }
        if(!empty($params['files_name'])){
            $paramsRelation['files']['name'] = $params['files_name'];
        }
        if(!empty($params['users_name'])){
            $paramsRelation['users']['name']  = $params['users_name'];
        }
        unset($params['files_name']);
        unset($params['users_name']);

        $documents = $this->documentService->getListByFilter($params,['files'],$paramsRelation ?? []);
        return view('documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'title' => 'required',
            'files.*' => 'required|file',
        ]);

        $document = $this->documentService->create([
            'name' => $request->name,
            'title' => $request->title,
            'user_id' => auth()->id(),
            'type' => $request->type,
        ]);

        foreach($request->file('files') as $file){
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
        return response()->json(['status'=>'success','document'=>$document->load('files')]);
    }

    public function edit($id)
    {
        $doc = $this->documentService->getByFilter(['id'=>$id],['files']);
        if (!$doc) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy công văn!']);
        }

        return response()->json(['status' => 'success', 'data' => $doc]);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'title' => 'required',
            'files.*' => 'nullable|file',
        ]);

        try {
            DB::beginTransaction();
            $document = $this->documentService->getByFilter(['id' => $id]);
            if(auth()->user()->hasRole('teacher') && $document->user_id != auth()->id()){
                toast('Bạn không có quyền sửa công văn này!','error');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền sửa công văn này!'],
                    200);
            }
            if (!$document) {
                toast('Không tìm thấy công văn!','error');
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
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi cập nhật công văn: ' . $e->getMessage(),
            ], 500);
        }
    }




    public function destroy($id)
    {
        $document = $this->documentService->getByFilter(['id'=>$id]);
        if(auth()->user()->hasRole('teacher') && $document->user_id != auth()->id()){
            toast('Bạn không có quyền xóa công văn này!','error');
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
