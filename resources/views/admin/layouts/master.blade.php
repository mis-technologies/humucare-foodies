<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $general->sitename($pageTitle ?? '') }}</title>
    <!-- site favicon -->
    <link rel="shortcut icon" type="image/png" href="{{getImage(imagePath()['logoIcon']['path'] .'/favicon.png')}}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap">
    <!-- bootstrap 4  -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/grid.min.css') }}">
    <!-- bootstrap toggle css -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor/bootstrap-toggle.min.css')}}">
    <!-- fontawesome 5  -->
    <link rel="stylesheet" href="{{asset('assets/global/css/all.min.css')}}">
    <!-- line-awesome webfont -->
    <link rel="stylesheet" href="{{asset('assets/global/css/line-awesome.min.css')}}">

    @stack('style-lib')

    <!-- custom select box css -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor/nice-select.css')}}">
    <!-- code preview css -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor/prism.css')}}">
    <!-- select 2 css -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor/select2.min.css')}}">
    <!-- jvectormap css -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor/jquery-jvectormap-2.0.5.css')}}">
    <!-- datepicker css -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor/datepicker.min.css')}}">
    <!-- timepicky for time picker css -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor/jquery-timepicky.css')}}">
    <!-- bootstrap-clockpicker css -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor/bootstrap-clockpicker.min.css')}}">
    <!-- bootstrap-pincode css -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor/bootstrap-pincode-input.css')}}">
    <!-- dashdoard main css -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/app.css')}}">


    @stack('style')
</head>
<body>
@yield('content')



<!-- jQuery library -->
<script src="{{asset('assets/global/js/jquery-3.6.0.min.js')}}"></script>
<!-- bootstrap js -->
<script src="{{asset('assets/admin/js/vendor/bootstrap.bundle.min.js')}}"></script>
<!-- bootstrap-toggle js -->
<script src="{{asset('assets/admin/js/vendor/bootstrap-toggle.min.js')}}"></script>

<!-- slimscroll js for custom scrollbar -->
<script src="{{asset('assets/admin/js/vendor/jquery.slimscroll.min.js')}}"></script>
<!-- custom select box js -->
<script src="{{asset('assets/admin/js/vendor/jquery.nice-select.min.js')}}"></script>


@include('partials.notify')
@stack('script-lib')

<script src="{{ asset('assets/admin/js/nicEdit.js') }}"></script>

<!-- code preview js -->
<script src="{{asset('assets/admin/js/vendor/prism.js')}}"></script>
<!-- seldct 2 js -->
<script src="{{asset('assets/admin/js/vendor/select2.min.js')}}"></script>
<!-- main js -->
<script src="{{asset('assets/admin/js/app.js')}}"></script>

