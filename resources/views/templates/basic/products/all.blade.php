@extends($activeTemplate.'layouts.frontend')
@section('content')

<section class="fd-shop">
    <div class="fd-container">
        <header class="fd-shop__head">
            <h1 class="fd-shop__title">{{ __($pageTitle) }}</h1>
            @if (request()->search)
                <p class="fd-shop__sub">
                    @lang('Showing results for') <b>{{ __(request()->search) }}</b>
                </p>
            @else
                <p class="fd-shop__sub">@lang('Freshly cooked and ready to order')</p>
            @endif
        </header>

        <div class="fd-shop__grid">
            {{-- ---------------------------------------------- filters --}}
            @if ($products->count() > 0)
            <aside class="filter--sidebar fd-filters">
                <div class="close--sidebar d-lg-none" aria-label="@lang('Close filters')">
                    <i class="las la-times"></i>
                </div>

                <div class="filter__widget fd-fcard">
                    <h5 class="filter__widget-title fd-fcard__title">
                        <i class="las la-th-large"></i> @lang('Categories')
                    </h5>
                    <div class="filter__widget-body fd-fcard__body">
                        <label class="fd-check" for="cate-0">
                            <input class="form-check-input sortCategory" name="category" type="checkbox" id="cate-0" value="" checked>
                            <span class="fd-check__box"><i class="las la-check"></i></span>
                            <span class="fd-check__label">@lang('All Categories')</span>
                        </label>
                        @foreach ($categoryList as $category)
                        <label class="fd-check" for="cate{{ $category->id }}">
                            <input class="form-check-input sortCategory" type="checkbox" name="category"
                                id="cate{{ $category->id }}" value="{{ $category->id }}">
                            <span class="fd-check__box"><i class="las la-check"></i></span>
                            <span class="fd-check__label">{{ __($category->name) }}</span>
                            <span class="fd-check__count">{{ $category->product_count }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="filter__widget fd-fcard">
                    <h5 class="filter__widget-title fd-fcard__title">
                        <i class="las la-tag"></i> @lang('Price')
                    </h5>
                    <div class="filter__widget-body fd-fcard__body">
                        <div class="filter-price-widget fd-price">
                            <div id="slider-range"></div>
                            <div class="price-range fd-price__out">
                                <label for="amount">@lang('Price')</label>
                                <input type="text" id="amount" readonly>
                                <input type="hidden" name="min_price" value="{{ getAmount($minPrice) }}">
                                <input type="hidden" name="max_price" value="{{ getAmount($maxPrice) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter__widget fd-fcard">
                    <h5 class="filter__widget-title fd-fcard__title">
                        <i class="las la-store"></i> @lang('Brands')
                    </h5>
                    <div class="filter__widget-body fd-fcard__body">
                        <label class="fd-check" for="brand0">
                            <input class="form-check-input sortBrand" name="brand" type="checkbox" id="brand0" value="" checked>
                            <span class="fd-check__box"><i class="las la-check"></i></span>
                            <span class="fd-check__label">@lang('All Brands')</span>
                        </label>
                        @foreach ($brands as $brand)
                        <label class="fd-check" for="brand{{ $brand->id }}">
                            <input class="form-check-input sortBrand" type="checkbox" name="brand"
                                id="brand{{ $brand->id }}" value="{{ $brand->id }}">
                            <span class="fd-check__box"><i class="las la-check"></i></span>
                            <span class="fd-check__label">{{ __($brand->name) }}</span>
                            <span class="fd-check__count">{{ $brand->product_count }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </aside>
            @endif

            {{-- ---------------------------------------------- results --}}
            <div class="fd-shop__main">
                <div class="top__bar fd-toolbar">
                    {{-- NOTE: deliberately no #list-item/#box-item/#grid-item/#grid-4-item ids.
                         main.js binds those and rewrites the grid children to
                         "col-12 products-list", which would destroy .fd-plist. --}}
                    <ul class="top__bar-left fd-views" aria-label="@lang('View')">
                        <li data-view="list" title="@lang('List')"><i class="fas fa-th-list"></i></li>
                        <li data-view="2" title="@lang('Large grid')"><i class="fas fa-th-large"></i></li>
                        <li data-view="3" title="@lang('Grid')"><i class="fas fa-th"></i></li>
                        <li class="active" data-view="4" title="@lang('Compact grid')"><i class="fas fa-border-none"></i></li>
                    </ul>

                    <ul class="top__bar-right fd-toolbar__right">
                        <li>
                            <span class="fd-select">
                                <select class="sortProduct">
                                    <option value="" selected disabled>@lang('Sort By')</option>
                                    <option value="id_desc">@lang('Latest')</option>
                                    <option value="price_asc">@lang('Price: low to high')</option>
                                    <option value="price_desc">@lang('Price: high to low')</option>
                                </select>
                            </span>
                        </li>
                        <li>
                            <span class="fd-select">
                                <select class="productPaginate">
                                    <option value="" disabled>@lang('Select One')</option>
                                    <option value="5">@lang('5 per page')</option>
                                    <option value="10">@lang('10 per page')</option>
                                    <option value="20" selected>@lang('20 per page')</option>
                                    <option value="40">@lang('40 per page')</option>
                                    <option value="60">@lang('60 per page')</option>
                                    <option value="80">@lang('80 per page')</option>
                                    <option value="100">@lang('100 per page')</option>
                                </select>
                            </span>
                        </li>
                    </ul>

                    <button type="button" class="filter--bar fd-filterbtn d-lg-none">
                        <i class="las la-filter"></i> @lang('Filters')
                    </button>
                </div>

                {{-- view mode lives on the wrapper so the AJAX swap can't wipe it --}}
                <div class="fd-plist-wrap" data-view="4">
                    <div class="loader-wrapper fd-loading">
                        <div class="loader fd-spinner"></div>
                    </div>
                    <div id="products">
                        @include($activeTemplate.'products.show_products')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('style-lib')
<link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/custom.css') }}">
@endpush

@push('script')
<script>
    (function($){
    "use strict";
    getWishlistCount()
        let page = null;
        $('.loader-wrapper').addClass('d-none')
        $('.sortCategory, .sortBrand, .productPaginate, .sortProduct').on('change',function () {
            $('.loader-wrapper').removeClass('d-none');
            if($('#cate-0').is(':checked')){
                $("input[type='checkbox'][name='category']").not(this).prop('checked', false);
            }
            if($('#brand0').is(':checked')){
                $("input[type='checkbox'][name='brand']").not(this).prop('checked', false);
            }
            fetchProduct();
        });

        $("#slider-range").slider({
            range: true,
            min: {{ $minPrice }},
            max: {{ $maxPrice }},
            values: [{{ $minPrice }}, {{ $maxPrice }}],
            slide: function (event, ui) {
                $("#amount").val("{{$general->cur_sym}}" + ui.values[0] + " - {{$general->cur_sym}}" + ui.values[1]);
                $('input[name=min_price]').val(ui.values[ 0 ]);
                $('input[name=max_price]').val(ui.values[ 1 ]);
            },
            change: function(){
                $('.loader-wrapper').removeClass('d-none')
                fetchProduct();
            }
        });
        $("#amount").val("{{$general->cur_sym}}" + $("#slider-range").slider("values", 0) + " - {{$general->cur_sym}}" + $("#slider-range").slider("values", 1));

        function fetchProduct(){

            let data = {};
            data.min  = $('input[name="min_price"]').val();
            data.max  = $('input[name="max_price"]').val();
            data.sort = $('.sortProduct').find(":selected").val();
            data.paginate = $('.productPaginate').find(":selected").val();
            data.search = "{{ request()->search }}";
            data.route = "{{ request()->route()->getname() }}";

            data.categories = [];
            $.each($("[name=category]:checked"), function() {
                if($(this).val()){
                    data.categories.push($(this).val());
                }
            });

            data.brands = [];
            $.each($("[name=brand]:checked"), function() {
                if($(this).val()){
                    data.brands.push($(this).val());
                }
            });

            let url =  `{{ route('all.products.filter') }}`;
            if(page){
                url = `{{ route('all.products.filter') }}?page=${page}`;
            }

            $.ajax({
                method: "GET",
                url: url,
                data: data,
                success: function (response) {
                    getWishlistCount();
                    $('#products').html(response);
                }
            }).done(function(){
                $('.loader-wrapper').addClass('d-none')
            });
        }

        $(document).on('click', '.pagination a', function(event){
            event.preventDefault();
            page = $(this).attr('href').split('page=')[1];
            fetchProduct();
            $('html, body').animate({scrollTop: $('.fd-shop__main').offset().top - 100}, 350);
        });

        function getWishlistCount(){
            $.ajax({
               type: "GET",
               url: "{{ route('get-wishlist-count') }}",
               success: function (response) {
                    var total = Object.keys(response).length;
                    $.each(response, function (indexInArray, value) {
                        $(document).find(`[data-product_id='${value.product_id}']`).closest('.add-wishlist').addClass('active');
                    });
                   $('.show-wishlist-count').text(total);
               }
           });
        }

        /* ---------- view switcher (list / 2 / 3 / 4 up) ---------- */
        var wrap = $('.fd-plist-wrap');
        $('.fd-views li').on('click', function () {
            $('.fd-views li').removeClass('active');
            $(this).addClass('active');
            wrap.attr('data-view', $(this).data('view'));
            try { localStorage.setItem('fdShopView', $(this).data('view')); } catch (e) {}
        });
        try {
            var saved = localStorage.getItem('fdShopView');
            if (saved) {
                wrap.attr('data-view', saved);
                $('.fd-views li').removeClass('active').filter('[data-view="' + saved + '"]').addClass('active');
            }
        } catch (e) {}

        /* ---------- mobile filter drawer ---------- */
        $('.filter--bar').on('click', function () { $('.fd-filters').addClass('is-open'); $('body').addClass('fd-noscroll'); });
        $('.close--sidebar').on('click', function () { $('.fd-filters').removeClass('is-open'); $('body').removeClass('fd-noscroll'); });

    })(jQuery);
</script>
@endpush
