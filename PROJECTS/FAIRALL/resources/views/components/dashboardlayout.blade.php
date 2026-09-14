@props(['name' => null, 'title', 'hideChrome' => false])

@php
    $displayName = $name ?: auth()->user()?->display_name ?? 'Guest';

    $iconPaths = [
        'Dashboard' =>
            'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
        'Users' =>
            'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z',
        'Beneficiaries' =>
            'M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z',
        'Activities' =>
            'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z',
        'Events' =>
            'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z',
        'Competitions' =>
            'M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0',
        'Home Visits' =>
            'm2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
        'Donors' =>
            'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z',
        'Grants' =>
            'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z',
        'Configuration' =>
            'M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75',
        'Reports' =>
            'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
        'Archive' =>
            'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z',
        'Funding' =>
            'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'Donate' =>
            'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'Donations' =>
            'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
    ];

    $currentPath = request()->path();

    $menuItems = [
        ['label' => 'Dashboard', 'url' => '/dashboard', 'can' => true],
        ['label' => 'Users', 'url' => '/users', 'can' => auth()->user()?->can('manage-users')],
        ['label' => 'Beneficiaries', 'url' => '/beneficiaries', 'can' => auth()->user()?->can('work-on-beneficiaries')],
        ['label' => 'Activities', 'url' => '/activities', 'can' => auth()->user()?->can('work-on-activities')],
        ['label' => 'Events', 'url' => '/events', 'can' => auth()->user()?->can('manage-events')],
        ['label' => 'Competitions', 'url' => '/competitions', 'can' => auth()->user()?->can('work-on-competitions')],
        ['label' => 'Home Visits', 'url' => '/home-visits', 'can' => auth()->user()?->can('manage-home-visits')],
        ['label' => 'Donors', 'url' => '/donors', 'can' => auth()->user()?->can('manage-donors-and-grants')],
        ['label' => 'Grants', 'url' => '/grants', 'can' => auth()->user()?->can('manage-donors-and-grants')],
        ['label' => 'Funding', 'url' => '/funding', 'can' => auth()->user()?->can('manage-donors-and-grants')],
        ['label' => 'Configuration', 'url' => '/configuration', 'can' => auth()->user()?->can('manage-configurations')],
        ['label' => 'Reports', 'url' => '/reports', 'can' => auth()->user()?->can('manage-reports')],
        ['label' => 'Archive', 'url' => '/archive', 'can' => auth()->user()?->user_type === 'staff'],
        ['label' => 'Donate', 'url' => '/donor-portal/donate', 'can' => auth()->user()?->can('login-as-donor')],
        ['label' => 'Donations', 'url' => '/donor-portal/donations', 'can' => auth()->user()?->can('login-as-donor')],
    ];
@endphp


<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/FAIRALL_LOGO.svg') }}">
    <title>FAIRALL | {{ $title }} </title>
</head>


