@extends($activeTemplate.'layouts.frontend')
@section('content')
@include($activeTemplate.'sections.advertise')

<section class="products-section pt-60 pb-60">
    <div class="container">
        <div class="row">
            @if ($products->count() >0)
            <div class="col-lg-3">
                <aside class="filter--sidebar">
                    <div class="close--sidebar d-lg-none">
                        <i class="las la-times"></i>
                    </div>
                    @if($subcategories != null)
                    <div class="filter__widget">
                        <h5 class="filter__widget-title">@lang('Filter by categories')</h5>
                        <div class="filter__widget-body">
                            <div class="form-check form--check">
                                <input class="form-check-input sortCategory" name="subcategory" type="checkbox"
                                    id="cate-0" value="" checked>
                                <label class="form-check-label" for="cate-0">
                                    <span>@lang('All Categories')</span>
                                </label>
                            </div>
                            @foreach ($subcategories as $subcategory)
                            <div class="form-check form--check">
                                <input class="form-check-input sortCategory" type="checkbox" name="subcategory"
                                    id="cate{{ $subcategory->id }}" value="{{ $subcategory->id }}">
                                <label class="form-check-label" for="cate{{ $subcategory->id }}">
                                    <span>{{ __($subcategory->name) }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    <div class="filter__widget">
                        <h5 class="filter__widget-title">@lang('Filter by Price')</h5>
                        <div class="filter__widget-body">
                            <div class="filter-price-widget pt-2">
                                <div id="slider-range"></div>
                                <div class="price-range">
                                    <label for="amount">@lang('Price :')</label>
                                    <input type="text" id="amount" readonly>
                                    <input type="hidden" name="min_price" value="{{ getAmount($minPrice) }}">
                                    <input type="hidden" name="max_price" value="{{ getAmount($maxPrice) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="filter__widget">
                        <h5 class="filter__widget-title">@lang('Filter by brands')</h5>
                        <div class="filter__widget-body">
                            <div class="form-check form--check">
                                <input class="form-check-input sortBrand" name="brand" type="checkbox"
                                    id="brand0" value="" checked>
                                <label class="form-check-label" for="brand0">
                                    <span>@lang('All Brands')</span>
                                </label>
                            </div>
                            @foreach ($brands as $brand)
                            <div class="form-check form--check">
                                <input class="form-check-input sortBrand" name="brand" type="checkbox"
                                    id="brand{{ $brand->id }}" value="{{ $brand->id }}">
                                <label class="form-check-label" for="brand{{ $brand->id }}">
                                    <span>{{ __($brand->name) }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </aside>
            </div>
            @endif
            <div class="@if($products->count() >0) col-lg-9  @else col-lg-12 @endif">
                <div class="top__bar mb-3">
                    <ul class="top__bar-left">
                        <li id="list-item">
                            <i class="fas fa-th-list"></i>
                        </li>
                        <li id="box-item">
                            <i class="fas fa-th-large"></i>
                        </li>
                        <li id="grid-item">
                            <i class="fas fa-th"></i>
                        </li>
                        <li class="active" id="grid-4-item">
                            <i class="fas fa-border-none"></i>
                        </li>
                    </ul>
                    <ul class="top__bar-right">
                        <li>
                            <select class="sortProduct">
                                <option value="" selected disabled>@lang('Sort By')</option>
                                <option value="id_desc">@lang('Latest')</option>
                                <option value="price_asc">@lang('From low to high')</option>
                                <option value="price_desc">@lang('From high to low')</option>
                            </select>
                        </li>
                        <li>
                            <select class="productPaginate">
                                <option value="" selected disabled>@lang('Select One')</option>
                                <option value="3">@lang('3 items per page')</option>
                                <option value="10">@lang('10 items per page')</option>
                                <option value="20" selected>@lang('20 items per page')</option>
                                <option value="40">@lang('40 items per page')</option>
                                <option value="60">@lang('60 items per page')</option>
                                <option value="80">@lang('80 items per page')</option>
                                <option value="100">@lang('100 items per page')</option>
                            </select>
                        </li>
                    </ul>
                    <div class="filter--bar d-lg-none">
                        <i class="las la-filter"></i>
                    </div>
                </div>
                <div class="loader-wrapper">
                    <div class="loader"></div>
                </div>
                <div class="row justify-content-center g-3" id="products">
                    @include($activeTemplate.'products.show_products')
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
        getWishlistCount();
        let page = null;
        $('.loader-wrapper').addClass('d-none')
        $('.sortProduct, .sortBrand, .productPaginate, .sortCategory').on('change',function () { 
            $('.loader-wrapper').removeClass('d-none')
            if($('#cate-0').is(':checked')){
                $("[name=subcategory]").not(this).prop('checked', false);
            }
            if($('#brand0').is(':checked')){
                $("[name=brand]").not(this).prop('checked', false);
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

            let data        = {};
            data.min        = $('input[name="min_price"]').val();
            data.max        = $('input[name="max_price"]').val();
            data.paginate   = $('.productPaginate').find(":selected").val();
            data.sort       = $('.sortProduct').find(":selected").val();
            data.categoryId = "{{ $categoryId }}";
            data.subcategoryId = "{{ $subcategoryId }}";

            data.subcategories = [];
            $.each($("[name=subcategory]:checked"), function() {
                if($(this).val()){
                    data.subcategories.push($(this).val());
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

    })(jQuery);
</script>
@endpush