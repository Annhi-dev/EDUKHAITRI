<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Cổng Học Viên') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-slate-50">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="w-64 bg-blue-700 text-white flex-shrink-0 fixed inset-y-0 left-0 z-50 shadow-2xl overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="bg-white p-2 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.052 0 0012 20.055a11.952 11.052 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        </div>
                        <span class="text-xl font-bold tracking-wider uppercase">Học Viên</span>
                    </div>
                    <p class="text-blue-200 text-[10px] uppercase font-bold tracking-widest pl-1">Hệ thống EDUKHAITRI</p>
                </div>

                <div class="px-6 py-4 border-b border-blue-600/50 flex items-center space-x-3 mb-4">
                    <img class="h-10 w-10 rounded-full object-cover border-2 border-blue-400 shadow-sm" src="{{ Auth::user()->avatar ? asset('storage/'.Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=fff&color=2563eb' }}" alt="">
                    <div class="overflow-hidden">
                        <p class="text-sm font-bold truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-blue-300 font-mono">{{ Auth::user()->hocVienProfile->ma_hoc_vien ?? 'HV-XXXX' }}</p>
                    </div>
                </div>

                <nav class="px-4 space-y-1">
                    <a href="{{ route('hv.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl transition duration-200 group {{ request()->routeIs('hv.dashboard') ? 'bg-blue-800 text-white shadow-lg' : 'text-blue-100 hover:bg-blue-600' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('hv.dashboard') ? 'text-white' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>

                    <a href="{{ route('hv.lich_hoc.index') }}" class="flex items-center px-4 py-3 rounded-xl transition duration-200 group {{ request()->routeIs('hv.lich_hoc.*') ? 'bg-blue-800 text-white shadow-lg' : 'text-blue-100 hover:bg-blue-600' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('hv.lich_hoc.*') ? 'text-white' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Lịch học
                    </a>

                    <a href="{{ route('hv.ket_qua.index') }}" class="flex items-center px-4 py-3 rounded-xl transition duration-200 group {{ request()->routeIs('hv.ket_qua.*') ? 'bg-blue-800 text-white shadow-lg' : 'text-blue-100 hover:bg-blue-600' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('hv.ket_qua.*') ? 'text-white' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
                        Kết quả học tập
                    </a>

                    <a href="{{ route('hv.diem_danh.index') }}" class="flex items-center px-4 py-3 rounded-xl transition duration-200 group {{ request()->routeIs('hv.diem_danh.*') ? 'bg-blue-800 text-white shadow-lg' : 'text-blue-100 hover:bg-blue-600' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('hv.diem_danh.*') ? 'text-white' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Điểm danh
                    </a>

                    <a href="{{ route('hv.danh_gia.index') }}" class="flex items-center px-4 py-3 rounded-xl transition duration-200 group {{ request()->routeIs('hv.danh_gia.*') ? 'bg-blue-800 text-white shadow-lg' : 'text-blue-100 hover:bg-blue-600' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('hv.danh_gia.*') ? 'text-white' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        Đánh giá khóa học
                    </a>

                    <a href="{{ route('hv.khoa_hoc.index') }}" class="flex items-center px-4 py-3 rounded-xl transition duration-200 group {{ request()->routeIs('hv.khoa_hoc.*') ? 'bg-blue-800 text-white shadow-lg' : 'text-blue-100 hover:bg-blue-600' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('hv.khoa_hoc.*') ? 'text-white' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Khóa học của tôi
                    </a>

                    <hr class="my-4 border-blue-600/50">

                    <a href="{{ route('hv.profile.show') }}" class="flex items-center px-4 py-3 rounded-xl transition duration-200 group {{ request()->routeIs('hv.profile.*') ? 'bg-blue-800 text-white shadow-lg' : 'text-blue-100 hover:bg-blue-600' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('hv.profile.*') ? 'text-white' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Hồ sơ cá nhân
                    </a>
                </nav>
            </aside>

            <!-- Content Area -->
            <div class="flex-1 ml-64 flex flex-col">
                <!-- Header -->
                <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-40 flex items-center justify-between px-8 shadow-sm">
                    <div>
                        <h1 class="text-lg font-bold text-slate-800">@yield('title', 'Cổng Học Viên')</h1>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <!-- Thông báo Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="p-2 text-slate-400 hover:text-blue-600 transition relative">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                @php $soTB = Auth::user()->soThongBaoChuaDoc(); @endphp
                                @if($soTB > 0)
                                    <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[8px] font-black flex items-center justify-center rounded-full border-2 border-white animate-pulse">{{ $soTB }}</span>
                                @endif
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-[2rem] shadow-2xl border border-slate-100 z-50 overflow-hidden">
                                <div class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Thông báo mới</span>
                                    <a href="{{ route('thong_bao.index') }}" class="text-[10px] font-black text-blue-600 hover:underline">TẤT CẢ</a>
                                </div>
                                <div class="divide-y divide-slate-50 max-h-96 overflow-y-auto">
                                    @forelse(Auth::user()->thongBaos()->wherePivot('da_doc', false)->latest()->take(5)->get() as $tb)
                                        <a href="{{ route('thong_bao.doc', $tb->id) }}" class="block p-4 hover:bg-blue-50/50 transition border-l-4 {{ match($tb->muc_do){'danger'=>'border-red-500','warning'=>'border-amber-500',default=>'border-blue-500'} }}">
                                            <p class="text-xs font-black text-slate-800 leading-tight">{{ $tb->tieu_de }}</p>
                                            <p class="text-[10px] text-slate-400 mt-1 line-clamp-2">{{ $tb->noi_dung }}</p>
                                            <p class="text-[8px] text-blue-500 mt-2 font-bold uppercase">{{ $tb->pivot->created_at->diffForHumans() }}</p>
                                        </a>
                                    @empty
                                        <div class="p-8 text-center text-slate-400 text-xs italic font-medium">Bạn đã cập nhật hết thông tin! 🎉</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="h-8 w-px bg-slate-200"></div>

                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center focus:outline-none transition duration-150">
                                <img class="h-10 w-10 rounded-full object-cover border-2 border-blue-100 shadow-sm" src="{{ Auth::user()->avatar ? asset('storage/'.Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=2563eb&color=fff' }}" alt="{{ Auth::user()->name }}">
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 border border-slate-100 z-50">
                                <div class="px-4 py-2 border-b border-slate-50 mb-1">
                                    <p class="text-xs font-black text-slate-800 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase">Student</p>
                                </div>
                                <a href="{{ route('hv.profile.show') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">Cài đặt tài khoản</a>
                                <hr class="my-1 border-slate-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition font-bold uppercase tracking-widest text-[10px]">Đăng xuất</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main Content -->
                <main class="p-8 flex-1">
                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center shadow-sm">
                            <svg class="w-5 h-5 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center shadow-sm">
                            <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </main>

                <!-- Footer -->
                <footer class="px-8 py-4 bg-white border-t border-slate-200 text-center text-slate-400 text-xs font-bold uppercase tracking-widest">
                    &copy; {{ date('Y') }} EDUKHAITRI SYSTEM. All rights reserved.
                </footer>
            </div>
        </div>
        
        @yield('scripts')
    </body>
</html>
