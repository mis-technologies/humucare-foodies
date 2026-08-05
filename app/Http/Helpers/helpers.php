<?php

use App\Lib\GoogleAuthenticator;
use App\Lib\SendSms;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\SmsTemplate;
use Carbon\Carbon;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

function sidebarVariation() {

    /// for sidebar
    $variation['sidebar'] = 'bg--10';
    //for selector
    $variation['selector'] = 'capsule--rounded';
    //for overlay
    $variation['overlay'] = 'overlay--indigo';
    //Opacity
    $variation['opacity'] = 'overlay--opacity-9'; // 1-10

    return $variation;
}

function systemDetails() {
    $system['name']    = 'dealshop';
    $system['version'] = '1.0';
    return $system;
}

function getLatestVersion() {
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website']      = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url                   = 'https://license.viserlab.com/updates/version/' . systemDetails()['name'];
    $result                = curlPostContent($url, $param);

    if ($result) {
        return $result;
    } else {
        return null;
    }

}

function slug($string) {
    return Illuminate\Support\Str::slug($string);
}

function shortDescription($string, $length = 120) {
    return Illuminate\Support\Str::limit($string, $length);
}

function shortCodeReplacer($shortCode, $replace_with, $template_string) {
    return str_replace($shortCode, $replace_with, $template_string);
}

function verificationCode($length) {

    if ($length == 0) {
        return 0;
    }

    $min = pow(10, $length - 1);
    $max = 0;

    while ($length > 0 && $length--) {
        $max = ($max * 10) + 9;
    }

    return random_int($min, $max);
}

