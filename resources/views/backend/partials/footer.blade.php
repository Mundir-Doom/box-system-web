</div>
    <script src="{{static_asset('backend')}}/vendor/jquery/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <!-- Load Popper v2 before Bootstrap 5 (or use Bootstrap 5 bundle) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="{{static_asset('backend')}}/vendor/bootstrap-five/bootstrap.min.js"></script>
    <!-- Remove Bootstrap 4 bundle to avoid conflicts with Bootstrap 5 -->
    {{-- <script src="{{static_asset('backend')}}/vendor/bootstrap/js/bootstrap.bundle.js"></script> --}}
    <script src="{{static_asset('backend')}}/vendor/slimscroll/jquery.slimscroll.js"></script>
    <script src="{{static_asset('backend')}}/libs/js/main-js.js"></script>
    <script src="{{static_asset('backend')}}/vendor/charts/sparkline/jquery.sparkline.js"></script>
    <script src="{{static_asset('backend')}}/vendor/charts/morris-bundle/raphael.min.js"></script>
    <script src="{{static_asset('backend')}}/vendor/charts/morris-bundle/morris.js"></script>
    <script src="{{static_asset('backend')}}/vendor/charts/c3charts/c3.min.js"></script>
    <script src="{{static_asset('backend')}}/vendor/charts/c3charts/d3-5.4.0.min.js"></script>
    <script src="{{static_asset('backend')}}/libs/js/datepicker.min.js"></script>
    <script src="{{static_asset('backend')}}/libs/js/custom.js"></script>
    <script src="{{static_asset('backend')}}/js/dynamic-modal.js"></script>
    <script src="{{static_asset('backend')}}/js/lang.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> 
    <script src="{{ static_asset('backend/vendor') }}/toastr/toastr.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 
    <script type="text/javascript">   
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script>var yes = "{{ __('delete.yes') }}";</script>
    <script>var cancel = "{{ __('delete.cancel') }}";</script>

    <script type="text/javascript">
        "use strict";
        $(function(){
            $('.demo-login-btn').click(function(){
                $('#email').attr('value',$(this).data('email'));
                $('#password').attr('value',$(this).data('password'));
            });
            // Prevent accidental navigation on collapse toggles in sidebar
            $(document).on('click', 'a[data-bs-toggle="collapse"], a[data-toggle="collapse"]', function(e){
                var href = $(this).attr('href') || '';
                if (href === '' || href === '#' || href.indexOf('javascript:') === 0) {
                    e.preventDefault();
                }
            });
            // Global AJAX error handler for clearer diagnostics
            $(document).ajaxError(function(event, jqxhr, settings, thrownError){
                var message = 'Request failed';
                var details = '';
                if (jqxhr && jqxhr.responseJSON && jqxhr.responseJSON.message) {
                    details = jqxhr.responseJSON.message;
                } else if (jqxhr && jqxhr.responseText) {
                    // Trim long HTML responses
                    details = jqxhr.responseText.replace(/<[^>]*>?/gm, '').slice(0, 300);
                } else if (thrownError) {
                    details = String(thrownError);
                }
                var url = settings && settings.url ? settings.url : '';
                var status = jqxhr && jqxhr.status ? jqxhr.status : '';
                var full = (url ? ('['+url+'] ') : '') + (status ? ('HTTP '+status+': ') : '') + details;
                if (typeof toastr !== 'undefined') {
                    toastr.error(full || message, 'Error');
                } else {
                    console.error('AJAX Error:', full || message);
                    alert(full || message);
                }
            });
        });
    </script>
@stack('scripts')

<script type="text/javascript">
    "use strict";
    $(document).ready(function() {
        // Ensure DOM is fully loaded before any operations
        if (!document.body) {
            console.warn('Document body not ready');
            return;
        }
        var firebaseConfig = {
            apiKey: "AIzaSyCaPJouHyLoY70OH8oFhSsiYuSD0HGCM0k",
            authDomain: "wemover-37dd3.firebaseapp.com",
            projectId: "wemover-37dd3",
            storageBucket: "wemover-37dd3.appspot.com",
            messagingSenderId: "627685996237",
            appId: "1:627685996237:web:317d417edc4c90ba14db84",
            measurementId: "G-H7DDEG6TY3"
        };

            firebase.initializeApp(firebaseConfig);
            const messaging = firebase.messaging();
            
            // Initialize FCM but don't request permission automatically
            function startFCM() {
                // Only request permission when there's user interaction
                if (Notification.permission === 'granted') {
                    getTokenAndStore();
                } else if (Notification.permission !== 'denied') {
                    // Wait for user interaction before requesting permission
                    $(document).one('click', function() {
                        messaging.requestPermission()
                            .then(function () {
                                return messaging.getToken()
                            })
                            .then(function (response) {
                                if (response) {
                                    $.ajax({
                                        url: '{{ route("notification-store.token") }}',
                                        type: 'POST',
                                        data: {
                                            token: response
                                        },
                                        dataType: 'JSON',
                                        success: function (response) {
                                            // console.log('FCM token stored');
                                        },
                                        error: function (error) {
                                            // console.log('FCM token error:', error);
                                        },
                                    });
                                }
                            }).catch(function (error) {
                            // console.log('FCM permission denied or error:', error);
                        });
                    });
                }
            }
            
            function getTokenAndStore() {
                messaging.getToken()
                    .then(function (newToken) {
                        if (!newToken) return;

                        try {
                            var lastToken = localStorage.getItem('web_fcm_token');
                        } catch (e) { lastToken = null; }

                        if (lastToken === newToken) {
                            return; // avoid duplicate POST
                        }

                        $.ajax({
                            url: '{{ route("notification-store.token") }}',
                            type: 'POST',
                            data: { token: newToken },
                            dataType: 'JSON',
                            success: function () {
                                try { localStorage.setItem('web_fcm_token', newToken); } catch (e) {}
                            },
                            error: function () {}
                        });
                    }).catch(function () {});
            }
            
            // Initialize FCM
            startFCM();

            messaging.onMessage(function(payload) {
                // console.log(payload.notification);
                const title = payload.notification.title;
                const options = {
                    body: payload.notification.body,
                    icon: payload.notification.icon,
                };
                Swal.fire({
                    imageUrl:payload.notification.image,
                    title: title,
                    text: payload.notification.body,
                    position: 'top',
                    showOkButton: true,
                    showCancelButton: true,
                    confirmButtonText: yes,
                    cancelButtonText: cancel,
                }).then((result) => {
                    if (result.isConfirmed){
                        // console.log('ok');
                    }
                })
                new Notification(title, options);
            });
    });
</script>

    {!! Toastr::message() !!}
</body>
</html>
