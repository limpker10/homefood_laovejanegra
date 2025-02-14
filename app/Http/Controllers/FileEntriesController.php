<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileEntry;
use App\Models\Configuracion;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileEntriesController extends Controller
{
    public function uploadFile(Request $request) {

        $file = $request->file;
        $filename = $request->file->getClientOriginalName();
        $path = hash( 'sha256', time());

        if(Storage::disk('uploads')->put($path.'/'.$filename,  File::get($file))) {

            $input['id_producto'] = $request->id_producto;
            $input['filename'] = $filename;
            $input['mime'] = $request->file->getMimeType();
            $input['path'] = $path;
            $input['size'] = $request->file->getSize();
            $file = FileEntry::create($input);

            return response()->json([
                'success' => true,
                'id' => $file->id
            ], 200);
        }

        return response()->json([
            'success' => false
        ], 500);
    }

    public function editFile(Request $request) {
        /*if(\Storage::exists('files/uploads/'.$request->file.'/'.$request->name)){
        if(\Storage::disk('uploads')->exists($request->file.'/'.$request->name)){
            \Storage::disk('uploads')->delete($request->file.'/'.$request->name);
          }else{
            return 'File does not exists.';
          }*/

        $file = $request->file;
        $filename = $request->file->getClientOriginalName();
        $path = hash( 'sha256', time());

        if(Storage::disk('uploads')->put($path.'/'.$filename,  File::get($file))) {
          if(isset($request->path)){
            //$file = FileEntry::find($request->id_producto);
            $file = FileEntry::where('id_producto', $request->id_producto)->first();

            $file->update([
                'filename' => $filename,
                'mime' => $request->file->getMimeType(),
                'path' => $path,
                'size' => $request->file->getSize()
            ]);
            \Storage::disk('uploads')->deleteDirectory($request->path);
          }
          else{
            $input['id_producto'] = $request->id_producto;
            $input['filename'] = $filename;
            $input['mime'] = $request->file->getMimeType();
            $input['path'] = $path;
            $input['size'] = $request->file->getSize();
            $file = FileEntry::create($input);
          }

            return response()->json([
                'success' => true,
                'file' => $file
            ], 200);
        }

        return response()->json([
            'success' => false
        ], 200);
    }

    public function configurationFile(Request $request){
        $file = $request->file;
        $filename = $request->file->getClientOriginalName();
        $path = 'configuration/logo';

        if(Storage::disk('uploads')->put($path.'/'.$filename,  File::get($file))) {

            $file = Configuracion::findOrFail(1);
            //../storage/files/uploads/
            $file->update([
                'logo' => '../storage/files/uploads/'.$path.'/'.$filename
            ]);
            //\Storage::disk('uploads')->deleteDirectory($request->path);
            

            return response()->json([
                'success' => true,
                'file' => $file
            ], 200);
        }

        return response()->json([
            'success' => false
        ], 200);
    }
}
