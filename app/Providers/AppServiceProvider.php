<?php

namespace App\Providers;

use App\Models\AdminNotification;
use App\Models\Category;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\Order;
use App\Models\Page;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        // $this->app['request']->server->set('HTTPS', true);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {

        // Force HTTPS URL generation so asset()/route()/url()/form actions never
        // emit http:// links that browsers block as mixed content on an HTTPS
        // site. ZERO-CONFIG and proxy-independent: it keys off the actual
        // request host (what asset() uses), not APP_URL and not
        // X-Forwarded-Proto. Only genuine local hosts stay on http.
        if (!app()->runningInConsole()) {
            $host      = request()->getHost();
            $appUrl    = (string) config('app.url');
            $looksLocal = str_contains($host, 'localhost')
                || str_contains($host, '127.0.0.1')
                || str_ends_with($host, '.test')
                || str_ends_with($host, '.local');

            if (str_starts_with($appUrl, 'https://') || !$looksLocal) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
                $this->app['request']->server->set('HTTPS', 'on');
            }
        }

        // The app is not installed until its core tables exist. Without this
        // guard every artisan command (including `migrate` itself) crashes on
        // a fresh database, making the app impossible to install.
        //
        // The try/catch also covers the DB being unreachable entirely — e.g.
        // during `docker build` (composer's package:discover) or before the
        // database container is up — so console commands never fatal.
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('general_settings')
                || !GeneralSetting::query()->exists()) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        $activeTemplate                  = activeTemplate();
        $general                         = GeneralSetting::first();
        $viewShare['general']            = $general;
        $viewShare['activeTemplate']     = $activeTemplate;
        $viewShare['activeTemplateTrue'] = activeTemplate(true);
        $viewShare['language']           = Language::all();
        $viewShare['pages']              = Page::where('tempname', $activeTemplate)->where('is_default', 0)->get();
        $viewShare['categories']         = Category::active()->with('subcategories', 'product')->get();
        view()->share($viewShare);

        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'banned_users_count'           => User::banned()->count(),
                'email_unverified_users_count' => User::emailUnverified()->count(),
                'sms_unverified_users_count'   => User::smsUnverified()->count(),
                'pending_ticket_count'         => SupportTicket::whereIN('status', [0, 2])->count(),
                'pending_deposits_count'       => Deposit::pending()->count(),
                'pending_withdraw_count'       => Withdrawal::pending()->count(),
                'pending_order_count'          => Order::pending()->count(),
            ]);
        });

        view()->composer('admin.partials.topnav', function ($view) {
            $view->with([
                'adminNotifications' => AdminNotification::where('read_status', 0)->with('user')->orderBy('id', 'desc')->get(),
            ]);
        });

        view()->composer('partials.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        if ($general->force_ssl) {
            \URL::forceScheme('https');
        }

        Paginator::useBootstrap();
    }

}
