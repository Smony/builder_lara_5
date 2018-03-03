<?php

namespace Vis\Builder;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class EditorController extends Controller
{
    /*
   * Loading photos from froala Editor
   */
    public function uploadFoto()
    {
        $photo = Input::file('file');

        $rules = [
            'file'  => 'required|image',
        ];

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json(['status' => 'error', 'errors_messages'=>$validator->messages()]);
        }

        $destinationPath = 'storage/editor/fotos';

        $ext = $photo->getClientOriginalExtension();  // Get real extension according to mime type
        $fullname = $photo->getClientOriginalName(); // Client file name, including the extension of the client
        $hashname = md5(date('H.i.s').'_'.$fullname).'.'.$ext; // Hash processed file name, including the real extension

        $full_path_img = '/'.$destinationPath.'/'.$hashname;

        Input::file('file')->move($destinationPath, $hashname);

        return Response::json(['link' => $full_path_img]);
    }

    //end uploadFoto

    /*
     * Loading files from froala Editor
     */
    public function uploadFile()
    {
        $file = Input::file('file');

        $rules = [
            'file'  => 'required',
        ];

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json(['status' => 'error', 'errors_messages'=>$validator->messages()]);
        }

        $destinationPath = 'storage/editor/files';

        $ext = $file->getClientOriginalExtension();  // Get real extension according to mime type
        $fullname = $file->getClientOriginalName(); // Client file name, including the extension of the client
        $hashname = md5(date('H.i.s').'_'.$fullname).'.'.$ext; // Hash processed file name, including the real extension

        $full_path_img = '/'.$destinationPath.'/'.$hashname;

        Input::file('file')->move($destinationPath, $hashname);

        return Response::json(['link' => $full_path_img]);
    }

    //end uploadFile

    /*
     * load img manager
     */
    public function loadImages()
    {
        $imgs = scandir(public_path().'/storage/editor/fotos');

        unset($imgs[0]);
        unset($imgs[1]);

        $imgRes = [];
        $k = 0;
        foreach ($imgs as $img) {
            if (is_file(public_path().'/storage/editor/fotos/'.$img)) {
                $imgRes[$k]['url'] = '/storage/editor/fotos/'.$img;
                $imgRes[$k]['thumb'] = '/storage/editor/fotos/'.$img;
                $k++;
            }
        }

        return Response::json($imgRes);
    }

    /*
     * delete img in folder
     */
    public function deleteImages()
    {
        unlink(public_path().Input::get('src'));
    }

    public function doQuickEdit()
    {
        $model = Input::get('model');
        $id = Input::get('id');
        $field = Input::get('field');
        $text = Input::get('text');

        $page = $model::find($id);
        $page->$field = $text;
        $page->save();
    }
}