function getNumber($length = 8) {
    $characters       = '1234567890';
    $charactersLength = strlen($characters);
    $randomString     = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

/** True when uploads should go to S3 rather than the local public/ disk. */
function usesS3Storage() {
    return config('filesystems.default') === 's3';
}

//moveable
function uploadImage($file, $location, $size = null, $old = null, $thumb = null) {
    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
    $ext      = strtolower($file->getClientOriginalExtension());

    // ---------- S3 ----------
    if (usesS3Storage()) {
        if ($old) {
            removeFile($location . '/' . $old);
            removeFile($location . '/thumb_' . $old);
        }
        $image = Image::make($file);
        if ($size) {
            $size = explode('x', strtolower($size));
            $image->resize($size[0], $size[1]);
        }
        // no ACL: the bucket has ACLs disabled and serves via bucket policy
        Storage::disk('s3')->put($location . '/' . $filename, (string) $image->encode($ext));

        if ($thumb) {
            $thumb = explode('x', $thumb);
            $t = Image::make($file)->resize($thumb[0], $thumb[1]);
            Storage::disk('s3')->put($location . '/thumb_' . $filename, (string) $t->encode($ext));
        }
        return $filename;
    }

    // ---------- local disk ----------
    $path = makeDirectory($location);
    if (!$path) {
        throw new Exception('File could not been created.');
    }
    if ($old) {
        removeFile($location . '/' . $old);
        removeFile($location . '/thumb_' . $old);
    }
    $image = Image::make($file);
    if ($size) {
        $size = explode('x', strtolower($size));
        $image->resize($size[0], $size[1]);
    }
    $image->save($location . '/' . $filename);
    if ($thumb) {
        $thumb = explode('x', $thumb);
        Image::make($file)->resize($thumb[0], $thumb[1])->save($location . '/thumb_' . $filename);
    }
    return $filename;
}

function uploadFile($file, $location, $size = null, $old = null) {
    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();

    if (usesS3Storage()) {
        if ($old) {
            removeFile($location . '/' . $old);
        }
        Storage::disk('s3')->put($location . '/' . $filename, file_get_contents($file->getRealPath()));
        return $filename;
    }

    $path = makeDirectory($location);
    if (!$path) {
        throw new Exception('File could not been created.');
    }
    if ($old) {
        removeFile($location . '/' . $old);
    }
    $file->move($location, $filename);
    return $filename;
}

function makeDirectory($path) {

    if (file_exists($path)) {
        return true;
    }

    return mkdir($path, 0755, true);
}

function removeFile($path) {
    if (usesS3Storage()) {
        try {
            if (Storage::disk('s3')->exists($path)) {
                return Storage::disk('s3')->delete($path);
            }
        } catch (\Throwable $e) {
            // deleting a missing object must never break the caller
        }
        return false;
    }
    return file_exists($path) && is_file($path) ? @unlink($path) : false;
}

function activeTemplate($asset = false) {
    $general  = GeneralSetting::first(['active_template']);
    // Degrade to the default template instead of a fatal when settings are
    // missing (fresh install, or mid-seed when the table is momentarily empty).
    $template = optional($general)->active_template ?: 'basic';
    $sess     = session()->get('template');

    if (trim($sess)) {
        $template = $sess;
    }

    if ($asset) {
        return 'assets/templates/' . $template . '/';
    }

    return 'templates.' . $template . '.';
}

function activeTemplateName() {
    $general  = GeneralSetting::first(['active_template']);
    $template = optional($general)->active_template ?: 'basic';
    $sess     = session()->get('template');

    if (trim($sess)) {
        $template = $sess;
    }

    return $template;
}

function loadReCaptcha() {
    $reCaptcha = Extension::where('act', 'google-recaptcha2')->where('status', 1)->first();
    return $reCaptcha ? $reCaptcha->generateScript() : '';
}

function loadAnalytics() {
    $analytics = Extension::where('act', 'google-analytics')->where('status', 1)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function loadTawkto() {
    $tawkto = Extension::where('act', 'tawk-chat')->where('status', 1)->first();
    return $tawkto ? $tawkto->generateScript() : '';
}

function loadFbComment() {
    $comment = Extension::where('act', 'fb-comment')->where('status', 1)->first();
    return $comment ? $comment->generateScript() : '';
}

function loadCustomCaptcha($height = 46, $width = '100%', $bgcolor = '#003', $textcolor = '#abc') {
    $textcolor = '#' . GeneralSetting::first()->base_color;
    $captcha   = Extension::where('act', 'custom-captcha')->where('status', 1)->first();

    if (!$captcha) {
        return 0;
    }

    $code = rand(100000, 999999);
    $char = str_split($code);
    $ret  = '<link href="https://fonts.googleapis.com/css?family=Henny+Penny&display=swap" rel="stylesheet">';
    $ret .= '<div style="height: ' . $height . 'px; line-height: ' . $height . 'px; width:' . $width . '; text-align: center; background-color: ' . $bgcolor . '; color: ' . $textcolor . '; font-size: ' . ($height - 20) . 'px; font-weight: bold; letter-spacing: 20px; font-family: \'Henny Penny\', cursive;  -webkit-user-select: none; -moz-user-select: none;-ms-user-select: none;user-select: none;  display: flex; justify-content: center;">';

    foreach ($char as $value) {
        $ret .= '<span style="    float:left;     -webkit-transform: rotate(' . rand(-60, 60) . 'deg);">' . $value . '</span>';
    }

    $ret .= '</div>';
    $captchaSecret = hash_hmac('sha256', $code, $captcha->shortcode->random_key->value);
    $ret .= '<input type="hidden" name="captcha_secret" value="' . $captchaSecret . '">';
    return $ret;
}

function captchaVerify($code, $secret) {
    $captcha       = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
    $captchaSecret = hash_hmac('sha256', $code, $captcha->shortcode->random_key->value);

    if ($captchaSecret == $secret) {
        return true;
    }

    return false;
}

function getTrx($length = 12) {
    $characters       = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString     = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function getAmount($amount, $length = 2) {
    $amount = round($amount, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false) {
    $separator = '';

    if ($separate) {
        $separator = ',';
    }

    $printAmount = number_format($amount, $decimal, '.', $separator);

    if ($exceptZeros) {
        $exp = explode('.', $printAmount);

        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        }

    }

    return $printAmount;
}

function removeElement($array, $value) {
    return array_diff($array, (is_array($value) ? $value : [$value]));
}

function cryptoQR($wallet) {

    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}

//moveable
function curlContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

//moveable
function curlPostContent($url, $arr = null) {

    if ($arr) {
        $params = http_build_query($arr);
    } else {
        $params = '';
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function inputTitle($text) {
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}

function titleToKey($text) {
    return strtolower(str_replace(' ', '_', $text));
}

function str_limit($title = null, $length = 10) {
    return \Illuminate\Support\Str::limit($title, $length);
}

//moveable
/**
 * Remember which paginated admin list is on screen, so that saving an edit can
 * return the admin to that page (and search term) instead of dumping them back
 * on page 1 — which meant re-navigating after every single edit.
 *
 * Call rememberListUrl() in the list action and listUrl() when redirecting.
 */
function rememberListUrl($key) {
    if (request()->isMethod('get')) {
        session(['admin_list_url.' . $key => request()->fullUrl()]);
    }
}

function listUrl($key, $fallback) {
    return session('admin_list_url.' . $key, $fallback);
}

/**
 * Flatten one field of getIpInfo() into a string for the user_logins table.
 *
 * The shipped code did `@implode(',', $info['city'])`. geoplugin returns
 * scalars, and in PHP 8 implode() THROWS a TypeError on a string — `@` only
 * suppresses diagnostics, never thrown errors. On PHP 8.3 (what the Docker
 * image runs) that made every login and registration from a new IP a 500.
 * Accepts a string, an array, null or a missing key and always returns a string.
 */
function geoValue($value) {
    if (is_array($value)) {
        return implode(',', array_filter($value, 'is_scalar'));
    }

    return is_scalar($value) ? (string) $value : '';
}

function getIpInfo() {
    $ip = $_SERVER["REMOTE_ADDR"];

//Deep detect ip
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }

    $xml = @simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $ip);

    $country = @$xml->geoplugin_countryName;
    $city    = @$xml->geoplugin_city;
    $area    = @$xml->geoplugin_areaCode;
    $code    = @$xml->geoplugin_countryCode;
    $long    = @$xml->geoplugin_longitude;
    $lat     = @$xml->geoplugin_latitude;

    $data['country'] = $country;
    $data['city']    = $city;
    $data['area']    = $area;
    $data['code']    = $code;
    $data['long']    = $long;
    $data['lat']     = $lat;
    $data['ip']      = request()->ip();
    $data['time']    = date('d-m-Y h:i:s A');

    return $data;
}

//moveable
function osBrowser() {
    $userAgent  = $_SERVER['HTTP_USER_AGENT'];
    $osPlatform = "Unknown OS Platform";
    $osArray    = [
        '/windows nt 10/i'      => 'Windows 10',
        '/windows nt 6.3/i'     => 'Windows 8.1',
        '/windows nt 6.2/i'     => 'Windows 8',
        '/windows nt 6.1/i'     => 'Windows 7',
        '/windows nt 6.0/i'     => 'Windows Vista',
        '/windows nt 5.2/i'     => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     => 'Windows XP',
        '/windows xp/i'         => 'Windows XP',
        '/windows nt 5.0/i'     => 'Windows 2000',
        '/windows me/i'         => 'Windows ME',
        '/win98/i'              => 'Windows 98',
        '/win95/i'              => 'Windows 95',
        '/win16/i'              => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i'        => 'Mac OS 9',
        '/linux/i'              => 'Linux',
        '/ubuntu/i'             => 'Ubuntu',
        '/iphone/i'             => 'iPhone',
        '/ipod/i'               => 'iPod',
        '/ipad/i'               => 'iPad',
        '/android/i'            => 'Android',
        '/blackberry/i'         => 'BlackBerry',
        '/webos/i'              => 'Mobile',
    ];

    foreach ($osArray as $regex => $value) {

        if (preg_match($regex, $userAgent)) {
            $osPlatform = $value;
        }

    }

    $browser      = "Unknown Browser";
    $browserArray = [
        '/msie/i'      => 'Internet Explorer',
        '/firefox/i'   => 'Firefox',
        '/safari/i'    => 'Safari',
        '/chrome/i'    => 'Chrome',
        '/edge/i'      => 'Edge',
        '/opera/i'     => 'Opera',
        '/netscape/i'  => 'Netscape',
        '/maxthon/i'   => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i'    => 'Handheld Browser',
    ];

    foreach ($browserArray as $regex => $value) {

        if (preg_match($regex, $userAgent)) {
            $browser = $value;
        }

    }

    $data['os_platform'] = $osPlatform;
    $data['browser']     = $browser;

    return $data;
}

function siteName() {
    $general    = GeneralSetting::first();
    $sitname    = str_word_count($general->sitename);
    $sitnameArr = explode(' ', $general->sitename);

    if ($sitname > 1) {
        $title = "<span>$sitnameArr[0] </span> " . str_replace($sitnameArr[0], '', $general->sitename);
    } else {
        $title = "<span>$general->sitename</span>";
    }

    return $title;
}

//moveable
function getTemplates() {
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website']      = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url                   = 'https://license.viserlab.com/updates/templates/' . systemDetails()['name'];
    $result                = curlPostContent($url, $param);

    if ($result) {
        return $result;
    } else {
        return null;
    }

}

function getPageSections($arr = false) {

    $jsonUrl  = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));

    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }

    return $sections;
}