<body class="font-body h-screen"
    style="font-family: var(--font-body1); background-color: var(--color-white); overflow-x: hidden;">
    <x-loading-overlay />
    @if (!$hideChrome)
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 shadow-lg shadow-neutral-light1/20"
        style="background-color: var(--color-primary1);">
        <div class="w-full px-4 md:px-6 py-3">
            <div class="flex items-center justify-between">
                <!-- Left side: Hamburger -->
                <div class="flex items-center gap-3">
                    <!-- Hamburger Menu Button -->
                    <button id="menu-toggle" class="p-2 rounded-lg transition-all duration-200 hover:bg-white/10"
                        style="color: white;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Logo -->
                    <a href="/dashboard" class="flex items-center gap-2 group">
                        <img src="{{ asset('images/FAIRALL_LOGO.png') }}" alt="FAIRALL Logo"
                            class="h-10 lg:h-12 w-auto transition-all duration-300 group-hover:scale-105">
                        <div class="flex flex-col max-xs:hidden">
                            <span class="text-base sm:text-lg md:text-2xl font-bold tracking-wide text-white"
                                style="font-family: var(--font-header1);">FAIRALL</span>
                            <span class="text-[7px] sm:text-[8px] text-white/50 tracking-wider hidden xs:block"
                                style="font-family: var(--font-body1);">FAIRPLAY FOR ALL FOUNDATION</span>
                        </div>
                    </a>
                    <div class="hidden lg:block w-px h-6 bg-white/30"></div>
                    <p class="text-white/70 text-sm hidden lg:block">{{ $title }}</p>
                </div>


                <div class="flex items-center gap-2">
                    <span id="header-datetime" class="text-white/70 text-sm max-xs:hidden"></span>
                    <x-notification-dropdown></x-notification-dropdown>


                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button"
                            class="btn btn-ghost btn-circle avatar hover:opacity-80 transition-all">
                            <div
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full overflow-hidden ring-2 ring-transparent hover:ring-yellow-400 transition-all">
                                <img alt="Profile"
                                    src="{{ auth()->user()?->avatar_url ?? asset('images/default-avatar.svg') }}"
                                    class="w-full h-full object-cover">
                            </div>
                        </div>
                        <ul tabindex="-1"
                            class="menu menu-sm dropdown-content bg-white rounded-xl z-1 mt-2 w-56 p-2 shadow-xl border border-gray-100">
                            <li class="menu-title border-b border-gray-100 pb-2 mb-2">
                                <span class="font-semibold text-sm"
                                    style="color: var(--color-primary1);">{{ $displayName }}</span>
                            </li>
                            <li><a href="/profile"
                                    class="flex items-center gap-2 w-full rounded transition duration-300 ease-in-out hover:bg-primary1/10"
                                    style="color: var(--color-primary1);"><svg class="w-4 h-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>Profile</a></li>
                            <li class="mt-1">
                                @auth
                                    <form method="POST" action="/logout"
                                        class="w-full rounded transition duration-300 ease-in-out hover:bg-[#dc2626]/10">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="flex items-center gap-2" style="color: #dc2626;"><svg
                                                class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>Logout</button>
                                    </form>
                                @endauth
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>
    @endif


    @if (!$hideChrome)
    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed top-0 left-0 bottom-0 w-60 bg-white z-45 transition-transform duration-300 ease-in-out overflow-y-auto flex flex-col -translate-x-full"
        style="display: none; background-color: white;">

        <!-- Navigation Menu -->
        <nav class="p-4 flex-1 mt-20">
            <ul class="space-y-1">
                @foreach ($menuItems as $item)
                    @if ($item['can'])
                        @php
                            $itemUrl = ltrim($item['url'], '/');
                            $isActive =
                                $currentPath === $itemUrl ||
                                ($currentPath !== '' && str_starts_with($currentPath, $itemUrl . '/'));
                            $iconPath = $iconPaths[$item['label']] ?? 'M12 6v6m0 0v6m0-6h6m-6 0H6';
                        @endphp
                        <li>
                            <a href="{{ $item['url'] }}"
                                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 group {{ $isActive ? 'active' : '' }}">
                                <svg class="w-5 h-5 transition-all duration-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"></path>
                                </svg>
                                <span class="text-sm font-medium"
                                    style="font-family: var(--font-body1);">{{ $item['label'] }}</span>
                                @if ($isActive)
                                    <span class="ml-auto w-1.5 h-1.5 rounded-full"
                                        style="background-color: var(--color-primary1);"></span>
                                @endif
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>
    </aside>

    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-primary1/50 z-40 hidden transition-opacity duration-300"></div>
    @endif

    <!-- Main Content -->
    <div class="min-h-full w-full bg-primary1 flex @if(!$hideChrome) pt-20 @else items-center justify-center @endif overflow-x-hidden overflow-y-auto">
        @if (!$hideChrome)
        <!-- Spacer for desktop sidebar -->
        <div id="sidebar-spacer"
            class="hidden lg:block w-64 shrink-0 bg-primary1 transition-all duration-300 ease-in-out"></div>
        @endif

        <main class="flex-1 min-w-0 p-6 lg:p-12 transition-all duration-300 ease-in-out">
            {{ $slot }}
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebar-overlay');
            var spacer = document.getElementById('sidebar-spacer');
            var menuToggle = document.getElementById('menu-toggle');
            var closeButton = document.getElementById('close-sidebar');
            var isSidebarOpen = false;
            var DESKTOP_BREAKPOINT = 1024;
            var LS_KEY = 'sidebar_open';
            var isDesktop = function() {
                return window.innerWidth >= DESKTOP_BREAKPOINT;
            };

            var hamburgerIcon =
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
            var closeIcon =
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';

            function saveState(open) {
                try {
                    localStorage.setItem(LS_KEY, open ? '1' : '0');
                } catch (e) {}
            }

            function openSidebar(save) {
                if (sidebar) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                }
                if (isDesktop()) {
                    if (overlay) {
                        overlay.classList.add('hidden');
                    }
                    document.body.style.overflow = '';
                    if (spacer) {
                        spacer.classList.remove('w-0');
                        spacer.classList.add('w-64');
                    }
                } else {
                    if (overlay) {
                        overlay.classList.remove('hidden');
                    }
                    document.body.style.overflow = 'hidden';
                    if (spacer) {
                        spacer.style.display = 'none';
                    }
                }
                isSidebarOpen = true;
                if (menuToggle) {
                    menuToggle.innerHTML = closeIcon;
                }
                if (save !== false) {
                    saveState(true);
                }
            }

            function closeSidebar(save) {
                if (sidebar) {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                }
                if (overlay) {
                    overlay.classList.add('hidden');
                }
                document.body.style.overflow = '';
                if (spacer) {
                    spacer.classList.remove('w-64');
                    spacer.classList.add('w-0');
                }
                isSidebarOpen = false;
                if (menuToggle) {
                    menuToggle.innerHTML = hamburgerIcon;
                }
                if (save !== false) {
                    saveState(false);
                }
            }

            if (menuToggle) {
                menuToggle.addEventListener('click', function() {
                    if (isSidebarOpen) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }
            if (closeButton) {
                closeButton.addEventListener('click', closeSidebar);
            }
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            function updateClock() {
                var now = new Date();
                var h = now.getHours();
                var m = now.getMinutes().toString().padStart(2, '0');
                var ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12 || 12;
                document.getElementById('header-datetime').textContent = h + ':' + m + ' ' + ampm;
            }
            updateClock();
            setInterval(updateClock, 1000);

            if (sidebar) {
                sidebar.querySelectorAll('a').forEach(function(link) {
                    link.addEventListener('click', function() {
                        if (!isDesktop()) {
                            closeSidebar();
                        }
                    });
                });
            }

            function handleResize() {
                if (isDesktop()) {
                    if (!isSidebarOpen) {
                        openSidebar(false);
                    }
                } else {
                    if (isSidebarOpen) {
                        closeSidebar(false);
                    }
                }
            }

            var prevWidth = window.innerWidth;
            window.addEventListener('resize', function() {
                var currentWidth = window.innerWidth;
                var crossedUp = prevWidth < DESKTOP_BREAKPOINT && currentWidth >= DESKTOP_BREAKPOINT;
                var crossedDown = prevWidth >= DESKTOP_BREAKPOINT && currentWidth < DESKTOP_BREAKPOINT;
                if (crossedUp || crossedDown) {
                    handleResize();
                }
                prevWidth = currentWidth;
            });

            sidebar.style.display = '';
            void sidebar.offsetHeight;

            var stored = (function() {
                try {
                    return localStorage.getItem(LS_KEY);
                } catch (e) {
                    return null;
                }
            })();

            if (isDesktop()) {
                if (stored === '0') {
                    closeSidebar(false);
                } else {
                    openSidebar(false);
                }
            } else {
                if (stored !== null) {
                    saveState(false);
                }
            }
        });
    </script>
</body>


</html>
