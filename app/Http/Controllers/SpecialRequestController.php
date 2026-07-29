<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\SpecialRequest;
use Illuminate\Http\Request;

/**
 * Customer-facing "ask us for a special offer" enquiries — bulk portions,
 * party catering, office lunches. No payment is taken: the restaurant replies
 * with a quote from the admin panel.
 */
class SpecialRequestController extends Controller
{
    // declared rather than set dynamically — dynamic properties are deprecated
    // as of PHP 8.2 and this app runs on 8.3
    protected $activeTemplate;

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    /** Standalone page — the section on the home page posts to the same route. */
    public function index()
    {
        $pageTitle = 'Special Order Request';
        $types     = SpecialRequest::types();

        return view($this->activeTemplate . 'special_request', compact('pageTitle', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:120',
            'email'     => 'required|email|max:191',
            'phone'     => 'required|string|max:40',
            'item'      => 'required|string|max:191',
            'quantity'  => 'required|string|max:120',
            'type'      => 'required|in:' . implode(',', array_keys(SpecialRequest::types())),
            'needed_on' => 'nullable|date|after_or_equal:today',
            'people'    => 'nullable|integer|min:1|max:5000',
            'budget'    => 'nullable|numeric|min:0',
            'details'   => 'nullable|string|max:2000',
            // Bots fill every field they find; a real browser leaves this empty
            // because it is hidden. Cheap spam filter with no CAPTCHA friction.
            'website'   => 'max:0',
        ], [
            'website.max'          => 'Something went wrong. Please try again.',
            'needed_on.after_or_equal' => 'Please pick today or a future date.',
        ]);

        $req               = new SpecialRequest();
        $req->request_no   = 'SR' . getNumber(8);
        $req->user_id      = auth()->id() ?? 0;
        $req->name         = $request->name;
        $req->email        = $request->email;
        $req->phone        = $request->phone;
        $req->request_type = $request->type;
        $req->item         = $request->item;
        $req->quantity     = $request->quantity;
        $req->needed_on    = $request->needed_on ?: null;
        $req->people       = $request->people ?: null;
        $req->budget       = $request->budget ?: null;
        $req->details      = $request->details;
        $req->status       = SpecialRequest::STATUS_NEW;
        $req->save();

        $notification            = new AdminNotification();
        $notification->user_id   = auth()->id() ?? 0;
        $notification->title     = 'Special order request from ' . $req->name;
        $notification->click_url = urlPath('admin.special.request.index');
        $notification->save();

        $general = GeneralSetting::first();
        sendSpecialRequestEmail(trim($general->email_from ?? ''), 'ADMIN_SPECIAL_REQUEST', $req);
        sendSpecialRequestEmail($req->email, 'SPECIAL_REQUEST_RECEIVED', $req);

        $message = 'Thanks! Your request reference is ' . $req->request_no . '. We will contact you shortly with a price.';

        if ($request->ajax()) {
            return response()->json(['success' => $message, 'request_no' => $req->request_no]);
        }

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }
}
