<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\Nice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        // 検索しなかったとき降順で表示する
        $query = Post::with('user')->latest();
        if (isset($search)) {
            $posts = $query->where('title', 'LIKE', "%{$search}%")->paginate(4);
        } else {
            $posts = $query->paginate(4);
        }
        return view('posts.index', compact('posts', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $post = new Post($request->all());
        $post->user_id = $request->user()->id;
        $file = $request->file('image');
        $post->image = self::createFileName($file);

        // トランザクション開始
        DB::beginTransaction();
        try {
            // 登録
            $post->save();

            // 画像アップロード
            if (!Storage::putFileAs('images/posts', $file, $post->image)) {
                // 例外を投げてロールバックさせる
                throw new \Exception('画像ファイルの保存に失敗しました。');
            }

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()
            ->route('posts.show', $post)
            ->with('notice', '記事を登録しました');
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     $post = Post::find($id);
    //     $nice = Nice::where('post_id', $post->id)->where('user_id', auth()->user()->id)->first();

    //     return view('posts.show', compact('post', 'nice'));
    // }
    public function show(Post $post)
    {
        $request = request();
        $ip = $request->ip();
        $nice = Nice::where('post_id', $post->id)->where('ip', $ip)->first();
        return view('posts.show', compact('post', 'nice'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::find($id);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, string $id)
    {
        $post = Post::find($id);

        if ($request->user()->cannot('update', $post)) {
            return redirect()->route('posts.show', $post)
                ->withErrors('自分の記事以外は更新できません');
        }

        $file = $request->file('image');
        if ($file) {
            $delete_file_path = $post->image_path;
            $post->image = self::createFileName($file);
        }
        $post->fill($request->all());

        // トランザクション開始
        DB::beginTransaction();
        try {
            // 更新
            $post->save();

            if ($file) {
                // 画像アップロード
                if (!Storage::putFileAs('images/posts', $file, $post->image)) {
                    // 例外を投げてロールバックさせる
                    throw new \Exception('画像ファイルの保存に失敗しました。');
                }

                // 画像削除
                if (!Storage::delete($delete_file_path)) {
                    //アップロードした画像を削除する
                    Storage::delete($post->image_path);
                    //例外を投げてロールバックさせる
                    throw new \Exception('画像ファイルの削除に失敗しました。');
                }
            }

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()->route('posts.show', $post)
            ->with('notice', '記事を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);

        // トランザクション開始
        DB::beginTransaction();
        try {
            $post->delete();

            // 画像削除
            if (!Storage::delete($post->image_path)) {
                // 例外を投げてロールバックさせる
                throw new \Exception('画像ファイルの削除に失敗しました。');
            }

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            return back()->withErrors($e->getMessage());
        }

        return redirect()->route('posts.index')
            ->with('notice', '記事を削除しました');
    }

    private static function createFileName($file)
    {
        return date('YmdHis') . '_' . $file->getClientOriginalName();
    }
}
