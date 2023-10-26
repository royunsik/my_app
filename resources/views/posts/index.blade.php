<x-app-layout>

    <div class="container max-w-5xl mx-auto px-4 md:px-12 pb-3 mt-3 break-words">

        <x-flash-message :message="session('notice')" />
    
        <div id="home" class="big-bg">
            <img src="{{ asset('img/11062b_cd3175f6aa574fd099bc7dcfae4e608b~mv2.jpeg') }}" class="bg-cover mt-8">
            <div class="home-content wrapper">
                <h2 class="page-title text-5xl text-center my-12 font-bold">牛タンの数だけ笑顔がある<h2>
            </div><!-- /.home-content -->
        </div><!-- /#home -->

        <form method="GET" action="{{ route('posts.index') }}">   
            <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input name="search" type="search" id="default-search" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="お店の名前、地名" required>
                <button type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">検索</button>
            </div>
        </form>

        <div class="flex flex-wrap -mx-1 lg:-mx-1 mb-4">
            @foreach ($posts as $post)
                <article class="w-full px-4 md:w-1/2 text-xl text-gray-800 leading-normal">
                    <a href="{{ route('posts.show', $post) }}">
                        <h2
                            class="font-bold font-sans break-normal text-gray-900 pt-12 pb-1 text-3xl md:text-4xl break-words">
                            {{ $post->title }}</h2>
                        <h3>{{ $post->user->name }}</h3>
                        <p class="text-sm mb-2 md:text-base font-normal text-gray-600">
                            <span
                                class="text-red-400 font-bold">{{ date('Y-m-d H:i:s', strtotime('-1 day')) < $post->created_at ? 'NEW' : '' }}</span>
                            {{ $post->created_at }}
                        </p>
                        <img class="w-full mb-2  object-cover h-48 w-96" src="{{ $post->image_url }}" alt="">
                        <p class="text-gray-700 text-base">{{ Str::limit($post->body, 50) }}</p>
                    </a>
                </article>
            @endforeach
        </div>
        {{ $posts->links() }}
    </div>

</x-app-layout>