function getImage($image, $size = null, $isAvatar = false) {
    // 1) local file (repo static assets + any legacy local uploads).
    //    public_path() makes this independent of the process CWD.
    if ($image && file_exists(public_path($image)) && is_file(public_path($image))) {
        return asset($image);
    }

    // 2) uploaded to S3. Only build a URL for a real filename (has an
    //    extension) — a bare directory path means "no image set" and should
    //    fall through to the placeholder. No per-image S3 API call is made.
    if (usesS3Storage()) {
        $base = $image ? basename($image) : '';
        $s3Url = config('filesystems.disks.s3.url');
        if ($base !== '' && strpos($base, '.') !== false && $s3Url) {
            return rtrim($s3Url, '/') . '/' . ltrim($image, '/');
        }
    }

    // 3) fallbacks
    if ($isAvatar) {
        return asset('assets/images/avatar.jpg');
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}

function notify($user, $type, $shortCodes = null) {
    sendEmail($user, $type, $shortCodes);
    sendSms($user, $type, $shortCodes);
}

function sendSms($user, $type, $shortCodes = []) {
    $general     = GeneralSetting::first();
    $smsTemplate = SmsTemplate::where('act', $type)->where('sms_status', 1)->first();
    $gateway     = $general->sms_config->name;
    $sendSms     = new SendSms;

    if ($general->sn == 1 && $smsTemplate) {
        $template = $smsTemplate->sms_body;

        foreach ($shortCodes as $code => $value) {
            $template = shortCodeReplacer('{{' . $code . '}}', $value, $template);
        }

        $message = shortCodeReplacer("{{message}}", $template, $general->sms_api);
        $message = shortCodeReplacer("{{name}}", $user->username, $message);
        $sendSms->$gateway($user->mobile, $general->sitename, $message, $general->sms_config);
    }

}

function sendEmail($user, $type = null, $shortCodes = []) {
    $general = GeneralSetting::first();

    $emailTemplate = EmailTemplate::where('act', $type)->where('email_status', 1)->first();

    if ($general->en != 1 || !$emailTemplate) {
        return;
    }

    // Without a From address PHPMailer throws "Invalid address: (From)" from
    // deep inside the transport, which callers then have to catch. sendGeneralEmail()
    // already skips in this case; do the same here so an unconfigured mailer is a
    // logged no-op rather than an exception surfacing in checkout.
    if (!trim($general->email_from ?: '')) {
        \Illuminate\Support\Facades\Log::warning(
            'Email not sent (' . $type . '): "Email From" is not set in General Settings.'
        );
        return;
    }

    $message = shortCodeReplacer("{{fullname}}", $user->fullname, $general->email_template);
    $message = shortCodeReplacer("{{username}}", $user->username, $message);
    $message = shortCodeReplacer("{{message}}", $emailTemplate->email_body, $message);

    if (empty($message)) {
        $message = $emailTemplate->email_body;
    }

    // The subject needs the same treatment as the body. Without this the
    // customer received a literal "Your Foodies order {{order_no}} is confirmed".
    $subject = $emailTemplate->subj;

    foreach ($shortCodes as $code => $value) {
        $message = shortCodeReplacer('{{' . $code . '}}', $value, $message);
        $subject = shortCodeReplacer('{{' . $code . '}}', $value, $subject);
    }

    $config = $general->mail_config;

    $emailLog              = new EmailLog();
    $emailLog->user_id     = $user->id;
    $emailLog->mail_sender = $config->name;
    $emailLog->email_from  = $general->sitename . ' ' . $general->email_from;
    $emailLog->email_to    = $user->email;
    $emailLog->subject     = $subject;
    $emailLog->message     = $message;
    $emailLog->save();

    if ($config->name == 'php') {
        sendPhpMail($user->email, $user->username, $subject, $message, $general);
    } else

    if ($config->name == 'smtp') {
        sendSmtpMail($config, $user->email, $user->username, $subject, $message, $general);
    } else

    if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $user->email, $user->username, $subject, $message, $general);
    } else

    if ($config->name == 'mailjet') {
        sendMailjetMail($config, $user->email, $user->username, $subject, $message, $general);
    }

}

