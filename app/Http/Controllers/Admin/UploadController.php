<?php

namespace App\Http\Controllers\Admin;

use App\Annotations\Permission;
use App\Http\Controllers\ResponseHelper;
use App\Models\AlbumImage as Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    use ResponseHelper;

    /**
     * 上传图片至相册
     * @Permission()
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function uploadImageToAlbum(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|image',
            'album_id' => 'present|integer'
        ]);
        $file = $request->file('file');
        $path = $file->store('public/album_images');
        if ($path === false) {
            return $this->respond(-1, '文件上传失败！');
        }
        $image = new Image();
        $image->album_id = $request->post('album_id');
        $image->sort = 0;
        $image->name = $file->getClientOriginalName();
        $image->size = $file->getSize();
        $image->path = $path;
        if ($image->save()) {
            return $this->success_respond(Storage::url($path), '上传成功');
        }
        return $this->respond(-1, '文件上传失败！！');
    }

    /**
     * 上传图片
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function uploadImage(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|image'
        ], [], [
            'file' => '图片',
        ]);
        $file = $request->file('file');
        $path = $file->store('public/text_images');
        return $this->success_respond(['src' => Storage::url($path), 'title' => $file->getClientOriginalName()], '上传成功');
    }
}
