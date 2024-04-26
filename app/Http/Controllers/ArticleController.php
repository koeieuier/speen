<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Article;
use App\Models\Image;
use App\Models\Category;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class ArticleController extends Controller
{
    //show the list of all (available) articles.
    public function all(Request $request): View
     {
        if($request->input('category_id')) {
            $filtered_articles = Article::join('article_category', 'articles.id', '=', 'article_category.article_id')
                                        ->where('article_category.category_id', $request->input('category_id'))
                                        ->get('articles.*'); //explicitly only get() article.* to prevent the id being overwritten
                                        //This is a feature, not a bug.
        }

        return view('article.all_articles', [
            'articles' => $request->input('category_id')?$filtered_articles:Article::all(),
            'categories' => Category::all(),  //possible improvement: show only categories in use
            'category_id' => $request->input('category_id')?$request->input('category_id'):0,
        ]);
    }

    //show a single article
    public function show(string $id): View
    {
        $article = Article::FindOrFail($id);
        $categories = Category::all();

        $image_file_info = false;
        if($article->image) {
            $image_file_info = pathinfo($article->image->path);
        }
        
        return view('article.show', [
            'article' => $article,
            'image_file_name' => $image_file_info ? $image_file_info['basename'] : null,
            'categories' => $categories,
        ]);
    }

    //create a new article, possibly including category and an image
    public function new(Request $request): RedirectResponse
    {
        $title=$request->input('title');
        $content=$request->input('content');
        $is_premium = null !== $request->input('is_premium');
        $allowed_file_extensions = ['jpg', 'jpeg', 'gif', 'png'];

        //handle image upload
        if($request->hasFile('image') && $request->file('image')->isValid() && in_array($request->image->extension(), $allowed_file_extensions)) {
           //upload is valid, and has an allowed filetype
           $request->image->store('images');
           $filename = $request->image->hashName();
           $path = $request->image->move(public_path('images'), $filename);
           $img = new Image(['path' => $path]);
           $img->save();
        }

        $categories = array();

        //handle existing categories
        if($request->input('category')) {
            foreach($request->input('category') as $cat_id) {
                $cat = Category::FindOrFail($cat_id);
                $categories[] = $cat->id;
            }
        }

        //handle a new category
        if($request->input('new_category')) {
            $new_category = new Category(["name"=>$request->input('new_category')]);
            $new_category->save();
            $categories[] = $new_category->id;
        }

        $image_id = isset($img) ? $img->id : null;
        $article_data = ['title' => $title, 'content' => $content, 'user_id'=>Auth::user()->id, 'image_id' => $image_id, 'is_premium' => $is_premium];
        $art = new Article($article_data);
        $art->save();
        $art->categories()->sync($categories);

        return redirect()->route('article.show', $art->id);
    }

    //delete an article, its comments, image and clean up the article_category table. Categories are not deleted (even if unused).
    public function destroy(string $id): RedirectResponse
    {
        $art = Article::FindOrFail($id);

        if($art && Auth::user()->id == $art->user_id)
            //delete images
            if($art->image)
                unlink($art->image->path) && $art->image->delete(); //remove it from the filesystem and the db

            //delete comments
            if($art->comments) {
                foreach($art->comments as $comment)
                    $comment->delete();
            }

            //remove categories from article
            $art->categories()->sync([]);
            $art->delete();

        return redirect()->route('dashboard', [Auth::user()->id]);
    }

    //update an article
    public function patch(Request $request, string $id): RedirectResponse
    {
        $art = Article::FindOrFail($id);
        $is_premium = ( null !== $request->input('is_premium') );

        //make sure user is the author of the article
        if($art && Auth::user()->id == $art->user_id) {
            if($request->hasFile('image') && $request->file('image')->isValid()) {
                //upload is valid
                $path = $request->image->store('images');
                $filename = $request->image->hashName();
                $path = $request->image->move(public_path('images'), $filename);
                $img = new Image(['path' => $path]);
                $img->save();
                $art->image_id=$img->id;
            }
            $art->content = $request->input('content');
            $art->title = $request->input('title');
            $art->is_premium = $is_premium;

            if($request->input('category')) {
                foreach($request->input('category') as $cat_id) {
                    $cat = Category::FindOrFail($cat_id); //make sure the category exists
                    $categories[] = $cat->id;
                }
            }
            $art->save();
            $art->categories()->sync($categories);
        }
        return redirect()->route('article.show', $art->id);
    }
}