function sendPhpMail($receiver_email, $receiver_name, $subject, $message, $general) {
    $headers = "From: $general->sitename <$general->email_from> \r\n";
    $headers .= "Reply-To: $general->sitename <$general->email_from> \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    @mail($receiver_email, $subject, $message, $headers);
}

function sendSmtpMail($config, $receiver_email, $receiver_name, $subject, $message, $general) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host     = $config->host;
        $mail->SMTPAuth = true;
        $mail->Username = $config->username;
        $mail->Password = $config->password;

        if ($config->enc == 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->Port    = $config->port;
        $mail->CharSet = 'UTF-8';
        //Recipients
        $mail->setFrom($general->email_from, $general->sitename);
        $mail->addAddress($receiver_email, $receiver_name);
        $mail->addReplyTo($general->email_from, $general->sitename);
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();
    } catch (Exception $e) {
        throw new Exception($e);
    }

}

function sendSendGridMail($config, $receiver_email, $receiver_name, $subject, $message, $general) {
    $sendgridMail = new \SendGrid\Mail\Mail();
    $sendgridMail->setFrom($general->email_from, $general->sitename);
    $sendgridMail->setSubject($subject);
    $sendgridMail->addTo($receiver_email, $receiver_name);
    $sendgridMail->addContent("text/html", $message);
    $sendgrid = new \SendGrid($config->appkey);
    try {
        $response = $sendgrid->send($sendgridMail);
    } catch (Exception $e) {
        throw new Exception($e);
    }

}

function sendMailjetMail($config, $receiver_email, $receiver_name, $subject, $message, $general) {
    $mj   = new \Mailjet\Client($config->public_key, $config->secret_key, true, ['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From'     => [
                    'Email' => $general->email_from,
                    'Name'  => $general->sitename,
                ],
                'To'       => [
                    [
                        'Email' => $receiver_email,
                        'Name'  => $receiver_name,
                    ],
                ],
                'Subject'  => $subject,
                'TextPart' => "",
                'HTMLPart' => $message,
            ],
        ],
    ];
    $response = $mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
}

