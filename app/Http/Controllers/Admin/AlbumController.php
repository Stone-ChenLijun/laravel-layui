<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper;
use App\Models\Album;
use App\Models\AlbumImage as Image;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    use ResponseHelper;

    public function managementView(Request $request)
    {
        $tree = Album::query()->where('parent_id', 0)
            ->with('children')->orderByDesc('sort')
            ->get([
            'id', 'name', 'parent_id'
        ]);
        return view('admin/album/album_management', ['tree' => [
            [
                'id' => 0,
                'name' => '全部相册',
                'children' => $tree]]
            ]
        );
    }

    public function addAlbum(Request $request)
    {
        $this->validate($request, [
            'parent_id' => 'required',
            'name' => 'required|string|min:1|max:16',
        ]);
        $album = new Album();
        $album->name = $request->post('name');
        $album->parent_id = $request->post('parent_id');
        return $album->save() ? $this->success_respond($album->id, '新建成功') : $this->respond(-1, '新建相册失败');
    }

    public function editAlbum(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'name' => 'required|string|min:1|max:8',
        ]);
        $album = Album::find($request->id);
        if (empty($album)) {
            return $this->respond(-1, '指定相册不存在');
        }
        $album->name = $request->name;
        return $album->save() ? $this->success_respond('修改成功') : $this->respond(-1, '修改相册失败');
    }

    public function deleteAlbum(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:albums']);
        $albumId = intval($request->get('id'));
        $temp = [$albumId];
        $albumIds = null;
        while (true) {
            $t = Album::whereIn('parent_id', $temp)->pluck('id')->merge($temp)->unique()->values()->toArray();
            if ($t == $temp) {
                $albumIds = $t;
                break;
            }
            $temp = $t;
        }
        $count1 = count($albumIds) - 1;
        $count2 = Image::whereIn('album_id', $albumIds)->count();
        if ($count1 > 0 || $count2 > 0) {
            return $this->respond(-1, "指定相册下仍有{$count1}个子相册与{$count2}张图片，无法删除");
        }
        $item = Album::find($request->post('id'));
        return $item->delete() ? $this->success_msg('删除成功') : $this->respond(-1, '删除相册失败');
    }

    public function getAlbumImage(Request $request)
    {
        $albumId = $request->album_id;
        $query = Image::orderBy('sort', 'desc')->orderBy('created_at', 'desc');
        if ($albumId > 0) {
            $albumIds = collect($albumId);
            if (!Album::where('id', $albumId)->exists()) {
                return $this->respond(-1, "id为{$albumId}的相册不存在");
            }
            $temp = $albumIds;
            while (true) {
                $t = Album::whereIn('parent_id', $temp)->pluck('id')
                    ->merge($temp)->unique()->values();
                if ($t == $temp) {
                    $albumIds = $t;
                    break;
                }
                $temp = $t;
            }
            $query->whereIn('album_id', $albumIds);
        }
        $result = $query->paginate($request->page_size);
        $result->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'url' => $item->preview_url,
                'size' => $item->size,
                'created_at' => Carbon::parse($item->created_at)->toDateTimeString(),
            ];
        });
        return $this->success_respond($result);
    }

    public function editImage(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'name' => 'required|string|max:256'
        ], [], [
            'id' => '图片id',
            'name' => '文件名',
        ]);
        $image = Image::find($request->id);
        if (empty($image)) {
            return $this->respond(-1, '指定图片不存在');
        }
        $image->name = $request->name;
        return $image->save() ? $this->success_msg('修改成功') : $this->respond(-1, '修改失败');
    }

    public function deleteImage(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);
        $image = Image::find($request->post('id'));
        if (empty($image)) {
            return $this->respond(-1, "id为{$request->id}的图片不存在");
        }
        return $image->delete() ? $this->success_msg('删除成功') : $this->respond(-1, '删除失败');
    }

    public function albumPhotoSelector(Request $request)
    {
        $tree = Album::where('parent_id', 0)->with('children')->orderBy('sort', 'desc')->get([
            'id', 'name', 'parent_id'
        ]);
        return view('admin/album/album_photo_selector', ['tree' => [[
                'id' => 0,
                'name' => '全部相册',
                'children' => $tree]]
            ]
        );
    }
}
