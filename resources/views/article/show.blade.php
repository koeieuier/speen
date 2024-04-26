<x-app-layout>
@if($article->user_id == Auth::user()->id)
    <form action="/article/{{$article->id}}" method="POST">
        @csrf
        @method('DELETE')
        <input type="submit" value="Delete me!"/>
    </form>
@endif
<br />
{{ __("Written by ") }} {{ $article->user->name }}:
@if($article->user_id == Auth::user()->id)
    <form action="/article/{{$article->id}}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PATCH')
        <select name="category[]" multiple="multiple">
            @foreach($categories as $cat)
                @if($article->categories->contains($cat->id))
                    <option value="{{$cat->id}}" selected="selected">{{ $cat->name }}</option>
                @else
                    <option value="{{$cat->id}}">{{ $cat->name }}</option>
                @endif
            @endforeach

        </select>
        <input name="title" type="text" value="{{ $article->title }}" /><br />
        <textarea name="content">{{ $article->content }}</textarea>
        <input type="hidden" name="MAX_FILE_SIZE" value="30000000" /><br />
        @if(empty($article->image))
            Optional: add an image (gif, jpeg or png)
            <input type="file" name="image" accept="image/png, image/jpeg, image/gif" />
        @else
            <img src="/images/{{ $image_file_name }}" />
        @endif
        {{ __('Premium:') }}
        @if($article->is_premium)
            <input type="checkbox" name="is_premium" checked="checked" />
        @else
            <input type="checkbox" name="is_premium" />
        @endif
        <input type="submit" />
    </form>
    <ul>
        @foreach($article->comments as $comment)
            <li>{{$comment->user->name}}:<br />{{ $comment->content }}</li>
        @endforeach
    </ul>
    Write a comment <br />
    <form action="/article/{{$article->id}}/comment" method="POST">
        @csrf
        <textarea name="content"></textarea>
        <input type="submit" />
    </form>
@else
    @if(!$article->is_premium || Auth::user()->is_premium)
        {{ $article->title }}<br />
        {{ $article->content }}<br />
        @if($article->image)
            <img src="/images/{{ $image_file_name }}" />
        @endif

        Categories: 
        @foreach($article->categories as $cat)
            {{ $cat->name }}
        @endforeach
        <br />
        {{ count($article->comments)  }} {{ __(" comments") }} <br />
        <ul>
            @foreach($article->comments as $comment)
                <li>{{$comment->user->name}}:<br />{{ $comment->content }}</li>
            @endforeach
        </ul>

        Write a comment <br />
        <form action="/article/{{$article->id}}/comment" method="POST">
            @csrf
            <textarea name="content"></textarea>
            <input type="submit" />
        </form>
    @else
        {{ __('A premium article.') }}
    @endif
@endif
        <br />
</x-app-layout>