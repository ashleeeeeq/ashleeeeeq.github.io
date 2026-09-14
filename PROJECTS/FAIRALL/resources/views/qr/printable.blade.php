<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>
        @if (!empty($activityName) || !empty($eventName))
            {{ $activityName ?? $eventName }} QR Code
        @endif
    </title>
    <style>
        @media print {
            body {
                background-color: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .print-card {
                break-inside: avoid;
                page-break-inside: avoid;
                box-shadow: none !important;
            }
        }
    </style>
</head>

<body class="font-body" style="font-family: var(--font-body1); background: linear-gradient(135deg, #f0f4f8 0%, #dbeafe 100%); margin: 0; padding: 0; min-height: 100vh;">

    <div class="flex items-center justify-center min-h-screen px-4 py-8 sm:py-12">
        <!-- QR Card -->
        <div class="print-card rounded-2xl overflow-hidden w-full max-w-xl sm:max-w-2xl" style="background-color: var(--color-white); box-shadow: 0 25px 40px -12px rgba(0,0,0,0.2);">
            
            <!-- Header -->
            <div class="px-5 pt-8 pb-6 text-center" style="background-color: var(--color-primary1);">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: rgba(255,255,255,0.1); border: 1.5px solid var(--color-accent1);">
                        <img src="{{ asset('images/FAIRALL_LOGO.png') }}" alt="FAIRALL Logo" class="h-9 w-auto">
                    </div>
                </div>
                <h1 class="text-white text-2xl font-bold tracking-tight" style="font-family: var(--font-header1);">Attendance Pass</h1>
                @if (!empty($activityName) || !empty($eventName))
                    <div class="mt-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full" style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $activityName ?? $eventName }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- QR Code Section -->
            <div class="px-6 pt-10 pb-10 text-center">
                <div class="relative inline-block">
                    <!-- Decorative rounded corners -->
                    <div class="absolute -top-3 -left-3 w-10 h-10 rounded-tl-2xl" style="border-top: 2px solid var(--color-accent1); border-left: 2px solid var(--color-accent1);"></div>
                    <div class="absolute -top-3 -right-3 w-10 h-10 rounded-tr-2xl" style="border-top: 2px solid var(--color-accent1); border-right: 2px solid var(--color-accent1);"></div>
                    <div class="absolute -bottom-3 -left-3 w-10 h-10 rounded-bl-2xl" style="border-bottom: 2px solid var(--color-accent1); border-left: 2px solid var(--color-accent1);"></div>
                    <div class="absolute -bottom-3 -right-3 w-10 h-10 rounded-br-2xl" style="border-bottom: 2px solid var(--color-accent1); border-right: 2px solid var(--color-accent1);"></div>
                    
                    <!-- QR Container -->
                    <div class="relative p-3 rounded-xl" style="background: white; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.05);">
                        <div class="rounded-lg overflow-hidden">
                            <img src="https://quickchart.io/qr?text={{ urlencode($token) }}&size=400&dark=1c2143" alt="QR Code" class="w-56 h-56 sm:w-64 sm:h-64">
                        </div>
                    </div>
                </div>
                
                <div class="mt-8">
                    <p class="text-base font-semibold" style="color: var(--color-primary1); font-family: var(--font-header1);">Scan to Check In</p>
                    <p class="text-xs mt-2" style="color: var(--color-neutral-dark1); opacity: 0.5;">Use the mobile application to scan this QR.</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 text-center border-t" style="border-color: rgba(0,0,0,0.06); background-color: var(--color-white);">
                <p class="text-sm tracking-wide" style="color: var(--color-neutral-dark1); opacity: 0.4;">FAIRPLAY FOR ALL FOUNDATION • QR CODE</p>
            </div>
        </div>
    </div>

</body>

</html>