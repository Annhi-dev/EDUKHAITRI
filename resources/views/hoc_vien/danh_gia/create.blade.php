@extends('layouts.hoc_vien')

@section('title', 'Thực hiện đánh giá')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-20">
    <!-- Breadcrumb -->
    <nav class="flex text-xs font-bold uppercase tracking-widest">
        <a href="{{ route('hv.danh_gia.index') }}" class="text-slate-400 hover:text-blue-600 transition">ĐÁNH GIÁ</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-blue-600">{{ $lopHoc->ten_lop }}</span>
    </nav>

    <!-- Thông tin khóa học -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 flex flex-col md:flex-row items-center gap-8">
        <div class="w-24 h-24 bg-blue-50 rounded-[2rem] flex items-center justify-center text-blue-600 border border-blue-100 shadow-inner">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        </div>
        <div class="flex-1 text-center md:text-left">
            <h2 class="text-2xl font-black text-slate-800 leading-tight">{{ $lopHoc->ten_lop }}</h2>
            <p class="text-blue-600 font-black uppercase tracking-widest text-[10px] mt-1">{{ $lopHoc->khoaHoc->ten_khoa_hoc }}</p>
            <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-4">
                <span class="flex items-center text-xs font-bold text-slate-500 bg-slate-50 px-3 py-1 rounded-lg">
                    <img class="w-5 h-5 rounded-full mr-2" src="{{ $lopHoc->giangVien->avatar ? asset('storage/'.$lopHoc->giangVien->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($lopHoc->giangVien->name) }}" alt="">
                    GV. {{ $lopHoc->giangVien->name }}
                </span>
                <span class="flex items-center text-xs font-bold text-slate-500 bg-slate-50 px-3 py-1 rounded-lg">
                    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Khai giảng: {{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Form Đánh giá -->
    <form action="{{ route('hv.danh_gia.store') }}" method="POST" class="space-y-8" x-data="{ 
        scores: { content: 0, teacher: 0, facility: 0 },
        setScore(type, val) { this.scores[type] = val }
    }">
        @csrf
        <input type="hidden" name="lop_hoc_id" value="{{ $lopHoc->id }}">

        <!-- Section 1: Star Rating -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-10 space-y-10">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-[0.2em] text-center mb-12">Đánh giá tổng quan</h3>
            
            <div class="space-y-12">
                @foreach([
                    'diem_noi_dung' => ['label' => 'Nội dung khóa học', 'key' => 'content'],
                    'diem_giang_vien' => ['label' => 'Chất lượng giảng viên', 'key' => 'teacher'],
                    'diem_co_so_vat_chat' => ['label' => 'Cơ sở vật chất', 'key' => 'facility']
                ] as $name => $cfg)
                    <div class="flex flex-col items-center space-y-4">
                        <label class="text-base font-black text-slate-700 uppercase tracking-widest">{{ $cfg['label'] }}</label>
                        <div class="flex space-x-3 text-slate-200">
                            <input type="hidden" name="{{ $name }}" x-model="scores.{{ $cfg['key'] }}" required>
                            @for($i=1; $i<=5; $i++)
                                <button type="button" @click="setScore('{{ $cfg['key'] }}', {{ $i }})" class="focus:outline-none transition-all duration-200 hover:scale-125" :class="scores.{{ $cfg['key'] }} >= {{ $i }} ? 'text-amber-400' : 'text-slate-200'">
                                    <svg class="w-10 h-10 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                </button>
                            @endfor
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Section 2: Tiêu chí chi tiết -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-10 space-y-10">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-[0.2em] mb-12">Đánh giá theo tiêu chí cụ thể</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                @foreach($tieuChis as $tc)
                    <div class="space-y-4" x-data="{ val: 5 }">
                        <div class="flex justify-between items-end">
                            <label class="text-xs font-black text-slate-700 uppercase tracking-widest">{{ $tc->ten_tieu_chi }}</label>
                            <span class="text-sm font-black px-3 py-1 rounded-lg border border-blue-100 bg-blue-50 text-blue-600" x-text="val + '/10'"></span>
                        </div>
                        <input type="range" name="chi_tiet_danh_gia[{{ $tc->id }}]" min="1" max="10" x-model="val" 
                               class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                            <span>Kém</span>
                            <span>Trung bình</span>
                            <span>Xuất sắc</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Section 3: Góp ý & Tùy chọn -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-10 space-y-8">
            <div class="space-y-4">
                <label class="text-sm font-black text-slate-800 uppercase tracking-widest ml-1">Ý kiến đóng góp khác</label>
                <textarea name="gop_y" rows="6" placeholder="Bạn có muốn chia sẻ thêm cảm nhận gì về khóa học này không?" 
                          class="w-full rounded-[2rem] border-slate-100 bg-slate-50 focus:ring-blue-500 focus:border-blue-500 text-sm p-6 font-medium"></textarea>
            </div>

            <div class="flex items-center justify-between p-6 bg-blue-50 rounded-[2rem] border border-blue-100">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-blue-600 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-800">Gửi ẩn danh</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter leading-tight">Tên của bạn sẽ không hiển thị trong báo cáo công khai</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="an_danh" value="1" checked class="sr-only peer">
                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
        </div>

        <!-- Submit -->
        <div class="text-center pt-8">
            <button type="submit" class="inline-flex items-center px-20 py-5 bg-slate-900 hover:bg-slate-800 text-white rounded-[2.5rem] font-black uppercase tracking-widest text-sm transition shadow-2xl hover:scale-105 active:scale-95">
                GỬI ĐÁNH GIÁ CỦA TÔI
                <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </button>
            <p class="mt-6 text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Cảm ơn những đóng góp quý báu của bạn!</p>
        </div>
    </form>
</div>
@endsection
