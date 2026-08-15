<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait UploadFileTrait
{

    public function UploadFile(Request $request,$FieldName,$folderName){
        return $request->file($FieldName)->store($folderName, 'imgFile');
    }

    public function ReplaceImg($objectModel,$request,$FieldName,$folderName){
        if (!$request->hasFile($FieldName)) {
            return $objectModel;
        }

        $newImage = $this->UploadFile($request, $FieldName, $folderName);
        if ($objectModel) {
            Storage::disk('imgFile')->delete($objectModel);
        }

        return $newImage;
    }
}
