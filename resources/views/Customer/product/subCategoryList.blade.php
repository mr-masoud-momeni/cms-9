@foreach($subcategories as $subcategory)
    <ul>
        <li>
            <label>
                <input type="checkbox" name="category[]" value="{{$subcategory->id}}" >
                {{$subcategory->name}}
            </label>
        </li>
        @if(count($subcategory->subcategory))
            @include('Customer.product.subCategoryList',['subcategories' => $subcategory->subcategory])
        @endif
    </ul>
@endforeach
