<?php

namespace App\Http\Middleware;

use Fideloper\Proxy\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * '*' trusts the X-Forwarded-* headers (notably X-Forwarded-Proto) from the
     * TLS-terminating reverse proxy / load balancer in front of the app. Without
     * this, Laravel sees the internal HTTP request and generates http:// asset,
     * script, form and favicon URLs — which browsers block as mixed content on
     * an HTTPS site. Safe here because the app is only reachable via that proxy.
     * Override with the TRUSTED_PROXIES env var if you need to pin specific IPs.
     *
     * @var array|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_AWS_ELB;
}
