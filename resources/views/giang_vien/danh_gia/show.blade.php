@extends('layouts.giang_vien')

@section('title', 'Chi tiết đánh giá')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('gv.danh_gia.index') }}" class="text-emerald-600 hover:text-emerald-800 flex items-center font-bold text-sm transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Quay lại
    </a>
    <a href="{{ route('gv.danh_gia.edit', $danhGia->id) }}" class="px-6 py-2 bg-slate-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition hover:scale-105 active:scale-95">
        CHỈNH SỬA
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Thông tin học viên & Tổng quan -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="h-32 bg-gradient-to-br from-purple-600 to-indigo-700 relative">
                <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2">
                    <img class="h-24 w-24 rounded-[2rem] object-cover border-4 border-white shadow-xl" src="{{ $danhGia->hocVien->avatar ? asset('storage/'.$danhGia->hocVien->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($danhGia->hocVien->name) }}" alt="">
                </div>
            </div>
            <div class="pt-16 pb-8 px-8 text-center">
                <h3 class="text-xl font-black text-slate-800">{{ $danhGia->hocVien->name }}</h3>
                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">{{ $danhGia->hocVien->hocVienProfile->ma_hoc_vien ?? 'N/A' }}</p>
                
                <div class="inline-flex flex-col items-center p-6 bg-slate-50 rounded-[2rem] border border-slate-100 w-full">
                    <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Xếp loại</span>
                    <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-4
                        {{ match($danhGia->xep_loai) {
                            'xuat_sac' => 'bg-amber-100 text-amber-700',
                            'gioi' => 'bg-emerald-100 text-emerald-700',
                            'kha' => 'bg-blue-100 text-blue-700',
                            'trung_binh' => 'bg-slate-100 text-slate-700',
                            'yeu' => 'bg-red-100 text-red-700',
                            default => 'bg-slate-50 text-slate-400'
                        } }}">
                        {{ str_replace('_', ' ', $danhGia->xep_loai) }}
                    </span>
                    <div class="flex flex-col">
                        <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Điểm trung bình</span>
                        <span class="text-4xl font-black text-slate-800">{{ number_format($danhGia->diem_trung_binh, 1) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Thông tin kỳ học</h4>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm font-bold text-slate-500">Lớp học:</span>
                    <span class="text-sm font-black text-slate-800">{{ $danhGia->lopHoc->ten_lop }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-bold text-slate-500">Kỳ học:</span>
                    <span class="text-sm font-black text-slate-800">Kỳ {{ $danhGia->ky_hoc }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-bold text-slate-500">Năm học:</span>
                    <span class="text-sm font-black text-slate-800">{{ $danhGia->nam_hoc }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết tiêu chí & Nhận xét -->
    <div class="lg:col-span-2 space-y-8">
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <h3 class="text-lg font-black text-slate-800 mb-8 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
                Điểm theo tiêu chí
            </h3>
            
            <div class="space-y-6">
                @foreach($tieuChis as $tc)
                    @php $diem = $danhGia->chi_tiet_danh_gia[$tc->id] ?? 0; @endphp
                    <div class="space-y-2">
                        <div class="flex justify-between items-end">
                            <div>
                                <span class="text-xs font-black text-slate-700 uppercase tracking-widest">{{ $tc->ten_tieu_chi }}</span>
                                <span class="text-[10px] text-slate-400 ml-2 font-bold">(Trọng số: {{ $tc->trong_so }})</span>
                            </div>
                            <span class="text-sm font-black text-slate-800">{{ $diem }}/10</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-500 rounded-full" style="width: {{ $diem * 10 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                Nhận xét của giảng viên
            </h3>
            <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 italic text-slate-600 leading-relaxed">
                "{{ $danhGia->nhan_xet ?? 'Không có nhận xét nào.' }}"
            </div>
            <div class="mt-6 flex items-center justify-end text-[10px] font-black text-slate-400 uppercase tracking-widest">
                Đánh giá bởi: {{ $danhGia->giangVien->name }} • {{ $danhGia->updated_at->format('d/m/Y') }}
            </div>
        </div>
    </div>
</div>
@endsection