function getPaginate($paginate = 20) {
    return $paginate;
}

function paginateLinks($data, $design = 'admin.partials.paginate') {
    return $data->appends(request()->all())->links($design);
}

function menuActive($routeName, $type = null) {

    if ($type == 3) {
        $class = 'side-menu--open';
    } elseif ($type == 2) {
        $class = 'sidebar-submenu__open';
    } else {
        $class = 'active';
    }

    if (is_array($routeName)) {

        foreach ($routeName as $key => $value) {

            if (request()->routeIs($value)) {
                return $class;
            }

        }

    } elseif (request()->routeIs($routeName)) {
        return $class;
    }

}

function imagePath() {
    $data['gateway'] = [
        'path' => 'assets/images/gateway',
        'size' => '800x800',
    ];
    $data['verify'] = [
        'withdraw' => [
            'path' => 'assets/images/verify/withdraw',
        ],
        'deposit'  => [
            'path' => 'assets/images/verify/deposit',
        ],
    ];
    $data['image'] = [
        'default' => 'assets/images/default.png',
    ];
    $data['withdraw'] = [
        'method' => [
            'path' => 'assets/images/withdraw/method',
            'size' => '800x800',
        ],
    ];
    $data['ticket'] = [
        'path' => 'assets/support',
    ];
    $data['digital_item'] = [
        'path' => 'assets/digitalItem',
    ];
    $data['brand'] = [
        'path' => 'assets/images/brand',
        'size' => '70x70',
    ];
    $data['category'] = [
        'path' => 'assets/images/category',
        'size' => '70x70',
    ];
    $data['product'] = [
        'thumb'   => [
            'path' => 'assets/images/product',
            'size' => '310x310',
        ],
        'gallery' => [
            'path' => 'assets/images/product/gallery',
            'size' => '510x510',
        ]
    ];
    $data['language'] = [
        'path' => 'assets/images/lang',
        'size' => '64x64',
    ];
    $data['logoIcon'] = [
        'path' => 'assets/images/logoIcon',
    ];
    $data['favicon'] = [
        'size' => '128x128',
    ];
    $data['extensions'] = [
        'path' => 'assets/images/extensions',
        'size' => '36x36',
    ];
    $data['seo'] = [
        'path' => 'assets/images/seo',
        'size' => '600x315',
    ];
    $data['profile'] = [
        'user'  => [
            'path' => 'assets/images/user/profile',
            'size' => '350x300',
        ],
        'admin' => [
            'path' => 'assets/admin/images/profile',
            'size' => '400x400',
        ],
    ];
    return $data;
}

function diffForHumans($date) {
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'Y-m-d h:i A') {
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

//moveable
function sendGeneralEmail($email, $subject, $message, $receiver_name = '') {

    $general = GeneralSetting::first();

    if ($general->en != 1 || !$general->email_from) {
        return;
    }

    $message = shortCodeReplacer("{{message}}", $message, $general->email_template);
    $message = shortCodeReplacer("{{fullname}}", $receiver_name, $message);
    $message = shortCodeReplacer("{{username}}", $email, $message);

    $config = $general->mail_config;

    if ($config->name == 'php') {
        sendPhpMail($email, $receiver_name, $subject, $message, $general);
    } else

    if ($config->name == 'smtp') {
        sendSmtpMail($config, $email, $receiver_name, $subject, $message, $general);
    } else

    if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $email, $receiver_name, $subject, $message, $general);
    } else

    if ($config->name == 'mailjet') {
        sendMailjetMail($config, $email, $receiver_name, $subject, $message, $general);
    }

}

function getContent($data_keys, $singleQuery = false, $limit = null, $orderById = false) {

    if ($singleQuery) {
        $content = Frontend::where('data_keys', $data_keys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });

        if ($orderById) {
            $content = $article->where('data_keys', $data_keys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $data_keys)->orderBy('id', 'desc')->get();
        }

    }

    return $content;
}

function gatewayRedirectUrl($type = false) {

    if ($type) {
        return 'user.order.history';
    } else {
        return 'user.checkout';
    }

}

function verifyG2fa($user, $code, $secret = null) {
    $ga = new GoogleAuthenticator();

    if (!$secret) {
        $secret = $user->tsc;
    }

    $oneCode  = $ga->getCode($secret);
    $userCode = $code;

    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();
        return true;
    } else {
        return false;
    }

}

function urlPath($routeName, $routeParam = null) {

    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }

    $basePath = route('home');
    $path     = str_replace($basePath, '', $url);
    return $path;
}

function productPrice($product){
    $discountPrice = showDiscountPrice($product->price,$product->discount,$product->discount_type);
    if($product->today_deals == 1){
        $general = GeneralSetting::first();
        $discountPrice = showDiscountPrice($product->price,$general->discount,$general->discount_type);
    }
    if ($discountPrice < 0) {
        $discountPrice = 0;
    }
    return $discountPrice;
}


