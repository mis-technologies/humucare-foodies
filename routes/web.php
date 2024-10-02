<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
 */

Route::namespace ('Gateway')->prefix('ipn')->name('ipn.')->group(function () {
    Route::post('paypal', 'Paypal\ProcessController@ipn')->name('Paypal');
    Route::get('paypal-sdk', 'PaypalSdk\ProcessController@ipn')->name('PaypalSdk');
    Route::post('perfect-money', 'PerfectMoney\ProcessController@ipn')->name('PerfectMoney');
    Route::post('stripe', 'Stripe\ProcessController@ipn')->name('Stripe');
    Route::post('stripe-js', 'StripeJs\ProcessController@ipn')->name('StripeJs');
    Route::post('stripe-v3', 'StripeV3\ProcessController@ipn')->name('StripeV3');
    Route::post('skrill', 'Skrill\ProcessController@ipn')->name('Skrill');
    Route::post('paytm', 'Paytm\ProcessController@ipn')->name('Paytm');
    Route::post('payeer', 'Payeer\ProcessController@ipn')->name('Payeer');
    Route::post('paystack', 'Paystack\ProcessController@ipn')->name('Paystack');
    Route::post('voguepay', 'Voguepay\ProcessController@ipn')->name('Voguepay');
    Route::get('flutterwave/{trx}/{type}', 'Flutterwave\ProcessController@ipn')->name('Flutterwave');
    Route::post('razorpay', 'Razorpay\ProcessController@ipn')->name('Razorpay');
    Route::post('instamojo', 'Instamojo\ProcessController@ipn')->name('Instamojo');
    Route::get('blockchain', 'Blockchain\ProcessController@ipn')->name('Blockchain');
    Route::get('blockio', 'Blockio\ProcessController@ipn')->name('Blockio');
    Route::post('coinpayments', 'Coinpayments\ProcessController@ipn')->name('Coinpayments');
    Route::post('coinpayments-fiat', 'Coinpayments_fiat\ProcessController@ipn')->name('CoinpaymentsFiat');
    Route::post('coingate', 'Coingate\ProcessController@ipn')->name('Coingate');
    Route::post('coinbase-commerce', 'CoinbaseCommerce\ProcessController@ipn')->name('CoinbaseCommerce');
    Route::get('mollie', 'Mollie\ProcessController@ipn')->name('Mollie');
    Route::post('cashmaal', 'Cashmaal\ProcessController@ipn')->name('Cashmaal');
    Route::post('authorize-net', 'AuthorizeNet\ProcessController@ipn')->name('AuthorizeNet');
    Route::post('2check-out', 'TwoCheckOut\ProcessController@ipn')->name('TwoCheckOut');
    Route::post('mercado-pago', 'MercadoPago\ProcessController@ipn')->name('MercadoPago');
});

// User Support Ticket
Route::prefix('ticket')->group(function () {
    Route::get('/', 'TicketController@supportTicket')->name('ticket');
    Route::get('/new', 'TicketController@openSupportTicket')->name('ticket.open');
    Route::post('/create', 'TicketController@storeSupportTicket')->name('ticket.store');
    Route::get('/view/{ticket}', 'TicketController@viewTicket')->name('ticket.view');
    Route::post('/reply/{ticket}', 'TicketController@replyTicket')->name('ticket.reply');
    Route::get('/download/{ticket}', 'TicketController@ticketDownload')->name('ticket.download');
});

/*
|--------------------------------------------------------------------------
| Start Admin Area
|--------------------------------------------------------------------------
 */

