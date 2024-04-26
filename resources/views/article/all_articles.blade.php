<x-app-layout>
    <div>
        <form>
            <select name="category_id" onChange=this.form.submit()>
                <option value="0" {{ !$category_id?'selected="selected"':'' }}>{{ __('All categories') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $category->id==$category_id?'selected="selected"':''}}>{{ $category->name }}</option>
                @endforeach
            </select>
        </form>
        <ul>
            @foreach($articles as $article)
                @if((!$article->is_premium || Auth::user()->is_premium) || $article->user_id == Auth::user()->id)
                    <li>
                        <a href="/article/{{$article->id}}">{{ $article->title }}</a>
                        @if($article->is_premium)
                            {{  __('PREMIUM ARTICLE') }}
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</x-app-layout>