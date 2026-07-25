<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;

/**
 * Ordering controls: accepting-orders switch, opening hours, and which
 * fulfilment methods (delivery / collection) are offered.
 */
class OrderSettingController extends Controller
{
    protected $days = [
        'mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday',
        'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday',
    ];

    public function index()
    {
        $pageTitle = 'Ordering & Opening Hours';
        $config    = orderConfig();
        $days      = $this->days;

        return view('admin.order_setting.index', compact('pageTitle', 'config', 'days'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'hours.*.open'  => 'nullable|date_format:H:i',
            'hours.*.close' => 'nullable|date_format:H:i',
        ]);

        $hours = [];
        foreach (array_keys($this->days) as $d) {
            $hours[$d] = [
                'closed' => isset($request->hours[$d]['closed']) ? 1 : 0,
                'open'   => $request->hours[$d]['open']  ?? '09:00',
                'close'  => $request->hours[$d]['close'] ?? '22:00',
            ];
        }

        $config = [
            'accepting_orders'   => $request->accepting_orders ? 1 : 0,
            'delivery_enabled'   => $request->delivery_enabled ? 1 : 0,
            'collection_enabled' => $request->collection_enabled ? 1 : 0,
            'hours'              => $hours,
        ];

        // at least one fulfilment method must stay on, or customers can't order
        if (!$config['delivery_enabled'] && !$config['collection_enabled']) {
            $notify[] = ['error', 'Enable at least one of Delivery or Collection.'];
            return back()->withNotify($notify);
        }

        $general = GeneralSetting::first();
        $general->order_config = $config;
        $general->save();

        $notify[] = ['success', 'Ordering settings updated.'];
        return back()->withNotify($notify);
    }
}