{{-- LOAD NIC EDIT --}}
<script>
    "use strict";
    bkLib.onDomLoaded(function() {
        $( ".nicEdit" ).each(function( index ) {
            $(this).attr("id","nicEditor"+index);
            new nicEditor({fullPanel : true}).panelInstance('nicEditor'+index,{hasPanel : true});
        });
    });
    (function($){
        $( document ).on('mouseover ', '.nicEdit-main,.nicEdit-panelContain',function(){
            $('.nicEdit-main').focus();
        });
    })(jQuery);


    $(document).ready(function(){
      $("#mySearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });
    });
</script>

@stack('script')

{{-- ==========================================================
     New-order alert: polls for orders, chimes and shows a toast.
     Sound is synthesised via the Web Audio API so there is no
     audio asset to ship or 404.
     ========================================================== --}}
<style>
    .fd-order-toast{
        position:fixed;top:18px;right:18px;z-index:99999;width:330px;max-width:calc(100vw - 36px);
        background:#14142e;color:#fff;border-radius:14px;padding:16px 18px;
        box-shadow:0 18px 45px -12px rgba(0,0,0,.55);border-left:5px solid #f5a623;
        font-family:inherit;transform:translateX(120%);transition:transform .35s cubic-bezier(.2,.7,.3,1);
    }
    .fd-order-toast.show{transform:none;}
    .fd-order-toast__top{display:flex;align-items:center;gap:10px;margin-bottom:6px;}
    .fd-order-toast__bell{
        width:34px;height:34px;border-radius:50%;background:rgba(245,166,35,.18);color:#f5a623;
        display:inline-flex;align-items:center;justify-content:center;font-size:16px;
        animation:fdRing .9s ease-in-out infinite;
    }
    {{-- @@ escapes the at-rule; a bare @keyframes is swallowed by the Blade compiler --}}
    @@keyframes fdRing{0%,100%{transform:rotate(0)}20%{transform:rotate(14deg)}40%{transform:rotate(-12deg)}60%{transform:rotate(8deg)}80%{transform:rotate(-6deg)}}
    .fd-order-toast__title{font-weight:700;font-size:15px;margin:0;color:#fff;}
    .fd-order-toast__meta{font-size:13px;color:rgba(255,255,255,.7);margin:0 0 12px;}
    .fd-order-toast__actions{display:flex;gap:8px;}
    .fd-order-toast__btn{
        flex:1;text-align:center;border:0;cursor:pointer;border-radius:9px;padding:9px 12px;
        font-size:13px;font-weight:600;text-decoration:none;
    }
    .fd-order-toast__btn--go{background:#f5a623;color:#fff;}
    .fd-order-toast__btn--go:hover{background:#e08e12;color:#fff;}
    .fd-order-toast__btn--x{background:rgba(255,255,255,.12);color:#fff;}
    .fd-sound-nudge{
        position:fixed;bottom:18px;right:18px;z-index:99999;display:none;align-items:center;gap:8px;
        background:#f5a623;color:#fff;border:0;border-radius:99px;padding:11px 18px;cursor:pointer;
        font-size:13px;font-weight:600;box-shadow:0 12px 30px -10px rgba(245,166,35,.9);
    }
</style>

<button type="button" class="fd-sound-nudge" id="fdSoundNudge">🔔 Enable order sound</button>

<script>
(function () {
    "use strict";
    var POLL_MS = 15000;
    var KEY     = 'fdLastOrderId';
    var url     = "{{ route('admin.orders.new.check') }}";
    var audioCtx = null, soundReady = false;

    /* ---- synthesised chime (no audio file needed) ---- */
    function initAudio() {
        var AC = window.AudioContext || window.webkitAudioContext;
        if (!AC) return;
        if (!audioCtx) audioCtx = new AC();
        if (audioCtx.state === 'suspended') {
            audioCtx.resume().then(function () { soundReady = true; hideNudge(); });
        } else { soundReady = true; hideNudge(); }
    }
    function beep(at, freq) {
        var o = audioCtx.createOscillator(), g = audioCtx.createGain();
        o.type = 'sine'; o.frequency.value = freq;
        o.connect(g); g.connect(audioCtx.destination);
        g.gain.setValueAtTime(0.0001, at);
        g.gain.exponentialRampToValueAtTime(0.35, at + 0.03);
        g.gain.exponentialRampToValueAtTime(0.0001, at + 0.42);
        o.start(at); o.stop(at + 0.45);
    }
    function chime() {
        if (!audioCtx || audioCtx.state !== 'running') { showNudge(); return; }
        var t = audioCtx.currentTime;
        for (var r = 0; r < 3; r++) {          // ring three times
            beep(t + r * 0.75, 880);
            beep(t + r * 0.75 + 0.18, 1320);
        }
    }
    function showNudge() { document.getElementById('fdSoundNudge').style.display = 'inline-flex'; }
    function hideNudge() { document.getElementById('fdSoundNudge').style.display = 'none'; }

    // Browsers block audio until the user interacts — arm it on first gesture.
    ['click', 'keydown'].forEach(function (ev) {
        document.addEventListener(ev, function armOnce() {
            initAudio();
            document.removeEventListener(ev, armOnce);
        }, { once: true });
    });
    document.getElementById('fdSoundNudge').addEventListener('click', function () { initAudio(); chime(); });

    /* ---- toast ---- */
    function toast(data) {
        var el = document.createElement('div');
        el.className = 'fd-order-toast';
        el.innerHTML =
            '<div class="fd-order-toast__top">' +
                '<span class="fd-order-toast__bell"><i class="fas fa-bell"></i></span>' +
                '<p class="fd-order-toast__title">New order received!</p>' +
            '</div>' +
            '<p class="fd-order-toast__meta">#' + (data.order_no || '') + ' &middot; ' +
                (data.currency || '') + (data.total || '') +
                ' &middot; ' + data.pending_count + ' pending</p>' +
            '<div class="fd-order-toast__actions">' +
                '<a class="fd-order-toast__btn fd-order-toast__btn--go" href="' + data.detail_url + '">View order</a>' +
                '<button type="button" class="fd-order-toast__btn fd-order-toast__btn--x">Dismiss</button>' +
            '</div>';
        document.body.appendChild(el);
        requestAnimationFrame(function () { el.classList.add('show'); });

        function close() { el.classList.remove('show'); setTimeout(function () { el.remove(); }, 400); }
        el.querySelector('.fd-order-toast__btn--x').addEventListener('click', close);
        setTimeout(close, 30000);

        if (window.Notification && Notification.permission === 'granted') {
            new Notification('New order #' + (data.order_no || ''), { body: (data.currency || '') + (data.total || '') });
        }
    }

    /* ---- poll ---- */
    function poll() {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (d) {
                if (!d) return;
                var seen = parseInt(localStorage.getItem(KEY) || '0', 10);
                // First run just records the baseline so we don't alert on history.
                if (!seen) { localStorage.setItem(KEY, d.latest_id); return; }
                if (d.latest_id > seen) {
                    localStorage.setItem(KEY, d.latest_id);
                    chime();
                    toast(d);
                }
            })
            .catch(function () { /* offline / session expired — try again next tick */ });
    }

    if (window.Notification && Notification.permission === 'default') {
        document.addEventListener('click', function askOnce() {
            Notification.requestPermission();
            document.removeEventListener('click', askOnce);
        }, { once: true });
    }

    poll();
    setInterval(poll, POLL_MS);
})();
</script>

</body>
</html>
