<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Nice;
use Illuminate\Support\Facades\Auth;

class NiceController extends Controller
{
    public function nice(Post $post, Request $request)
    {
        $nice = new Nice();
        $nice->post_id = $post->id;
        $nice->ip = $request->ip();

        if (Auth::check()) {
            $nice->user_id = Auth::user()->id;
        }

        $nice->save();
        return back();
    }

    public function unnice(Post $post, Request $request)
    {
        $user=$request->ip();
        $nice=Nice::where('post_id', $post->id)->where('ip', $user)->first();
        $nice->delete();
        return back();
    }
}
