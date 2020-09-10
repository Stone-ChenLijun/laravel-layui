<?php

namespace App\Http\Controllers\Admin;

use App\Annotations\Permission;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper;
use App\Models\News;
use App\Rules\Sort;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    use ResponseHelper;

    public function listView()
    {
        return view('admin/index/news_list');
    }

    public function addView()
    {
        return view('admin/index/add_news');
    }

    public function editView(Request $request)
    {
        $item = News::find($request->id);
        if (empty($item)) {
            return abort(404, '指定公告不存在');
        }
        return view('admin/index/edit_news', ['item' => $item]);
    }

    /**
     * 公告列表
     * @Permission(action="admin.news.list")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $query = News::query()->orderByDesc('sort')->orderByDesc('created_at');
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
                'sort' => $item->sort,
                'is_show' => $item->is_show,
                'created_at' => Carbon::parse($item->created_at)->toDateTimeString(),
                'updated_at' => Carbon::parse($item->updated_at)->toDateTimeString(),
            ];
        });
        return $this->success_respond($result);
    }

    /**
     * 新建公告
     * @Permission(action="admin.news.add")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function add(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:32',
            'content' => 'present|string',
            'sort' => ['sometimes', new Sort()],
            'is_show' => 'required|boolean',
        ], [], [
            'title' => '标题',
            'content' => '公告内容',
            'sort' => '排序',
            'is_show' => '是否显示',
        ]);
        $item = new News($request->only(['title', 'content', 'is_show']));
        if (strlen($request->sort)) {
            $item->sort = $request->sort;
        }
        return $item->save() ? $this->success_msg('新建成功') : $this->respond(-1, '新建失败');
    }

    /**
     * 编辑公告
     * @Permission(action="admin.news.edit")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function edit(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'title' => 'required|string|max:32',
            'content' => 'present|string',
            'sort' => ['sometimes', new Sort()],
            'is_show' => 'required|boolean',
        ], [], [
            'id' => '公告',
            'title' => '标题',
            'content' => '公告内容',
            'sort' => '排序',
            'is_show' => '是否显示',
        ]);
        $item = News::find($request->id);
        if (empty($item)) {
            return $this->respond(-1, '指定公告不存在');
        }
        $item->fill($request->only(['title', 'content', 'is_show']));
        if (strlen($request->sort)) {
            $item->sort = $request->sort;
        }
        return $item->save() ? $this->success_msg('修改成功') : $this->respond(-1, '修改失败');
    }

    /**
     * 删除公告
     * @Permission(action="admin.news.delete")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ], [], [
            'id' => '公告'
        ]);
        $item = News::find($request->id);
        if (empty($item)) {
            return $this->respond(-1, '指定公告不存在');
        }
        return $item->delete() ? $this->success_msg('删除成功') : $this->respond(-1, '删除失败');
    }
}