function showDiscountPrice($price,$discount,$discount_type) {
    if ($discount != 0) {
        if ($discount_type == 1) {
            $discountPrice = $price - $discount;
        } else {
            $discountPrice = $price - ($price * $discount / 100);
        }
        return $discountPrice;
    }
    return $price;
}

function showProductRatings($avgRate){
    $ratings = '';
    if($avgRate > 0){
        $avgRating = $avgRate;
        $integerVal = floor($avgRating);
        $fraction = $avgRating - $integerVal;

        if($fraction < .25){
            $avgRating = intval($avgRating);
        }
        if($fraction > .75){
            $avgRating = intval($avgRating) + 1;
        }
        for($i = 1; $i <= $avgRating; $i++){
            $ratings .= '<i class="las la-star"></i>';
        }
        if($fraction > .25 && $fraction < .75){
            $avgRating += 1;
            $ratings .= '<i class="las la-star-half-alt"></i>';
        }
    } else {
        $avgRating = 0;
    }
    $nonStar = 5 - intval($avgRating);
    for($k = 1; $k <= $nonStar; $k++){
        $ratings .= '<i class="lar la-star"></i>';
    }
    return $ratings;
}
function discountText($product,$general){

    if($product->discount != 0){
        if($product->discount_type == 1){
            $discount = $general->cur_sym . showAmount($product->discount);
        }else{
            $discount = showAmount($product->discount).'%';
        }

    }else if($product->today_deals == 1){
        if ($general->discount_type == 1){
            $discount = $general->cur_sym . showAmount($general->discount);
        }else{
            $discount = showAmount($general->discount).'%';
        }
    }

    return "<span class='badge badge--discount'><i class='las la-minus'></i>
                $discount
            </span>";
}


/**
 * Resolve posted modifier selections into a validated, priced snapshot.
 *
 * Returns ['options' => [['group'=>..,'name'=>..,'price'=>..], ...],
 *          'price'   => <total delta>,
 *          'error'   => <string|null>]
 *
 * Validation lives here (not the UI) so a hand-crafted request can't skip a
 * required choice or smuggle an option that isn't attached to the dish.
 */
function resolveProductOptions($product, $selectedIds) {
    // NB: must be a closure, not 'intval' — Collection::map passes the key as
    // the 2nd arg, which intval() reads as the numeric base, so every element
    // after the first was silently converted to 0.
    $selectedIds = collect((array) $selectedIds)->filter()->map(function ($id) {
        return (int) $id;
    })->unique();
    $chosen      = [];
    $delta       = 0;

    $groups = $product->optionGroups()->with('options')->get();

    foreach ($groups as $group) {
        $picked = $group->options->whereIn('id', $selectedIds->all());

        if ($group->is_required && $picked->count() < max(1, $group->min_select)) {
            return ['options' => [], 'price' => 0, 'error' => 'Please choose an option for "' . $group->name . '".'];
        }
        if ($group->type == 'single' && $picked->count() > 1) {
            return ['options' => [], 'price' => 0, 'error' => 'Only one choice allowed for "' . $group->name . '".'];
        }
        if ($group->max_select > 0 && $picked->count() > $group->max_select) {
            return ['options' => [], 'price' => 0, 'error' => 'You may pick at most ' . $group->max_select . ' for "' . $group->name . '".'];
        }

        foreach ($picked as $opt) {
            $chosen[] = ['group' => $group->name, 'name' => $opt->name, 'price' => (float) $opt->price];
            $delta += (float) $opt->price;
        }
    }

    return ['options' => $chosen, 'price' => $delta, 'error' => null];
}

/** Human-readable one-line summary of a cart/order line's modifiers. */
function optionSummary($options) {
    if (empty($options)) { return ''; }
    if (is_string($options)) { $options = json_decode($options, true) ?: []; }
    return collect($options)->pluck('name')->implode(', ');
}

/**
 * Email the restaurant when an order arrives.
 *
 * The shipped code only ever notified the CUSTOMER — nothing told the kitchen.
 * Goes to adminNotifyAddress() (the admin account, not the noreply sender),
 * using the ADMIN_NEW_ORDER template. Never let a mail failure break checkout.
 */
