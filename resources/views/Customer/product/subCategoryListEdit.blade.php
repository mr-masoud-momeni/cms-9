@foreach($subcategories as $subcategory)
    <ul>
        <li>
            <label>
                <input type="checkbox" name="category[]" value="{{$subcategory->id}}" {{in_array($subcategory->id, $article->categories->pluck('id')->toarray())?'checked':''}}>
                {{$subcategory->name}}
            </label>
        </li>
        @if(count($subcategory->subcategory))
            @include('Backend.article.subCategoryListEdit',['subcategories' => $subcategory->subcategory])
        @endif
    </ul>
@endforeach
