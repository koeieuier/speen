<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;


class CommentController extends Controller
{
    //
    public function create(Request $request, string $id): RedirectResponse {
        $art = Article::FindOrFail($id);

        if($art) {
            $comment = new Comment;
            $comment->content = $request->input('content');
            $comment->user_id = Auth::user()->id;
            $comment->article_id = $art->id; 
            $comment->save();
            
            return redirect()->route('article.show', $art->id); //show article
        }
        //there was no article to comment to, send the user to list of own articles (a.k.a. dashboard). This should never happen.
        return redirect()->route('dashboard', [Auth::user()->id]);
    }
}