Route::namespace ('Admin')->prefix('admin')->name('admin.')->group(function () {
    Route::namespace ('Auth')->group(function () {
        Route::get('/', 'LoginController@showLoginForm')->name('login');
        Route::post('/', 'LoginController@login')->name('login');
        Route::get('logout', 'LoginController@logout')->name('logout');
        // Admin Password Reset
        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'ForgotPasswordController@sendResetCodeEmail');
        Route::post('password/verify-code', 'ForgotPasswordController@verifyCode')->name('password.verify.code');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'ResetPasswordController@reset')->name('password.change');
    });

    Route::middleware('admin')->group(function () {
    // Route::middleware('admin')->group(function () {
        Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');
        Route::get('profile', 'AdminController@profile')->name('profile');
        Route::post('profile', 'AdminController@profileUpdate')->name('profile.update');
        Route::get('password', 'AdminController@password')->name('password');
        Route::post('password', 'AdminController@passwordUpdate')->name('password.update');

        //Notification
        Route::get('notifications', 'AdminController@notifications')->name('notifications');
        Route::get('notification/read/{id}', 'AdminController@notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'AdminController@readAll')->name('notifications.readAll');

        //Report Bugs
        Route::get('request-report', 'AdminController@requestReport')->name('request.report');
        Route::post('request-report', 'AdminController@reportSubmit');

        Route::get('system-info', 'AdminController@systemInfo')->name('system.info');

        // Users Manager
        Route::get('customers', 'ManageUsersController@allUsers')->name('users.all');
        Route::get('customers/active', 'ManageUsersController@activeUsers')->name('users.active');
        Route::get('customers/banned', 'ManageUsersController@bannedUsers')->name('users.banned');
        Route::get('customers/email-verified', 'ManageUsersController@emailVerifiedUsers')->name('users.email.verified');
        Route::get('customers/email-unverified', 'ManageUsersController@emailUnverifiedUsers')->name('users.email.unverified');
        Route::get('customers/sms-unverified', 'ManageUsersController@smsUnverifiedUsers')->name('users.sms.unverified');
        Route::get('customers/sms-verified', 'ManageUsersController@smsVerifiedUsers')->name('users.sms.verified');
        Route::get('customers/with-balance', 'ManageUsersController@usersWithBalance')->name('users.with.balance');

        Route::get('customers/{scope}/search', 'ManageUsersController@search')->name('users.search');
        Route::get('customer/detail/{id}', 'ManageUsersController@detail')->name('users.detail');
        Route::post('customer/update/{id}', 'ManageUsersController@update')->name('users.update');
        Route::post('customer/add-sub-balance/{id}', 'ManageUsersController@addSubBalance')->name('users.add.sub.balance');
        Route::get('customer/send-email/{id}', 'ManageUsersController@showEmailSingleForm')->name('users.email.single');
        Route::post('customer/send-email/{id}', 'ManageUsersController@sendEmailSingle')->name('users.email.single');
        Route::get('customer/login/{id}', 'ManageUsersController@login')->name('users.login');
        Route::get('customer/transactions/{id}', 'ManageUsersController@transactions')->name('users.transactions');
        Route::get('customer/deposits/{id}', 'ManageUsersController@deposits')->name('users.deposits');
        Route::get('customer/deposits/via/{method}/{type?}/{userId}', 'ManageUsersController@depositViaMethod')->name('users.deposits.method');
        Route::get('customer/withdrawals/{id}', 'ManageUsersController@withdrawals')->name('users.withdrawals');
        Route::get('customer/withdrawals/via/{method}/{type?}/{userId}', 'ManageUsersController@withdrawalsViaMethod')->name('users.withdrawals.method');
        // Login History
        Route::get('customers/login/history/{id}', 'ManageUsersController@userLoginHistory')->name('users.login.history.single');

        Route::get('customers/send-email', 'ManageUsersController@showEmailAllForm')->name('users.email.all');
        Route::post('customers/send-email', 'ManageUsersController@sendEmailAll')->name('users.email.send');
        Route::get('customers/email-log/{id}', 'ManageUsersController@emailLog')->name('users.email.log');
        Route::get('customers/email-details/{id}', 'ManageUsersController@emailDetails')->name('users.email.details');

        // Subscriber
        Route::get('subscriber', 'SubscriberController@index')->name('subscriber.index');
        Route::get('subscriber/send-email', 'SubscriberController@sendEmailForm')->name('subscriber.sendEmail');
        Route::post('subscriber/remove', 'SubscriberController@remove')->name('subscriber.remove');
        Route::post('subscriber/send-email', 'SubscriberController@sendEmail')->name('subscriber.sendEmail');

        // Payment
        Route::get('/payment/success', 'ManageUsersController@paymentSuccess')->name('payment.successful');

        // Brand
        Route::prefix('brand')->name('brand.')->group(function () {
            Route::get('/index', 'BrandController@index')->name('index');
            Route::post('/store/{id?}', 'BrandController@store')->name('store');
        });

        Route::prefix('category')->name('category.')->group(function () {
            Route::get('/index', 'CategoryController@index')->name('index');
            Route::post('/store/{id?}', 'CategoryController@store')->name('store');
        });

        Route::prefix('subcategory')->name('subcategory.')->group(function () {
            Route::get('/index', 'SubCategoryController@index')->name('index');
            Route::post('/store/{id?}', 'SubCategoryController@store')->name('store');
        });

        Route::prefix('product')->name('product.')->group(function () {
            Route::get('/index', 'ProductController@index')->name('index');
            Route::get('/create', 'ProductController@create')->name('create');
            Route::post('/store', 'ProductController@store')->name('store');
            Route::get('/edit/{id}', 'ProductController@edit')->name('edit');
            Route::post('/update/{id}', 'ProductController@update')->name('update');
            Route::get('digital/file/{id}', 'ProductController@digitalFileDownload')->name('digital.file.download');
            Route::get('/reviews/{id}','ProductController@reviews')->name('reviews');
            Route::post('/review/remove/{id}','ProductController@reviewRemove')->name('review.remove');
            Route::get('/today-deals/products',"ProductController@todayDeals")->name('todayDeal');
            Route::post('/today-deals/discount',"ProductController@todayDealsDiscount")->name('today.deal');
        });




        // coupon
        Route::prefix('coupon')->name('coupon.')->group(function(){
            Route::get('index', 'CouponController@index')->name('index');
            Route::post('store/{id?}', 'CouponController@store')->name('store');
        });

        // Deposit Gateway
        Route::name('gateway.')->prefix('gateway')->group(function () {
            // Automatic Gateway
            Route::get('automatic', 'GatewayController@index')->name('automatic.index');
            Route::get('automatic/edit/{alias}', 'GatewayController@edit')->name('automatic.edit');
            Route::post('automatic/update/{code}', 'GatewayController@update')->name('automatic.update');
            Route::post('automatic/remove/{code}', 'GatewayController@remove')->name('automatic.remove');
            Route::post('automatic/activate', 'GatewayController@activate')->name('automatic.activate');
            Route::post('automatic/deactivate', 'GatewayController@deactivate')->name('automatic.deactivate');

            // Manual Methods
            Route::get('manual', 'ManualGatewayController@index')->name('manual.index');
            Route::get('manual/new', 'ManualGatewayController@create')->name('manual.create');
            Route::post('manual/new', 'ManualGatewayController@store')->name('manual.store');
            Route::get('manual/edit/{alias}', 'ManualGatewayController@edit')->name('manual.edit');
            Route::post('manual/update/{id}', 'ManualGatewayController@update')->name('manual.update');
            Route::post('manual/activate', 'ManualGatewayController@activate')->name('manual.activate');
            Route::post('manual/deactivate', 'ManualGatewayController@deactivate')->name('manual.deactivate');
        });

        // DEPOSIT SYSTEM
        Route::name('deposit.')->prefix('deposit')->group(function () {
            Route::get('/', 'DepositController@deposit')->name('list');
            Route::get('pending', 'DepositController@pending')->name('pending');
            Route::get('rejected', 'DepositController@rejected')->name('rejected');
            Route::get('approved', 'DepositController@approved')->name('approved');
            Route::get('successful', 'DepositController@successful')->name('successful');
            Route::get('details/{id}', 'DepositController@details')->name('details');

            Route::post('reject', 'DepositController@reject')->name('reject');
            Route::post('approve', 'DepositController@approve')->name('approve');
            Route::get('via/{method}/{type?}', 'DepositController@depositViaMethod')->name('method');
            Route::get('/{scope}/search', 'DepositController@search')->name('search');
            Route::get('date-search/{scope}', 'DepositController@dateSearch')->name('dateSearch');
        });

        Route::name('orders.')->prefix('orders')->group(function () {
            Route::get('/all', 'ManageOrderController@allOrder')->name('all');
            Route::get('/pending', 'ManageOrderController@pendingOrder')->name('pending');
            Route::get('/confirmed', 'ManageOrderController@confirmOrder')->name('confirmed');
            Route::get('/shipped', 'ManageOrderController@shippedOrder')->name('shipped');
            Route::get('/delivered', 'ManageOrderController@deliveredOrder')->name('delivered');
            Route::get('/cancel', 'ManageOrderController@cancelOrder')->name('cancel');
            Route::get('/detail/{id}', 'ManageOrderController@orderDetail')->name('detail');
            Route::post('/status/{id}', 'ManageOrderController@orderStatus')->name('status');
            Route::get('/invoice/{id}', 'ManageOrderController@invoice')->name('invoice');
            Route::get('/user/{id}', 'ManageOrderController@userOrders')->name('user');
            Route::get('/user/transactions/{id}', 'ManageOrderController@userTransactions')->name('user.transactions');
        });
        Route::prefix('shipping')->group(function () {
            Route::get('/index', 'ShippingMethodController@index')->name('shipping.index');
            Route::post('/store/{id?}', 'ShippingMethodController@store')->name('shipping.store');
        });

        // WITHDRAW SYSTEM
        Route::name('withdraw.')->prefix('withdraw')->group(function () {
            Route::get('pending', 'WithdrawalController@pending')->name('pending');
            Route::get('approved', 'WithdrawalController@approved')->name('approved');
            Route::get('rejected', 'WithdrawalController@rejected')->name('rejected');
            Route::get('log', 'WithdrawalController@log')->name('log');
            Route::get('via/{method_id}/{type?}', 'WithdrawalController@logViaMethod')->name('method');
            Route::get('{scope}/search', 'WithdrawalController@search')->name('search');
            Route::get('date-search/{scope}', 'WithdrawalController@dateSearch')->name('dateSearch');
            Route::get('details/{id}', 'WithdrawalController@details')->name('details');
            Route::post('approve', 'WithdrawalController@approve')->name('approve');
            Route::post('reject', 'WithdrawalController@reject')->name('reject');

            // Withdraw Method
            Route::get('method/', 'WithdrawMethodController@methods')->name('method.index');
            Route::get('method/create', 'WithdrawMethodController@create')->name('method.create');
            Route::post('method/create', 'WithdrawMethodController@store')->name('method.store');
            Route::get('method/edit/{id}', 'WithdrawMethodController@edit')->name('method.edit');
            Route::post('method/edit/{id}', 'WithdrawMethodController@update')->name('method.update');
            Route::post('method/activate', 'WithdrawMethodController@activate')->name('method.activate');
            Route::post('method/deactivate', 'WithdrawMethodController@deactivate')->name('method.deactivate');
        });

        // Report
        Route::get('report/transaction', 'ReportController@transaction')->name('report.transaction');
        Route::get('report/transaction/search', 'ReportController@transactionSearch')->name('report.transaction.search');
        Route::get('report/login/history', 'ReportController@loginHistory')->name('report.login.history');
        Route::get('report/login/ipHistory/{ip}', 'ReportController@loginIpHistory')->name('report.login.ipHistory');
        Route::get('report/email/history', 'ReportController@emailHistory')->name('report.email.history');

        // Admin Support
        Route::get('tickets', 'SupportTicketController@tickets')->name('ticket');
        Route::get('tickets/pending', 'SupportTicketController@pendingTicket')->name('ticket.pending');
        Route::get('tickets/closed', 'SupportTicketController@closedTicket')->name('ticket.closed');
        Route::get('tickets/answered', 'SupportTicketController@answeredTicket')->name('ticket.answered');
        Route::get('tickets/view/{id}', 'SupportTicketController@ticketReply')->name('ticket.view');
        Route::post('ticket/reply/{id}', 'SupportTicketController@ticketReplySend')->name('ticket.reply');
        Route::get('ticket/download/{ticket}', 'SupportTicketController@ticketDownload')->name('ticket.download');
        Route::post('ticket/delete', 'SupportTicketController@ticketDelete')->name('ticket.delete');

        // Language Manager
        Route::get('/language', 'LanguageController@langManage')->name('language.manage');
        Route::post('/language', 'LanguageController@langStore')->name('language.manage.store');
        Route::post('/language/delete/{id}', 'LanguageController@langDel')->name('language.manage.del');
        Route::post('/language/update/{id}', 'LanguageController@langUpdate')->name('language.manage.update');
        Route::get('/language/edit/{id}', 'LanguageController@langEdit')->name('language.key');
        Route::post('/language/import', 'LanguageController@langImport')->name('language.importLang');

        Route::post('language/store/key/{id}', 'LanguageController@storeLanguageJson')->name('language.store.key');
        Route::post('language/delete/key/{id}', 'LanguageController@deleteLanguageJson')->name('language.delete.key');
        Route::post('language/update/key/{id}', 'LanguageController@updateLanguageJson')->name('language.update.key');

        // General Setting
        Route::get('general-setting', 'GeneralSettingController@index')->name('setting.index');
        Route::post('general-setting', 'GeneralSettingController@update')->name('setting.update');
        Route::get('optimize', 'GeneralSettingController@optimize')->name('setting.optimize');

        // Logo-Icon
        Route::get('setting/logo-icon', 'GeneralSettingController@logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'GeneralSettingController@logoIconUpdate')->name('setting.logo.icon');

        //Custom CSS
        Route::get('custom-css', 'GeneralSettingController@customCss')->name('setting.custom.css');
        Route::post('custom-css', 'GeneralSettingController@customCssSubmit');

        //Cookie
        Route::get('cookie', 'GeneralSettingController@cookie')->name('setting.cookie');
        Route::post('cookie', 'GeneralSettingController@cookieSubmit');

        // Plugin
        Route::get('extensions', 'ExtensionController@index')->name('extensions.index');
        Route::post('extensions/update/{id}', 'ExtensionController@update')->name('extensions.update');
        Route::post('extensions/activate', 'ExtensionController@activate')->name('extensions.activate');
        Route::post('extensions/deactivate', 'ExtensionController@deactivate')->name('extensions.deactivate');

        // Email Setting
        Route::get('email-template/global', 'EmailTemplateController@emailTemplate')->name('email.template.global');
        Route::post('email-template/global', 'EmailTemplateController@emailTemplateUpdate')->name('email.template.global');
        Route::get('email-template/setting', 'EmailTemplateController@emailSetting')->name('email.template.setting');
        Route::post('email-template/setting', 'EmailTemplateController@emailSettingUpdate')->name('email.template.setting');
        Route::get('email-template/index', 'EmailTemplateController@index')->name('email.template.index');
        Route::get('email-template/{id}/edit', 'EmailTemplateController@edit')->name('email.template.edit');
        Route::post('email-template/{id}/update', 'EmailTemplateController@update')->name('email.template.update');
        Route::post('email-template/send-test-mail', 'EmailTemplateController@sendTestMail')->name('email.template.test.mail');

        // SMS Setting
        Route::get('sms-template/global', 'SmsTemplateController@smsTemplate')->name('sms.template.global');
        Route::post('sms-template/global', 'SmsTemplateController@smsTemplateUpdate')->name('sms.template.global');
        Route::get('sms-template/setting', 'SmsTemplateController@smsSetting')->name('sms.templates.setting');
        Route::post('sms-template/setting', 'SmsTemplateController@smsSettingUpdate')->name('sms.template.setting');
        Route::get('sms-template/index', 'SmsTemplateController@index')->name('sms.template.index');
        Route::get('sms-template/edit/{id}', 'SmsTemplateController@edit')->name('sms.template.edit');
        Route::post('sms-template/update/{id}', 'SmsTemplateController@update')->name('sms.template.update');
        Route::post('email-template/send-test-sms', 'SmsTemplateController@sendTestSMS')->name('sms.template.test.sms');

        // order

        // SEO
        Route::get('seo', 'FrontendController@seoEdit')->name('seo');
        // Frontend
        Route::name('frontend.')->prefix('frontend')->group(function () {
            Route::get('templates', 'FrontendController@templates')->name('templates');
            Route::post('templates', 'FrontendController@templatesActive')->name('templates.active');

            Route::get('frontend-sections/{key}', 'FrontendController@frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'FrontendController@frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'FrontendController@frontendElement')->name('sections.element');
            Route::post('remove', 'FrontendController@remove')->name('remove');

            // Page Builder
            Route::get('manage-pages', 'PageBuilderController@managePages')->name('manage.pages');
            Route::post('manage-pages', 'PageBuilderController@managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'PageBuilderController@managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete', 'PageBuilderController@managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'PageBuilderController@manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'PageBuilderController@manageSectionUpdate')->name('manage.section.update');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Start User Area
|--------------------------------------------------------------------------
 */

Route::name('user.')->group(function () {
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\LoginController@login');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register')->middleware('regStatus');
    Route::post('check-mail', 'Auth\RegisterController@checkUser')->name('checkUser');

    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetCodeEmail')->name('password.email');
    Route::get('password/code-verify', 'Auth\ForgotPasswordController@codeVerify')->name('password.code.verify');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/verify-code', 'Auth\ForgotPasswordController@verifyCode')->name('password.verify.code');
});

Route::name('user.')->prefix('user')->group(function () {

    Route::middleware('auth')->group(function () {
        Route::get('authorization', 'AuthorizationController@authorizeForm')->name('authorization');
        Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
        Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
        Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

        Route::middleware(['checkStatus'])->group(function () {
            Route::get('dashboard', 'UserController@home')->name('home');

            Route::get('profile-setting', 'UserController@profile')->name('profile.setting');
            Route::post('profile-setting', 'UserController@submitProfile')->name('profile.update');
            Route::get('change-password', 'UserController@changePassword')->name('change.password');
            Route::post('change-password', 'UserController@submitPassword');

            Route::get('/lCheckout', 'CheckoutController@lCheckout')->name('l.checkout');

            Route::get('/order/detail/{id}', 'OrderController@orderDetail')->name('order.detail');
            Route::get('digital/file/download/{id}/{order_id}','OrderController@fileDownload')->name('digital.file.download');

            Route::get('review/products/','ReviewProductController@reviewProducts')->name('review.products');
            Route::get('review/create/{id}/{name}','ReviewProductController@reviewCreate')->name('review.create');
            Route::post('review/store/{id}','ReviewProductController@reviewStore')->name('review.store');


        });
    });
    // deposit

         Route::any('/payment', 'Gateway\PaymentController@deposit')->name('deposit');
            Route::post('deposit/insert', 'Gateway\PaymentController@depositInsert')->name('deposit.insert');
            Route::get('payment/preview', 'Gateway\PaymentController@depositPreview')->name('deposit.preview');
            Route::get('payment/confirm', 'Gateway\PaymentController@depositConfirm')->name('deposit.confirm');
            Route::get('payment/manual', 'Gateway\PaymentController@manualDepositConfirm')->name('deposit.manual.confirm');
            Route::post('deposit/manual', 'Gateway\PaymentController@manualDepositUpdate')->name('deposit.manual.update');
            Route::any('deposit/history', 'UserController@depositHistory')->name('deposit.history');

        Route::get('/shipping/method', 'CheckoutController@shippingMethod')->name('shipping.method');

        Route::get('/payment/history','UserController@paymentHistory')->name('payment.history');

        Route::get('/my/orders', 'OrderController@orderHistory')->name('order.history');

        Route::post('/checkout/order', 'CheckoutController@order')->name('checkout.order');

        Route::post('/checkout', 'CheckoutController@checkout')->name('checkout');
        Route::post('/milCheckout', 'CheckoutController@milCheckout')->name('mil.checkout');
        Route::get('/homeservice', 'CartController@alert')->name('alert');
});


Route::get('/contact', 'SiteController@contact')->name('contact');
Route::post('/contact', 'SiteController@contactSubmit');
Route::get('/change/{lang?}', 'SiteController@changeLanguage')->name('lang');
Route::get('page/{id}/{slug}', 'SiteController@pageDetails')->name('page.details');

Route::get('/cookie/accept', 'SiteController@cookieAccept')->name('cookie.accept');

Route::get('placeholder-image/{size}', 'SiteController@placeholderImage')->name('placeholder.image');

Route::get('/{slug}', 'SiteController@pages')->name('pages');
Route::get('/', 'SiteController@index')->name('home');
Route::get('page/{id}/{slug}', 'SiteController@pageDetails')->name('page.details');

Route::post('/subscribe/post', 'SiteController@subscribe')->name('subscribe.post');


Route::get('/all/categories', 'SiteController@allCategory')->name('all.category');

Route::get('/all/brands', 'SiteController@allBrands')->name('all.brands');

Route::get('/all/products', 'SiteController@products')->name('products');
Route::get('/home-service/view/[hs]','SiteController@homeService')->name('home-service');

Route::get('/hot_deals/products', 'SiteController@products')->name('hot_deals.products');

Route::get('/featured/products', 'SiteController@products')->name('featured.products');

Route::get('/best/selling/products', 'SiteController@products')->name('best-selling.products');

Route::get('/category/products/{id}/{name}', 'SiteController@categoryProducts')->name('category.products');

Route::get('/subcategory/products/{id}/{name}', 'SiteController@categoryProducts')->name('subcategory.products');

Route::get('/brand/products/{id}/{name}', 'SiteController@brandProducts')->name('brand.products');

Route::get('/all/product/filter', 'SiteController@productsFilter')->name('all.products.filter');

Route::get('product/detail/{id}/{name}', 'SiteController@productDetail')->name('product.detail');

Route::get('/track/order','SiteController@trackOrder')->name('track-order');
Route::post('get/track/order','SiteController@getTrackOrder')->name('get.track-order');

Route::get('/product/quickView', 'SiteController@quickView')->name('product.quickView');

Route::post('/add-to-cart', 'CartController@addToCart')->name('add-to-cart');
Route::post('/update-cart', 'CartController@updateCart')->name('update-cart');
Route::get('/delete/cart', 'CartController@deleteCart')->name('delete-cart');
Route::get('/get/cart/count', 'CartController@getCartCount')->name('get-cart-count');
Route::get('/cart/products', 'CartController@cartProducts')->name('cart');
Route::get('/coupon/apply', 'CartController@couponApply')->name('coupon-apply');

Route::get('/add/wishlist', 'WishlistController@addWishlist')->name('add-wishlist');
Route::get('/get/wishlist/count', 'WishlistController@getWishlistCount')->name('get-wishlist-count');
Route::get('/wishlist/products', 'WishlistController@wishlistProducts')->name('wishlist');
Route::get('/remove/wishlist', 'WishlistController@removeWishlist')->name('remove-wishlist');



