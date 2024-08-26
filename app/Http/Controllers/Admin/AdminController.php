<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\Withdrawal;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller {

    public function dashboard() {

        $pageTitle = 'Dashboard';

        // User Info
        $widget['total_users']            = User::count();
        $widget['verified_users']         = User::where('status', 1)->count();
        $widget['email_unverified_users'] = User::where('ev', 0)->count();
        $widget['sms_unverified_users']   = User::where('sv', 0)->count();

        // Monthly Deposit & Withdraw Report Graph
        $report['months']                = collect([]);
        $report['deposit_month_amount']  = collect([]);
        $report['withdraw_month_amount'] = collect([]);

        $depositsMonth = Deposit::where('created_at', '>=', Carbon::now()->subYear())
            ->where('status', 1)
            ->selectRaw("SUM( CASE WHEN status = 1 THEN amount END) as depositAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy('months')->get();

        $depositsMonth->map(function ($depositData) use ($report) {
            $report['months']->push($depositData->months);
            $report['deposit_month_amount']->push(showAmount($depositData->depositAmount));
        });
        $withdrawalMonth = Withdrawal::where('created_at', '>=', Carbon::now()->subYear())->where('status', 1)
            ->selectRaw("SUM( CASE WHEN status = 1 THEN amount END) as withdrawAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy('months')->get();
        $withdrawalMonth->map(function ($withdrawData) use ($report) {

            if (!in_array($withdrawData->months, $report['months']->toArray())) {
                $report['months']->push($withdrawData->months);
            }

            $report['withdraw_month_amount']->push(showAmount($withdrawData->withdrawAmount));
        });

        $months = $report['months'];

        for ($i = 0; $i < $months->count(); ++$i) {
            $monthVal = Carbon::parse($months[$i]);

            if (isset($months[$i + 1])) {
                $monthValNext = Carbon::parse($months[$i + 1]);

                if ($monthValNext < $monthVal) {
                    $temp           = $months[$i];
                    $months[$i]     = Carbon::parse($months[$i + 1])->format('F-Y');
                    $months[$i + 1] = Carbon::parse($temp)->format('F-Y');
                } else {
                    $months[$i] = Carbon::parse($months[$i])->format('F-Y');
                }

            }

        }

        // Withdraw Graph

        $deliveredOrders = Order::where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))->where('order_status', 3)
            ->selectRaw('sum(total) as totalAmount')
            ->selectRaw('DATE(created_at) day')
            ->groupBy('day')->get();

        $delivered['per_day']        = collect([]);
        $delivered['per_day_amount'] = collect([]);
        $deliveredOrders->map(function ($deliveredItem) use ($delivered) {
            $delivered['per_day']->push(date('d M', strtotime($deliveredItem->day)));
            $delivered['per_day_amount']->push($deliveredItem->totalAmount + 0);
        });

        // Deposit Graph
        $totalOrder = Order::where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))
            ->selectRaw('count(id) as totalOrder')
            ->selectRaw('DATE(created_at) day')
            ->groupBy('day')->get();
        $orders['per_day']        = collect([]);
        $orders['per_day_amount'] = collect([]);
        $totalOrder->map(function ($orderItem) use ($orders) {
            $orders['per_day']->push(date('d M', strtotime($orderItem->day)));
            $orders['per_day_amount']->push($orderItem->totalOrder + 0);
        });


        // user Browsing, Country, Operating Log
        $userLoginData = UserLogin::where('created_at', '>=', \Carbon\Carbon::now()->subDay(30))->get(['browser', 'os', 'country']);

        $chart['user_browser_counter'] = $userLoginData->groupBy('browser')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_os_counter'] = $userLoginData->groupBy('os')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_country_counter'] = $userLoginData->groupBy('country')->map(function ($item, $key) {
            return collect($item)->count();
        })->sort()->reverse()->take(5);

        $total['products'] = Product::count();
        $total['subscriber'] = Subscriber::count();

        $order['total_orders'] = Order::count();
        $order['completed_orders'] = Order::delivered()->count();
        $order['inCompleted_orders'] = Order::where('order_status','!=',3)->count();
        $order['total_sale_amount'] = Order::delivered()->sum('total');
        $order['pending_amount'] = Order::whereNotIn('order_status',[3,9])->sum('total');
        $order['cancel_amount'] = Order::cancel()->sum('total');

        $latestOrders = Order::latest()->take(7)->get();
        $emptyMessage = 'No order found';

        return view('admin.dashboard', compact('pageTitle', 'widget', 'report', 'delivered', 'chart', 'depositsMonth', 'withdrawalMonth', 'months', 'orders', 'total','order','latestOrders','emptyMessage'));
    }

    public function profile() {
        $pageTitle = 'Profile';
        $admin     = Auth::guard('admin')->user();
        return view('admin.profile', compact('pageTitle', 'admin'));
    }

    public function profileUpdate(Request $request) {
        $this->validate($request, [
            'name'  => 'required',
            'email' => 'required|email',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);
        $user = Auth::guard('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old         = $user->image ?: null;
                $user->image = uploadImage($request->image, imagePath()['profile']['admin']['path'], imagePath()['profile']['admin']['size'], $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }

        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Your profile has been updated.'];
        return redirect()->route('admin.profile')->withNotify($notify);
    }

    public function password() {
        $pageTitle = 'Password Setting';
        $admin     = Auth::guard('admin')->user();
        return view('admin.password', compact('pageTitle', 'admin'));
    }

    public function passwordUpdate(Request $request) {
        $this->validate($request, [
            'old_password' => 'required',
            'password'     => 'required|min:5|confirmed',
        ]);

        $user = Auth::guard('admin')->user();

        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password do not match !!'];
            return back()->withNotify($notify);
        }

        $user->password = bcrypt($request->password);
        $user->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return redirect()->route('admin.password')->withNotify($notify);
    }

    public function notifications() {
        $notifications = AdminNotification::orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        $pageTitle     = 'Notifications';
        return view('admin.notifications', compact('pageTitle', 'notifications'));
    }

    public function notificationRead($id) {
        $notification              = AdminNotification::findOrFail($id);
        $notification->read_status = 1;
        $notification->save();
        return redirect($notification->click_url);
    }

    public function requestReport() {
        $pageTitle            = 'Your Listed Report & Request';
        $arr['app_name']      = systemDetails()['name'];
        $arr['app_url']       = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASE_CODE');
        $url                  = "https://license.viserlab.com/issue/get?" . http_build_query($arr);
        $response             = json_decode(curlContent($url));

        if ($response->status == 'error') {
            return redirect()->route('admin.dashboard')->withErrors($response->message);
        }

        $reports = $response->message[0];
        return view('admin.reports', compact('reports', 'pageTitle'));
    }

    public function reportSubmit(Request $request) {
        $request->validate([
            'type'    => 'required|in:bug,feature',
            'message' => 'required',
        ]);
        $url = 'https://license.viserlab.com/issue/add';

        $arr['app_name']      = systemDetails()['name'];
        $arr['app_url']       = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASE_CODE');
        $arr['req_type']      = $request->type;
        $arr['message']       = $request->message;
        $response             = json_decode(curlPostContent($url, $arr));

        if ($response->status == 'error') {
            return back()->withErrors($response->message);
        }

        $notify[] = ['success', $response->message];
        return back()->withNotify($notify);
    }

    public function systemInfo() {
        $laravelVersion = app()->version();
        $serverDetails  = $_SERVER;
        $currentPHP     = phpversion();
        $timeZone       = config('app.timezone');
        $pageTitle      = 'System Information';
        return view('admin.info', compact('pageTitle', 'currentPHP', 'laravelVersion', 'serverDetails', 'timeZone'));
    }

    public function readAll() {
        AdminNotification::where('read_status', 0)->update([
            'read_status' => 1,
        ]);
        $notify[] = ['success', 'Notifications read successfully'];
        return back()->withNotify($notify);
    }

}
