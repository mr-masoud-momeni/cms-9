@foreach($subcategories as $subcategory)
    <ul>
        <li>
            <label>
                <input type="checkbox" name="category[]" value="{{ $subcategory->id }}"
                    {{ $article->categories->pluck('id')->contains($subcategory->id) ? 'checked' : '' }}>
                {{ $subcategory->name }}
            </label>
        </li>

        @if($subcategory->subcategory && $subcategory->subcategory->count())
            @include('Customer.article.subCategoryListEdit', ['subcategories' => $subcategory->subcategory, 'article' => $article])
        @endif
    </ul>
@endforeach
