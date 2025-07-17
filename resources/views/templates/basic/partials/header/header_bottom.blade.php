<div class="header-bottom bg--section py-2">
    <div class="container">
        <div class="header-wrapper">
            <div class="logo me-lg-4 me-auto">
                <a href="{{ route('home') }}">

                    <img style="height: 200px; width: 200px;"
                        src="{{ getImage(imagePath()['logoIcon']['path'] . '/foodies logo.png') }}" alt="@lang('logo')">
                    {{-- <h5 align="center">Shop</h5> --}}
                </a>

            </div>


            <form action="{{ route('products') }}" method="GET" class="search-form d-none d-lg-block">
                <div class="input-group search--group">
                    <input type="text" class="form-control" name="search" placeholder="@lang('Search here')"
                        value="{{ request()->search ?? null }}">
                    <button class="cmn--btn" type="submit">@lang('Search') </button>
                </div>
            </form>
            <div class="cart-wrapper d-flex flex-wrap  me-4 me-lg-0">
                <a href="{{ route('wishlist') }}" class="cart--btn">
                    <i class="far fa-heart"></i>
                    <span class="qty show-wishlist-count">0</span>
                </a>
                <a href="{{ route('cart') }}" class="cart--btn">
                    <i class="fas fa-cart-arrow-down"></i>
                    <span class="qty show-cart-count">0</span>
                </a>
            </div>
            <div class="header-bar d-lg-none">
                <span></span>
                <span></span>
                <span></span>
            </div>

        </div>
        <div id="allergy-popup"
            style="display: none; position: fixed; top: 20%; left: 50%; transform: translateX(-50%); z-index: 1000; background: white; border: 1px solid #ccc; box-shadow: 0 0 15px rgba(0,0,0,0.3); max-width: 600px; width: 90%;">
            <div class="row">
                <div class="p-5 col-lg-12">
                    <button id="close-popup"
                        style="position: absolute; top: 10px; right: 15px; color: red; background: transparent; border: none; font-size: 40px; cursor: pointer;">&times;</button>
                    <span style="font-size: 20px; font-weight: bold;">Food Allergies :</span><br>
                    Due to the nature of our business we cannot guarantee that food prepared on these premises is free
                    from allergenic ingredients.
                    This sign is being displayed in accordance with guidance from Birmingham City Council, Environmental
                    Health.
                </div>
            </div>
        </div>


    </div>
</div>

<script>
    window.addEventListener('load', function () {
        setTimeout(function () {
            document.getElementById('allergy-popup').style.display = 'block';
        }, 2000); // 10 seconds

        document.getElementById('close-popup').addEventListener('click', function () {
            document.getElementById('allergy-popup').style.display = 'none';
        });
    });
</script>
