<?php
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with('files')->get();
        return view('documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'title' => 'required',
            'files.*' => 'required|file',
        ]);

        $document = Document::create([
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

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        foreach ($document->files as $file) {
            Storage::disk('public')->delete($file->path);
            $file->delete();
        }
        $document->delete();
        toast("Xóa công văn file thành công");

        return response()->json(['status' => 'success']);
    }
}
