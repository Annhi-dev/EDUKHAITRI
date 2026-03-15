@extends('layouts.giang_vien')

@section('title', 'Tạo yêu cầu đổi lịch')

@section('content')
<div class="mb-6">
    <a href="{{ route('gv.lich_day.index') }}" class="text-emerald-600 hover:text-emerald-800 flex items-center font-bold text-sm transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Quay lại
    </a>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/50">
            <h2 class="text-2xl font-black text-slate-800">Yêu cầu thay đổi buổi dạy</h2>
            <p class="text-slate-500 font-medium">Vui lòng nhập thông tin lịch mới và lý do cụ thể.</p>
        </div>

        <form action="{{ route('gv.yeu_cau.store') }}" method="POST" class="p-8 space-y-8">
            @csrf
            <input type="hidden" name="lich_hoc_id" value="{{ $lichHoc->id }}">

            <!-- Thông tin buổi học gốc -->
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Thông tin buổi học gốc (Read-only)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Lớp học</p>
                        <p class="text-sm font-black text-slate-700">{{ $lichHoc->lopHoc->ten_lop }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Ngày dạy cũ</p>
                        <p class="text-sm font-black text-slate-700">{{ \Carbon\Carbon::parse($lichHoc->ngay_hoc)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Giờ dạy cũ</p>
                        <p class="text-sm font-black text-slate-700">{{ substr($lichHoc->gio_bat_dau, 0, 5) }} - {{ substr($lichHoc->gio_ket_thuc, 0, 5) }}</p>
                    </div>
                </div>
            </div>

            <!-- Form nhập lịch mới -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-sm font-black text-slate-700">Ngày muốn dạy mới (*)</label>
                    <input type="date" name="ngay_muon_doi" value="{{ old('ngay_muon_doi') }}" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                    @error('ngay_muon_doi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-black text-slate-700">Phòng học mới (nếu có)</label>
                    <input type="text" name="phong_hoc_moi" value="{{ old('phong_hoc_moi') }}" placeholder="VD: P.202" class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-black text-slate-700">Giờ bắt đầu mới (*)</label>
                    <input type="time" name="gio_bat_dau_moi" value="{{ old('gio_bat_dau_moi', substr($lichHoc->gio_bat_dau, 0, 5)) }}" required class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-black text-slate-700">Giờ kết thúc mới (*)</label>
                    <input type="time" name="gio_ket_thuc_moi" value="{{ old('gio_ket_thuc_moi', substr($lichHoc->gio_ket_thuc, 0, 5)) }}" required class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                </div>
            </div>

            <div class="space-y-2" x-data="{ count: 0 }">
                <label class="block text-sm font-black text-slate-700">Lý do thay đổi lịch (*)</label>
                <textarea name="ly_do" required rows="4" maxlength="500" @input="count = $el.value.length" placeholder="Nhập ít nhất 10 ký tự lý do..." class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-medium"></textarea>
                <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
                    @error('ly_do') <p class="text-red-500">{{ $message }}</p> @else <span></span> @enderror
                    <span :class="count < 10 ? 'text-red-400' : 'text-emerald-500'"><span x-text="count"></span> / 500 ký tự</span>
                </div>
            </div>

            <div class="pt-6 flex space-x-4">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-emerald-100 transition duration-200">
                    GỬI YÊU CẦU CHO ADMIN
                </button>
                <a href="{{ route('gv.lich_day.index') }}" class="px-8 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black py-4 rounded-2xl transition duration-200">
                    HỦY
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
