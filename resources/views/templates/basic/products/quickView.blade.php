<div class="row">
    <div class="col-lg-5">
        <div class="sync1 owl-carousel owl-theme">
            <div class="thumbs">
                <img src="{{ getImage(imagePath()['product']['thumb']['path'].'/'. $product->image,imagePath()['product']['thumb']['size']) }}"
                    alt="product/details">
            </div>
            @foreach ($product->productGallery as $gallery)
            <div class="thumbs">
                <img src="{{ getImage(imagePath()['product']['gallery']['path'].'/'. $gallery->image,imagePath()['product']['gallery']['size']) }}"
                    alt="product/details">
            </div>
            @endforeach
        </div>
        <div class="sync2 owl-carousel owl-theme" id="sync2">
            <div class="thumbs">
                <img src="{{ getImage(imagePath()['product']['thumb']['path'].'/'. $product->image,imagePath()['product']['thumb']['size']) }}"
                    alt="product/details">
            </div>
            @foreach ($product->productGallery as $gallery)
            <div class="thumbs">
                <img src="{{ getImage(imagePath()['product']['gallery']['path'].'/'. $gallery->image,imagePath()['product']['gallery']['size']) }}" alt="product/details">
            </div>
            @endforeach
        </div>
    </div>
    @php
    $price = productPrice($product);
    @endphp
    <div class="col-lg-7">
        <div class="product-details-content">
            <h5 class="title">{{ __($product->name) }}</h5>
            <div class="price">
                <span class="text--base">{{ $general->cur_sym }}{{ showAmount($price) }}</span>
                @if ($product->discount != 0)
                <del class="text--danger">{{ $general->cur_sym }}{{ showAmount($product->price) }}</del>
                @elseif($product->today_deals == 1)
                <del class="text--danger">{{ $general->cur_sym }}{{ showAmount($product->price) }}</del>
                @endif
            </div>
            <div class="ratings-area">
                @if ($general->display_stock == 1)  
                <div class="{{ $product->quantity == 0 ? 'badge badge--danger' : 'badge badge--success' }} badge-sm badge-warning me-3">
                    {{$product->quantity == 0 ? 'Out of Stock' : 'In Stock' }}
                </div>
                @endif
                <div class="ratings">
                    @php
                        $star = showProductRatings($product->avg_rate);
                        echo $star;
                    @endphp
                </div>
                <span class="ms-2 me-auto">({{ $product->reviews->count() }})</span>
            </div>
            <p class="txt">
                {{ __($product->summary) }}
            </p>
            <div class="single-add-cart-area">
                <div class="cart-plus-minus">
                    <div class="cart-decrease qtybutton dec">
                        <i class="las la-minus"></i>
                    </div>
                    <input type="number" class="form-control productQuantity" name="quantity" value="1">
                    <div class="cart-increase qtybutton inc active">
                        <i class="las la-plus"></i>
                    </div>
                </div>
                @if ($general->display_stock == 1)  
                    <div class="quantity--amount">
                        (<span class="amount">{{ $product->quantity }}</span>)
                    </div>
                @endif
                <a href="#0" class="cmn--btn add-to-cart" data-product_id="{{ $product->id }}">@lang('Add To Cart')</a>
            </div>
            <div class="repeat--item">
                <ul class="lists">
                    <li class="mt-2">
                        <span class="name">@lang('Share')</span>
                        <ul class="social-icons">
                            <li>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                    title="@lang('Facebook')">
                                    <i class="lab la-facebook-f"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://twitter.com/intent/tweet?text={{ __($product->name) }}%0A{{ url()->current() }}"
                                    title="@lang('twitter')">
                                    <i class="lab la-twitter"></i>
                                </a>
                            </li>
                            <li>
                                <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{urlencode(url()->current()) }}&amp;title=my share text&amp;summary=dit is de linkedin summary"
                                    title="@lang('Linkedin')">
                                    <i class="lab la-linkedin-in"></i>
                                </a>
                            </li>
                            <li>
                                <a href="http://pinterest.com/pin/create/button/?url={{urlencode(url()->current()) }}&description={{ __($product->name) }}&media={{ getImage('assets/images/product/'. $product->image) }}"
                                    title="@lang('Pinterest')">
                                    <i class="lab la-pinterest"></i>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    "use script";
    var inStock = parseFloat($('.quantity--amount .amount').text());
    $(".qtybutton").on("click", function() {
      var $button = $(this);
      $button.parent().find('.qtybutton').removeClass('active')
      $button.addClass('active');
        var oldValue = $button.parent().find("input").val();
        var showInStock = $('.quantity--amount .amount');
        if ($button.hasClass('inc')) {
            var newVal = parseFloat(oldValue) + 1;
            showInStock.html(inStock - newVal);
        } else {
            if (oldValue > 1) {
                var newVal = parseFloat(oldValue) - 1;
                showInStock.html(inStock - newVal);
            } else {
                newVal = 1;
            }
        }
      $button.parent().find("input").val(newVal);
    });
    $('.qtybutton').on('click', function() {
        var qty = $('.quantity').val();
        var quantity = qty++;
        console.log(quantity);
        var price = $('.productPrice').text();
        var totalPrice = qty * parseInt(price);
        $('.totalprice').text(totalPrice);
        $('.totalprice').val(totalPrice);
    });
    $('.quantity').on('change', function() {
        var qty = $('.quantity').val();
        var price = $('.productPrice').text();
        var totalPrice = qty * parseInt(price);
        $('.totalprice').text(totalPrice);
        $('.totalprice').val(totalPrice);
    });
    
    // 
    var sync1 = $(".sync1");
    var sync2 = $(".sync2");
    var thumbnailItemClass = '.owl-item';
    var slides = sync1.owlCarousel({
      items: 1,
      loop: false,
      margin: 0,
      mouseDrag: true,
      touchDrag: true,
      pullDrag: false,
      scrollPerPage: true,
      nav: false,
      dots: false,
    }).on('changed.owl.carousel', syncPosition);

    function syncPosition(el) {
      $owl_slider = $(this).data('owl.carousel');
      var loop = $owl_slider.options.loop;

      if (loop) {
        var count = el.item.count - 1;
        var current = Math.round(el.item.index - (el.item.count / 2) - .5);
        if (current < 0) {
          current = count;
        }
        if (current > count) {
          current = 0;
        }
      } else {
        var current = el.item.index;
      }

      var owl_thumbnail = sync2.data('owl.carousel');
      var itemClass = "." + owl_thumbnail.options.itemClass;

      var thumbnailCurrentItem = sync2
        .find(itemClass)
        .removeClass("synced")
        .eq(current);
      thumbnailCurrentItem.addClass('synced');

      if (!thumbnailCurrentItem.hasClass('active')) {
        var duration = 500;
        sync2.trigger('to.owl.carousel', [current, duration, true]);
      }
    }
    var thumbs = sync2.owlCarousel({
        items: 3,
        loop: false,
        margin: 10,
        nav: false,
        dots: false,
        responsive:{
            500:{
                items: 4,
            },
            768:{
                items: 5,
            },
            992:{
                items: 4,
            },
            1200:{
                items: 5,
            },
        },
        onInitialized: function(e) {
          var thumbnailCurrentItem = $(e.target).find(thumbnailItemClass).eq(this._current);
          thumbnailCurrentItem.addClass('synced');
        },
      })
      .on('click', thumbnailItemClass, function(e) {
        e.preventDefault();
        var duration = 500;
        var itemIndex = $(e.target).parents(thumbnailItemClass).index();
        sync1.trigger('to.owl.carousel', [itemIndex, duration, true]);
      }).on("changed.owl.carousel", function(el) {
        var number = el.item.index;
        $owl_slider = sync1.data('owl.carousel');
        $owl_slider.to(number, 500, true);
    });
    sync1.owlCarousel();
</script>