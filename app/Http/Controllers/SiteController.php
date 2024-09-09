<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductPricePerLiter;
use App\Models\ProductPricePerMilliliter;
use App\Models\ProductPricePerVolume;
use App\Models\SubCategory;
use App\Models\Subscriber;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller {

    public function __construct() {
        $this->activeTemplate = activeTemplate();
    }

    public function index() {
        $count             = Page::where('tempname', $this->activeTemplate)->where('slug', 'home')->count();
        $pageTitle         = 'Home';
        $sections          = Page::where('tempname', $this->activeTemplate)->where('slug', 'home')->first();
        $todayDealProducts = Product::active()->where('today_deals', 1)->latest()->take(8)->get();
        return view($this->activeTemplate . 'home', compact('pageTitle', 'sections', 'todayDealProducts'));
    }

    public function pages($slug) {
        $page      = Page::where('tempname', $this->activeTemplate)->where('slug', $slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections  = $page->secs;
        return view($this->activeTemplate . 'pages', compact('pageTitle', 'sections'));
    }

    public function contact() {
        $pageTitle = "Contact Us";
        return view($this->activeTemplate . 'contact', compact('pageTitle'));
    }

    public function contactSubmit(Request $request) {

        $attachments = $request->file('attachments');
        $allowedExts = ['jpg', 'png', 'jpeg', 'pdf'];

        $this->validate($request, [
            'name'    => 'required|max:191',
            'email'   => 'required|max:191',
            'subject' => 'required|max:100',
            'message' => 'required',
        ]);

        $random = getNumber();

        $ticket           = new SupportTicket();
        $ticket->user_id  = auth()->id() ?? 0;
        $ticket->name     = $request->name;
        $ticket->email    = $request->email;
        $ticket->priority = 2;

        $ticket->ticket     = $random;
        $ticket->subject    = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status     = 0;
        $ticket->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title     = 'A new support ticket has opened ';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message                   = new SupportMessage();
        $message->supportticket_id = $ticket->id;
        $message->message          = $request->message;
        $message->save();

        $notify[] = ['success', 'ticket created successfully!'];

        return redirect()->route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function about() {
        $pageTitle = "About Us";
        $sections  = Page::where('tempname', $this->activeTemplate)->where('slug', 'about-us')->first();
        return view($this->activeTemplate . 'about', compact('pageTitle', 'sections'));
    }

    public function changeLanguage($lang = null) {
        $language = Language::where('code', $lang)->first();

        if (!$language) {
            $lang = 'en';
        }

        session()->put('lang', $lang);
        return redirect()->back();
    }

    public function cookieAccept() {
        session()->put('cookie_accepted', true);
        return response('Cookie accepted successfully');
    }

    public function placeholderImage($size = null) {
        $imgWidth  = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text      = $imgWidth . '×' . $imgHeight;
        $fontFile  = realpath('assets/font') . DIRECTORY_SEPARATOR . 'RobotoMono-Regular.ttf';
        $fontSize  = round(($imgWidth - 50) / 8);

        if ($fontSize <= 9) {
            $fontSize = 9;
        }

        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox    = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function pageDetails($id, $slug) {
        $page      = Frontend::where('id', $id)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle = $page->data_values->title;
        return view($this->activeTemplate . 'policy_pages', compact('page', 'pageTitle'));
    }

    public function subscribe(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $emailExist = Subscriber::where('email', $request->email)->first();

        if (!$emailExist) {
            $subscribe        = new Subscriber();
            $subscribe->email = $request->email;
            $subscribe->save();
            return response()->json(['success' => 'Subscribed Successfully']);
        } else {
            return response()->json(['error' => 'Already Subscribed']);
        }

    }

    public function allCategory() {
        $pageTitle    = 'All Categories';
        $emptyMessage = 'No category found';
        $categoryList = Category::where('status', 1)->orderBy('name')->paginate(getPaginate());
        return view($this->activeTemplate . 'all_category', compact('pageTitle', 'emptyMessage', 'categoryList'));
    }

    public function allBrands() {
        $pageTitle    = 'All Brands';
        $emptyMessage = 'No brand found';
        $brands       = Brand::where('status', 1)->orderBy('name')->paginate(getPaginate());
        return view($this->activeTemplate . 'all_brands', compact('pageTitle', 'emptyMessage', 'brands'));
    }

    public function products(Request $request) {

        $pageTitle    = 'All Products';
        $emptyMessage = 'No product found';

        $products = Product::active()->with('reviews');

        if ($request->route()->getName() == 'hot_deals.products') {
            $pageTitle = 'Hot Deal Products';
            $products  = $products->where('hot_deals', 1);
        }

        if ($request->route()->getName() == 'featured.products') {
            $pageTitle = 'Featured Products';
            $products  = $products->where('featured_product', 1);
        }

        if ($request->route()->getName() == 'best-selling.products') {
            $pageTitle = 'Best Selling Products';

            $products = $products->where('sale_count','!=',0)->orderBy('sale_count','desc');
        }

        if ($request->search) {
            $pageTitle     = 'Search Results';
            $searchKeyword = $request->search;
            $products      = $products->where(function ($q) use ($searchKeyword) {
                $q->orWhere('description', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('features', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('slug', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('summary', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('name', 'LIKE', '%' . $searchKeyword . '%')->orWhereHas('category', function ($category) use ($searchKeyword) {
                    $category->where('name', 'like', "%$searchKeyword%");
                })->orWhereHas('subcategory', function ($subcategory) use ($searchKeyword) {
                    $subcategory->where('name', 'like', "%$searchKeyword%");
                })->orWhereHas('brand', function ($brand) use ($searchKeyword) {
                    $brand->where('name', 'like', "%$searchKeyword%");
                });
            });
        }

        $cloneProducts = clone $products;
        $minPrice      = $cloneProducts->min('price') ?? 0;
        $maxPrice      = $cloneProducts->max('price') ?? 0;

        $categoryArray = [];
        $brandArray    = [];

        foreach ($products->get() as $product) {
            $categoryArray[] = $product->category_id;
            $brandArray[]    = $product->brand_id;
        }

        $categoryId = array_unique($categoryArray);
        $brandId    = array_unique($brandArray);


        $categoryList = Category::whereIn('id', $categoryId)->where('status', 1)->withCount('product')->get();
        $brands       = Brand::whereIn('id', $brandId)->where('status', 1)->withCount('product')->get();
        $products     = $products->latest()->paginate(getPaginate());

        return view($this->activeTemplate . 'products.all', compact('pageTitle', 'products', 'brands', 'minPrice', 'maxPrice', 'emptyMessage', 'categoryList'));
    }

    // public function homeService(Request $request) {

    //     $pageTitle    = 'All Products';
    //     $emptyMessage = 'No product found';

    //     $products = Product::active()->with('reviews');

    //     if ($request->route()->getName() == 'hot_deals.products') {
    //         $pageTitle = 'Hot Deal Products';
    //         $products  = $products->where('hot_deals', 1);
    //     }

    //     if ($request->route()->getName() == 'featured.products') {
    //         $pageTitle = 'Featured Products';
    //         $products  = $products->where('featured_product', 1);
    //     }

    //     if ($request->route()->getName() == 'best-selling.products') {
    //         $pageTitle = 'Best Selling Products';

    //         $products = $products->where('sale_count','!=',0)->orderBy('sale_count','desc');
    //     }

    //     if ($request->search) {
    //         $pageTitle     = 'Search Results';
    //         $searchKeyword = $request->search;
    //         $products      = $products->where(function ($q) use ($searchKeyword) {
    //             $q->orWhere('description', 'LIKE', '%' . $searchKeyword . '%')
    //                 ->orWhere('features', 'LIKE', '%' . $searchKeyword . '%')
    //                 ->orWhere('slug', 'LIKE', '%' . $searchKeyword . '%')
    //                 ->orWhere('summary', 'LIKE', '%' . $searchKeyword . '%')
    //                 ->orWhere('name', 'LIKE', '%' . $searchKeyword . '%')->orWhereHas('category', function ($category) use ($searchKeyword) {
    //                 $category->where('name', 'like', "%$searchKeyword%");
    //             })->orWhereHas('subcategory', function ($subcategory) use ($searchKeyword) {
    //                 $subcategory->where('name', 'like', "%$searchKeyword%");
    //             })->orWhereHas('brand', function ($brand) use ($searchKeyword) {
    //                 $brand->where('name', 'like', "%$searchKeyword%");
    //             });
    //         });
    //     }

    //     $cloneProducts = clone $products;
    //     $minPrice      = $cloneProducts->min('price') ?? 0;
    //     $maxPrice      = $cloneProducts->max('price') ?? 0;

    //     $categoryArray = [];
    //     $brandArray    = [];

    //     foreach ($products->get() as $product) {
    //         $categoryArray[] = $product->category_id;
    //         $brandArray[]    = $product->brand_id;
    //     }

    //     $categoryId = array_unique($categoryArray);
    //     $brandId    = array_unique($brandArray);


    //     $categoryList = Category::whereIn('id', $categoryId)->where('status', 1)->withCount('product')->get();
    //     $brands       = Brand::whereIn('id', $brandId)->where('status', 1)->withCount('product')->get();
    //     $products     = $products->latest()->paginate(getPaginate());

    //     return view($this->activeTemplate . 'products.all', compact('pageTitle', 'products', 'brands', 'minPrice', 'maxPrice', 'emptyMessage', 'categoryList'));
    // }

    public function productsFilter(Request $request) {

        $productList = Product::active()->with('reviews');

        if ($request->route == 'hot_deals.products') {
            $productList = $productList->where('hot_deals', 1);
        }

        if ($request->route == 'featured.products') {
            $productList = $productList->where('featured_product', 1);
        }

        if ($request->route == 'best-selling.products') {
            $productList = $productList->where('sale_count','!=',0)->orderBy('sale_count','desc');
        }

        if ($request->search) {
            $searchKeyword = $request->search;
            $productList   = $productList->where(function ($q) use ($searchKeyword) {
                $q->orWhere('description', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('features', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('slug', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('summary', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('name', 'LIKE', '%' . $searchKeyword . '%')->orWhereHas('category', function ($query) use ($searchKeyword) {
                    $query->where('name', 'like', "%$searchKeyword%");
                })->orWhereHas('subcategory', function ($query) use ($searchKeyword) {
                    $query->where('name', 'like', "%$searchKeyword%");
                })->orWhereHas('brand', function ($query) use ($searchKeyword) {
                    $query->where('name', 'like', "%$searchKeyword%");
                });
            });
        }


        if ($request->brandId) {
            $productList = $productList->where('brand_id', $request->brandId);
        }

        if ($request->categoryId) {

            if ($request->categoryId != 0) {
                $productList   = $productList->where('category_id', $request->categoryId);
                $productFilter = $this->subcategoriesQuery($productList, $request);
            }

        } else {
            $productFilter = $this->categoriesQuery($productList, $request);
        }

        if ($request->subcategoryId) {
            $productFilter = $productList->where('subcategory_id', $request->subcategoryId);
        }

        $productFilter = $this->productsQuery($productFilter, $request);

        if ($request->paginate == null) {
            $paginate = getPaginate();
        } else {
            $paginate = $request->paginate;
        }

        $emptyMessage = 'No product found';
        $products     = $productFilter->latest()->paginate($paginate);
        return view($this->activeTemplate . 'products.show_products', compact('products', 'emptyMessage'));

    }

    public function categoryProducts(Request $request, $id, $name) {

        $pageTitle    = $name . ' - Products';
        $emptyMessage = 'No product found';
        $products     = Product::active()->with('reviews');
        $categoryId    = 0;
        $subcategoryId = 0;

        if ($request->route()->getName() == 'category.products') {
            $categoryId    = $id;
            $products      = $products->where('category_id', $categoryId);
            $subcategories = SubCategory::where('category_id', $categoryId)->withCount('product')->where('status', 1)->get();
        }

        if ($request->route()->getName() == 'subcategory.products') {
            $subcategoryId = $id;
            $products      = $products->where('subcategory_id', $subcategoryId);
            $subcategories = null;
        }

        $cloneProducts = clone $products;

        $minPrice      = $cloneProducts->min('price') ?? 0;
        $maxPrice      = $cloneProducts->max('price') ?? 0;

        $brandArray = [];

        foreach ($products->get() as $product) {
            $brandArray[] = $product->brand_id;
        }

        $brandId  = array_unique($brandArray);
        $brands   = Brand::whereIn('id', $brandId)->where('status', 1)->withCount('product')->get();
        $products = $products->latest()->paginate(getPaginate());

        return view($this->activeTemplate . 'products.category_products', compact('pageTitle', 'emptyMessage', 'products', 'minPrice', 'maxPrice', 'subcategories', 'brands', 'categoryId', 'subcategoryId'));

    }

    public function brandProducts(Request $request, $id, $name) {

        $brandId      = $id;
        $pageTitle    = $name . ' - Products';
        $emptyMessage = 'No product found';
        $products     = Product::active()->with('reviews')->where('brand_id', $brandId);

        $cloneProducts = clone $products;
        $minPrice      = $cloneProducts->min('price') ?? 0;
        $maxPrice      = $cloneProducts->max('price') ?? 0;

        $categoryArray = [];

        foreach ($products->get() as $product) {
            $categoryArray[] = $product->category_id;
        }

        $categoryId   = array_unique($categoryArray);
        $categoryList = Category::whereIn('id', $categoryId)->where('status', 1)->withCount('product')->get();
        $products     = $products->latest()->paginate(getPaginate());

        return view($this->activeTemplate . 'products.brand_products', compact('pageTitle', 'emptyMessage', 'products', 'minPrice', 'maxPrice', 'categoryList', 'brandId'));
    }

    protected function categoriesQuery($productList, $request) {

        if ($request->categories) {
            $productList = $productList->whereIn('category_id', $request->categories);
        }

        return $productList;
    }

    protected function subcategoriesQuery($productList, $request) {

        if ($request->subcategories) {
            $productList = $productList->whereIn('subcategory_id', $request->subcategories);
        }

        return $productList;
    }

    protected function productsQuery($productFilter, $request) {
        if ($request->brands) {
            $productFilter = $productFilter->whereIn('brand_id', $request->brands);
        }

        if ($request->min && $request->max) {
            $productFilter = $productFilter->whereBetween('price', [$request->min, $request->max]);
        }

        if ($request->sort) {
            $sort          = explode('_', $request->sort);
            $productFilter = $productFilter->orderBy(@$sort[0], @$sort[1]);
        }

        return $productFilter;
    }

    public function quickView(Request $request) {
        $product = Product::active()->with('productGallery')->with('reviews')->findOrFail($request->product_id);
        return view($this->activeTemplate . 'products.quickView', compact('product'));
    }

    public function productDetail($id, $name) {
        $pageTitle    = 'Product Detail';
        $emptyMessage = 'There was no review found.';

        $product        = Product::active()->with('category', 'productGallery', 'reviews.user')->findOrFail($id);
        $relatedProduct = Product::active()->with('category', 'reviews')->where('id', '!=', $id)->where('category_id', $product->category_id)->take(4)->get();

        $productId = OrderDetail::with('order')->whereHas('order', function ($order) {
            $order->where('order_status', 3);
        })->groupBy('product_id')->selectRaw('*, sum(quantity) as sum')->orderBy('sum', 'desc')->distinct('product_id')->pluck('product_id');

        $product_price_per_liter = ProductPricePerLiter::whereProductId($id)->first();
        $product_price_per_milliliter = ProductPricePerMilliliter::whereProductId($id)->first();

        $price_range = ProductPricePerVolume::where('product_id', $product->id)->get();

        $topProducts = Product::active()->where('sale_count', '!=', 0)->orderBy('sale_count', 'desc')->latest()->with('reviews')->take(8)->get();
        $seoContents['image']               = getImage(imagePath()['product']['thumb']['path'] .'/'.$product->image, imagePath()['product']['thumb']['size']);
        $seoContents['image_size']          = imagePath()['product']['thumb']['size'];

        return view($this->activeTemplate . 'products.detail', compact('price_range','product_price_per_milliliter','product_price_per_liter','pageTitle', 'product', 'relatedProduct', 'topProducts', 'emptyMessage','seoContents'));
    }

    public function trackOrder() {
        $pageTitle = 'Track Your Order';
        return view($this->activeTemplate . 'track.track_order', compact('pageTitle'));
    }

    public function getTrackOrder(Request $request) {
        $validator = Validator::make($request->all(), [
            'orderNo' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $order = Order::where('order_no', $request->orderNo)->first();

        if (!$order) {
            return response()->json(['error' => 'Sorry! The order number was not found.']);
        }

        $emptyMessage = 'Your order has been cancelled.';
        return view($this->activeTemplate . 'track.show_track', compact('order', 'emptyMessage'));
    }

}
