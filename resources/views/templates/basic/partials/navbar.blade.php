<ul class="category-link">
    @foreach ($categories as $category)
    <li>
        <a href="{{ route('category.products',['id'=>$category->id,'name'=>slug($category->name)]) }}">
            {{ __($category->name) }}
        </a>
        <ul class="category-sublink">
            @foreach ($category->subcategories as $subcategory)
            <li>
                <a href="{{ route('subcategory.products',['id'=>$subcategory->id,'name'=>slug($subcategory->name)]) }}">
                    {{ __($subcategory->name) }}
                </a>
            </li>
            @endforeach
        </ul>
    </li>
    @endforeach
    <li>
        <a href="{{ route('all.category') }}">
            @lang('View All Categories')
        </a>
    </li>
</ul>