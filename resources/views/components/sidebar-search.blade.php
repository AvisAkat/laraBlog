<div>
    <form action="{{ route('blog.search_posts') }}" method="GET">
        <div>
            <div class="search-bar">
                <div class="search-bar-input text-center">
                    <input type="text" name="articleSearch" placeholder="Search articles..."
                        value="{{ request('articleSearch') ?? '' }}">
                    @error('articleSearch')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <button type="submit">Search</button>
                </div>
            </div>
        </div>
    </form>
</div>