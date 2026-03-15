<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel Admin') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <div class="w-64 bg-slate-900 text-white flex-shrink-0 min-h-screen shadow-xl fixed inset-y-0 left-0 z-50">
                <div class="p-6">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-600 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.052 0 0012 20.055a11.952 11.052 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        </div>
                        <span class="text-xl font-black tracking-tighter">EDUKHAITRI</span>
                    </div>
                </div>
                <nav class="mt-6 px-4 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                    
                    <div class="pt-4 pb-2 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] pl-4">Người dùng</div>
                    <a href="{{ route('admin.giang_vien.index') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.giang_vien.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Giảng viên
                    </a>
                    <a href="{{ route('admin.hoc_vien.index') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.hoc_vien.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Học viên
                    </a>

                    <div class="pt-4 pb-2 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] pl-4">Đào tạo</div>
                    <a href="{{ route('admin.khoa_hoc.index') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.khoa_hoc.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Khóa học
                    </a>
                    <a href="{{ route('admin.lop_hoc.index') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.lop_hoc.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Lớp học
                    </a>
                    <a href="{{ route('admin.lich_hoc.index') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.lich_hoc.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Thời khóa biểu
                    </a>

                    <div class="pt-4 pb-2 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] pl-4">Hệ thống</div>
                    <a href="{{ route('admin.bao_cao.index') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.bao_cao.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
                        Báo cáo & Thống kê
                    </a>
                    <a href="{{ route('admin.thong_bao.index') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.thong_bao.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        Thông báo
                    </a>
                </nav>
            </div>

            <!-- Content Area -->
            <div class="flex-1 ml-64 flex flex-col">
                <!-- Header -->
                <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-40 flex items-center justify-between px-8 shadow-sm">
                    <div>
                        <h1 class="text-lg font-black text-slate-800 uppercase tracking-widest">@yield('title', 'Quản trị hệ thống')</h1>
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
                                        <div class="p-8 text-center text-slate-400 text-xs italic font-medium">Bạn đã đọc hết thông báo! 🎉</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="h-8 w-px bg-slate-200"></div>

                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center focus:outline-none transition duration-150">
                                <img class="h-10 w-10 rounded-xl object-cover border-2 border-blue-500 shadow-sm" src="{{ Auth::user()->avatar ? asset('storage/'.Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=0f172a&color=fff' }}" alt="{{ Auth::user()->name }}">
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 border border-slate-100 z-50">
                                <div class="px-4 py-2 border-b border-slate-50 mb-1">
                                    <p class="text-xs font-black text-slate-800 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase">Administrator</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">Cài đặt hồ sơ</a>
                                <hr class="my-1 border-slate-50">
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
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl flex items-center shadow-sm">
                            <svg class="w-5 h-5 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span class="text-sm font-bold uppercase tracking-tight">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl flex items-center shadow-sm">
                            <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                            <span class="text-sm font-bold uppercase tracking-tight">{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </main>

                <!-- Footer -->
                <footer class="px-8 py-4 bg-white border-t border-slate-200 text-center text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em]">
                    &copy; {{ date('Y') }} EDUKHAITRI SYSTEM. All rights reserved.
                </footer>
            </div>
        </div>
        
        @yield('scripts')
    </body>
</html>
