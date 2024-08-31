@foreach($subcategories as $subcategory)
    <ul>
        <li>
            <label>
                <input type="checkbox" name="category[]" value="{{$subcategory->id}}" {{in_array($subcategory->id, $product->categories->pluck('id')->toarray())?'checked':''}}>
                {{$subcategory->name}}
            </label>
        </li>
        @if(count($subcategory->subcategory))
            @include('Customer.product.subCategoryListEdit',['subcategories' => $subcategory->subcategory])
        @endif
    </ul>
@endforeach
