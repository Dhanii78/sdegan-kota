@if(Auth::check())
    <script>
        window.SDeganNotificationConfig = {
            checkUrl: "{{ route('notifications.check') }}",
            userRole: "{{ strtolower(Auth::user()->role ?? '') }}"
        };
        @if(session('success') || session('status'))
            window.SDeganFlashSuccess = true;
        @endif
    </script>
    <script src="{{ asset('js/notification-sound.js') }}"></script>
    <style>
        .sdegan-toast-popup {
            font-family: 'Poppins', system-ui, -apple-system, sans-serif !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18) !important;
            border: 1px solid rgba(16, 185, 129, 0.2) !important;
            background: #ffffff !important;
        }
        .sdegan-toast-popup .swal2-title {
            font-size: 15px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
        }
        .sdegan-toast-popup .swal2-html-container {
            font-size: 13px !important;
            color: #475569 !important;
        }
    </style>
@endif