function notifyAdminNewOrder($order) {
    try {
        $general = GeneralSetting::first();
        $to      = adminNotifyAddress();

        if (!$to || $general->en != 1) {
            return; // no destination, or email notifications switched off
        }

        $template = EmailTemplate::where('act', 'ADMIN_NEW_ORDER')->where('email_status', 1)->first();
        if (!$template) {
            return;
        }

        $order->loadMissing('orderDetail.product', 'user');

        $items = [];
        foreach ($order->orderDetail as $detail) {
            $line = ($detail->quantity ?? 1) . ' x ' . optional($detail->product)->name;
            if (optionSummary($detail->options)) {
                $line .= ' (' . optionSummary($detail->options) . ')';
            }
            $items[] = $line;
        }

        $address = $order->address;
        if (is_string($address)) {
            $address = json_decode($address, true) ?: [];
        }

        $shortCodes = [
            'order_no'    => $order->order_no,
            'user_name'   => optional($order->user)->username ?: 'Guest',
            'method_name' => $order->payment_type == 1 ? 'Online payment' : 'Cash on delivery',
            'total'       => showAmount($order->total),
            'currency'    => $general->cur_sym,
            'items'       => implode('<br>', $items),
            'address'     => implode(', ', array_filter((array) $address, 'is_scalar')),
        ];

        $message = $template->email_body;
        $subject = $template->subj;

        foreach ($shortCodes as $code => $value) {
            $message = shortCodeReplacer('{{' . $code . '}}', $value, $message);
            $subject = shortCodeReplacer('{{' . $code . '}}', $value, $subject);
        }

        // sendGeneralEmail dispatches to whichever transport is configured
        // (php/smtp/sendgrid/mailjet) with the correct argument order.
        sendGeneralEmail($to, $subject, $message, 'Restaurant Admin');
    } catch (\Throwable $e) {
        // an order must never fail because the mail server is down
        \Log::error('Admin new-order email failed: ' . $e->getMessage());
    }
}

/**
 * Store one of the two fixed-name brand images (logo.png / favicon.png).
 *
 * These cannot go through uploadImage(): the views hardcode the filenames, so
 * the upload must overwrite in place rather than receive a uniqid name. On S3
 * the object is replaced — important on Docker, where the container filesystem
 * is wiped on every redeploy and a locally-saved logo would silently vanish.
 */
function storeBrandImage($image, $filename) {
    $dir = imagePath()['logoIcon']['path'];

    if (usesS3Storage()) {
        Storage::disk('s3')->put($dir . '/' . $filename, (string) $image->encode('png'));
    } else {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        $image->save($dir . '/' . $filename);
    }

    // drop the "is there a custom one?" flag so the new file shows immediately
    \Illuminate\Support\Facades\Cache::forget('brand_image_' . $filename);
}

/**
 * URL for a brand image, preferring an admin-uploaded one.
 *
 * getImage() cannot be used here: it checks the local disk first, and the repo
 * ships placeholder logo.png/favicon.png files that would permanently shadow
 * anything the restaurant uploads. So when S3 is in use we look there first.
 * The existence check is cached for an hour — one HEAD per file, not per request.
 */
function brandImage($filename) {
    $path = imagePath()['logoIcon']['path'] . '/' . $filename;

    if (usesS3Storage()) {
        $exists = \Illuminate\Support\Facades\Cache::remember(
            'brand_image_' . $filename,
            3600,
            function () use ($path) {
                try {
                    return Storage::disk('s3')->exists($path);
                } catch (\Throwable $e) {
                    return false;
                }
            }
        );

        if ($exists) {
            return rtrim(config('filesystems.disks.s3.url'), '/') . '/' . $path;
        }
    }

    return asset($path);
}

/**
 * Public URL for a non-image upload (e.g. the home-page video). Mirrors
 * getImage()'s resolution order — local file wins, then S3 — but returns the
 * raw path rather than a placeholder when nothing is found.
 */
function fileUrl($path) {
    $path = ltrim($path, '/');

    if (file_exists(public_path($path)) && is_file(public_path($path))) {
        return asset($path);
    }

    if (usesS3Storage()) {
        return rtrim(config('filesystems.disks.s3.url'), '/') . '/' . $path;
    }

    return asset($path);
}

/**
 * The clip shown beside "Why you should choose Foodies".
 *
 * Falls back to the bundled /foodies.mp4 so the section never renders an empty
 * <video> on a fresh install where nothing has been uploaded yet.
 */
function homepageVideoUrl() {
    $file = optional(getContent('why_choose.content', true))->data_values->video ?? null;

    return $file
        ? fileUrl('assets/frontend/video/' . $file)
        : asset('foodies.mp4');
}

/**
 * Where restaurant-facing alerts (new order, new special request) are sent.
 *
 * Prefers the admin account's own address. `email_from` is the SENDER — on most
 * setups it is a "noreply@" mailbox nobody reads — so alerting it means the
 * kitchen never sees the message. Falls back to email_from when the admin
 * account has no address, and returns '' when neither is set (caller skips).
 */
function adminNotifyAddress() {
    static $address = null;

    if ($address === null) {
        try {
            $admin   = \App\Models\Admin::whereNotNull('email')->orderBy('id')->first();
            $address = trim(optional($admin)->email ?: '');
        } catch (\Throwable $e) {
            $address = '';
        }

        if (!$address) {
            $address = trim(optional(GeneralSetting::first())->email_from ?: '');
        }
    }

    return $address;
}

