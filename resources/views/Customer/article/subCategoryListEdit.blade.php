@foreach($subcategories as $subcategory)
    <ul>
        <li>
            <label>
                <input type="checkbox" name="category[]" value="{{ $subcategory->id }}"
                    {{ $article->categories->pluck('id')->contains($subcategory->id) ? 'checked' : '' }}>
                {{ $subcategory->name }}
            </label>
        </li>

        @if(count($subcategory->subcategory))
            @include('Customer.article.subCategoryListEdit', ['subcategories' => $subcategory->subcategory, 'article' => $article])
        @endif
    </ul>
@endforeach
