<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecialRequest;
use Illuminate\Http\Request;

/**
 * Inbox for customer special-order enquiries. The restaurant reads a request,
 * emails back a price, and moves it through New -> Quoted -> Confirmed.
 */
class SpecialRequestController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle    = 'Special Order Requests';
        $emptyMessage = 'No special requests yet.';

        $requests = SpecialRequest::query();

        if ($search = $request->search) {
            $requests->where(function ($q) use ($search) {
                $q->where('request_no', 'LIKE', "%$search%")
                    ->orWhere('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhere('phone', 'LIKE', "%$search%")
                    ->orWhere('item', 'LIKE', "%$search%");
            });
        }

        // `status` is a valid filter at 0 (New), so check presence, not truthiness
        if ($request->filled('status')) {
            $requests->where('status', (int) $request->status);
        }

        $requests = $requests->latest()->paginate(getPaginate());
        $statuses = SpecialRequest::statuses();

        return view('admin.special_request.index', compact('pageTitle', 'emptyMessage', 'requests', 'statuses'));
    }

    /** Send the customer a quote and mark the request as quoted. */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:2000',
        ]);

        $req = SpecialRequest::findOrFail($id);

        $req->admin_reply = $request->reply;
        $req->replied_at  = now();
        // Only advance a brand-new request; never drag a confirmed one backwards.
        if ($req->status == SpecialRequest::STATUS_NEW) {
            $req->status = SpecialRequest::STATUS_QUOTED;
        }
        $req->save();

        sendSpecialRequestEmail($req->email, 'SPECIAL_REQUEST_REPLY', $req, [
            'reply' => nl2br(e($request->reply)),
        ]);

        $notify[] = ['success', 'Your quote has been emailed to ' . $req->email];
        return back()->withNotify($notify);
    }

    public function status(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer|in:' . implode(',', array_keys(SpecialRequest::statuses())),
        ]);

        $req         = SpecialRequest::findOrFail($id);
        $req->status = (int) $request->status;
        $req->save();

        $notify[] = ['success', 'Request marked as ' . $req->status_name];
        return back()->withNotify($notify);
    }

    public function destroy($id)
    {
        SpecialRequest::findOrFail($id)->delete();

        $notify[] = ['success', 'Request deleted'];
        return back()->withNotify($notify);
    }
}