/**
 * True when outgoing mail is actually deliverable. sendGeneralEmail() refuses
 * to send without `email_from`, and does so silently — so screens that depend
 * on email can call this to warn the operator instead of failing invisibly.
 */
function emailDeliveryConfigured() {
    $general = GeneralSetting::first();

    return $general && $general->en == 1 && trim($general->email_from ?: '') !== '';
}

/**
 * Shortcodes shared by every special-request email, so the admin alert, the
 * customer acknowledgement and the quote reply all speak the same language.
 */
function specialRequestShortCodes($req) {
    $general = GeneralSetting::first();

    return [
        'request_no'   => $req->request_no,
        'request_type' => $req->type_name,
        'item'         => $req->item,
        'quantity'     => $req->quantity,
        'needed_on'    => $req->needed_on ? $req->needed_on->format('d M Y') : 'Not specified',
        'people'       => $req->people ?: 'Not specified',
        'budget'       => $req->budget ? $general->cur_sym . showAmount($req->budget) : 'Not specified',
        'name'         => $req->name,
        'email'        => $req->email,
        'phone'        => $req->phone,
        'details'      => nl2br(e($req->details ?: 'None')),
    ];
}

/**
 * Render one of the special-request templates and send it.
 *
 * Mirrors notifyAdminNewOrder(): a dead mail server must never turn a
 * successful enquiry into an error page, so everything is swallowed and logged.
 */
function sendSpecialRequestEmail($to, $act, $req, $extra = []) {
    try {
        $general = GeneralSetting::first();

        if (!$to || $general->en != 1) {
            return; // no destination, or email notifications switched off
        }

        $template = EmailTemplate::where('act', $act)->where('email_status', 1)->first();
        if (!$template) {
            return;
        }

        $message = $template->email_body;
        $subject = $template->subj;

        foreach (specialRequestShortCodes($req) + $extra as $code => $value) {
            $message = shortCodeReplacer('{{' . $code . '}}', $value, $message);
            $subject = shortCodeReplacer('{{' . $code . '}}', $value, $subject);
        }

        sendGeneralEmail($to, $subject, $message, $req->name);
    } catch (\Throwable $e) {
        \Log::error('Special request email (' . $act . ') failed: ' . $e->getMessage());
    }
}

/**
 * Ordering-availability config with safe defaults, so the storefront never
 * breaks if the blob is missing a key.
 */
function orderConfig($key = null) {
    static $cfg = null;
    if ($cfg === null) {
        $raw  = optional(GeneralSetting::first())->order_config ?: [];
        $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        $hours = [];
        foreach ($days as $d) {
            $h = $raw['hours'][$d] ?? [];
            $hours[$d] = [
                'closed' => (int) ($h['closed'] ?? 0),
                'open'   => $h['open']  ?? '09:00',
                'close'  => $h['close'] ?? '22:00',
            ];
        }
        $cfg = [
            'accepting_orders'   => (int) ($raw['accepting_orders']   ?? 1),
            'delivery_enabled'   => (int) ($raw['delivery_enabled']   ?? 1),
            'collection_enabled' => (int) ($raw['collection_enabled'] ?? 1),
            'hours'              => $hours,
        ];
    }
    return $key === null ? $cfg : ($cfg[$key] ?? null);
}

/**
 * Is the restaurant open for orders right now?
 * Returns ['open' => bool, 'reason' => string, 'today' => array|null].
 */
function restaurantOpen() {
    $cfg = orderConfig();

    if (!$cfg['accepting_orders']) {
        return ['open' => false, 'reason' => 'We are not accepting orders at the moment.', 'today' => null];
    }

    $now    = Carbon::now();
    $dayKey = strtolower($now->format('D'));   // mon, tue, ...
    $today  = $cfg['hours'][$dayKey] ?? null;

    if (!$today || $today['closed']) {
        return ['open' => false, 'reason' => 'We are closed today.', 'today' => $today];
    }

    try {
        $open  = Carbon::createFromFormat('H:i', $today['open'])->setDateFrom($now);
        $close = Carbon::createFromFormat('H:i', $today['close'])->setDateFrom($now);
    } catch (\Throwable $e) {
        return ['open' => true, 'reason' => '', 'today' => $today]; // malformed time -> fail open
    }

    // close time past midnight (e.g. 18:00 -> 02:00)
    if ($close->lessThanOrEqualTo($open)) {
        $close->addDay();
    }

    if ($now->between($open, $close)) {
        return ['open' => true, 'reason' => '', 'today' => $today];
    }

    $msg = $now->lessThan($open)
        ? 'We open today at ' . $open->format('g:i A') . '.'
        : 'We are closed for today. Hours: ' . $open->format('g:i A') . ' – ' . $close->format('g:i A') . '.';

    return ['open' => false, 'reason' => $msg, 'today' => $today];
}
