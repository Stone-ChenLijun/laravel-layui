<?php

namespace App\Http\Controllers\Admin;

use App\Annotations\Permission;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper;
use App\Models\AlbumImage;
use App\Models\Carousel;
use App\Rules\Sort;
use App\Rules\Url;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NavController extends Controller
{
    use ResponseHelper;

    public function listView()
    {
        return view('admin/index/nav_list');
    }

    public function addView()
    {
        return view('admin/index/add_nav');
    }

    public function editView(Request $request)
    {
        $item = Carousel::find($request->id);
        if (empty($item)) {
            return abort(404, '指定导航栏不存在');
        }
        return view('admin/index/edit_nav', ['item' => $item]);
    }

    public function list(Request $request)
    {
        $query = Carousel::query()->where('type', Carousel::TYPE_NAV)->orderByDesc('sort');
        if ($request->is_show == 1) {
            $query->where('is_show', true);
        } elseif ($request->is_show === 0 || $request->is_show === '0') {
            $query->where('is_show', false);
        }
        if (strlen($request->title)) {
            $query->where('title', 'like', "%{$request->title}%");
        }
        $result = $query->paginate($request->page_size);
        $result->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'image_id' => $item->image_id,
                'preview_url' => $item->image->preview_url,
                'url' => $item->url,
                'sort' => $item->sort,
                'is_show' => $item->is_show,
                'created_at' => Carbon::parse($item->created_at)->toDateTimeString(),
                'updated_at' => Carbon::parse($item->updated_at)->toDateTimeString(),
            ];
        });
        return $this->success_respond($result);
    }

    /**
     * 新建导航栏
     * @Permission(action="admin.nav.add")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function add(Request $request)
    {
        $this->validate($request, [
            'image_id' => 'required|integer',
            'title' => 'required|string|between:1,32',
            'sort' => ['sometimes', new Sort()],
            'url' => ['sometimes', 'string', new Url()],
            'is_show' => 'required|boolean',
        ], [], [
            'image_id' => '图片',
            'title' => '标题',
            'sort' => '排序',
            'url' => '跳转链接',
            'is_show' => '是否显示',
        ]);
        $image = AlbumImage::find($request->image_id);
        if (empty($image)) {
            return $this->respond(-1, '指定图片不存在');
        }
        $item = new Carousel();
        if (empty($item)) {
            return $this->respond(-1, '指定图片不存在');
        }
        $item->type = Carousel::TYPE_NAV;
        $item->image_id = $image->id;
        if (!strlen($request->url)) {
            $item->url = '#';
        } else {
            $item->url = $request->url;
        }
        if (strlen($request->sort)) {
            $item->sort = $request->sort;
        }
        $item->fill($request->only(['title', 'is_show']));
        return $item->save() ? $this->success_msg('新建成功') : $this->respond(-1, '新建失败');
    }

    /**
     * 修改导航栏
     * @Permission(action="admin.nav.edit")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function edit(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'image_id' => 'required|integer',
            'title' => 'required|string|between:1,32',
            'sort' => ['sometimes', new Sort()],
            'url' => ['sometimes', 'string', new Url()],
            'is_show' => 'required|boolean',
        ], [], [
            'id' => '导航栏',
            'image_id' => '图片',
            'title' => '标题',
            'sort' => '排序',
            'url' => '跳转链接',
            'is_show' => '是否显示',
        ]);
        $item = Carousel::where('type', Carousel::TYPE_NAV)->where('id', $request->id)->first();
        if (empty($item)) {
            return $this->respond(-1, '指定导航栏不存在');
        }
        $image = AlbumImage::find($request->image_id);
        if (empty($image)) {
            return $this->respond(-1, '指定图片不存在');
        }
        $item->image_id = $image->id;
        if (!strlen($request->url)) {
            $item->url = '#';
        } else {
            $item->url = $request->url;
        }
        if (strlen($request->sort)) {
            $item->sort = $request->sort;
        }
        $item->fill($request->only(['title', 'is_show']));
        return $item->save() ? $this->success_msg('修改成功') : $this->respond(-1, '修改失败');
    }

    /**
     * 删除导航栏
     * @Permission(action="admin.nav.delete")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ], [], [
            'id' => '导航栏'
        ]);
        $item = Carousel::where('type', Carousel::TYPE_NAV)->where('id', $request->id)->first();
        if (empty($item)) {
            return $this->respond(-1, '指定导航栏不存在');
        }
        return $item->delete() ? $this->success_msg('删除成功') : $this->respond(-1, '删除失败');
    }
}
