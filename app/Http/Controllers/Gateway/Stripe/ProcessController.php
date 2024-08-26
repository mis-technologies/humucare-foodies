<?php

namespace App\Http\Controllers\Gateway\Stripe;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Token;

class ProcessController extends Controller {

    /*
     * Stripe Gateway
     */
    public static function process($deposit) {

        $alias = $deposit->gateway->alias;

        $send['track']  = $deposit->trx;
        $send['view']   = 'user.payment.' . $alias;
        $send['method'] = 'post';
        $send['url']    = route('ipn.' . $alias);
        return json_encode($send);
    }

    public function ipn(Request $request) {
        $track   = Session::get('Track');
        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

        if ($deposit->status == 1) {
            $notify[] = ['error', 'Invalid request.'];
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }

        $this->validate($request, [
            'cardNumber' => 'required',
            'cardExpiry' => 'required',
            'cardCVC'    => 'required',
        ]);

        $cc  = $request->cardNumber;
        $exp = $request->cardExpiry;
        $cvc = $request->cardCVC;

        $exp  = $pieces  = explode("/", $_POST['cardExpiry']);
        $emo  = trim($exp[0]);
        $eyr  = trim($exp[1]);
        $cnts = round($deposit->final_amo, 2) * 100;

        $stripeAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        Stripe::setApiKey($stripeAcc->secret_key);

        Stripe::setApiVersion("2020-03-02");

        try {
            $token = Token::create([
                "card" => [
                    "number"    => "$cc",
                    "exp_month" => $emo,
                    "exp_year"  => $eyr,
                    "cvc"       => "$cvc",
                ],
            ]);
            try {
                $charge = Charge::create([
                    'card'        => $token['id'],
                    'currency'    => $deposit->method_currency,
                    'amount'      => $cnts,
                    'description' => 'item',
                ]);

                if ($charge['status'] == 'succeeded') {
                    PaymentController::userDataUpdate($deposit->trx);
                    $notify[] = ['success', 'Payment captured successfully.'];
                    return redirect()->route(gatewayRedirectUrl(true))->withNotify($notify);
                }

            } catch (\Exception $e) {
                $notify[] = ['error', $e->getMessage()];
            }

        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
        }

        return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
    }

}
