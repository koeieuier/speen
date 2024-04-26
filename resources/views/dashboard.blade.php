<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }} <a href="/articles">{{ __("All articles") }}</a>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Welcome ") }} {{ Auth::user()->name }}, {{__("You have")}}
                    {{ count(Auth::user()->articles) }} {{__("articles published.")}}
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Write a new article") }}
                    <form method="POST" action="/article/new" enctype="multipart/form-data" >
                        @csrf
                        @if($categories)
                            <select multiple="multiple" name="category[]">
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{ $category->name }}</option>
                                @endforeach
                            </select></br>
                        @endif
                        {{ __("New category:") }}<input type="text" name="new_category" /><br />
                        <input type="text" name="title" /><br />
                        <textarea name="content"></textarea><br />
                        <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
                        <input type="file" name="image" accept="image/png, image/jpeg, image/gif" /><br />
                        {{ __('Premium:') }}<input type="checkbox" name="is_premium" /><br />
                        <input type="submit" />
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Your articles:") }}
                    <ul>
                        @foreach(Auth::user()->articles as $article)
                            <li>
                                <a href="/article/{{$article->id}}">{{ $article->title }}</a>
                                @if($article->is_premium)
                                    {{  __('PREMIUM ARTICLE') }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
