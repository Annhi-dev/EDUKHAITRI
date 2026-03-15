@extends('layouts.admin')

@section('title', 'Tạo thông báo mới')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.thong_bao.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center font-bold text-sm transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại danh sách
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-10 border-b border-slate-50 bg-slate-50/30">
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-widest">Soạn thảo thông báo</h3>
            <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Gửi tin nhắn hệ thống đến người dùng</p>
        </div>

        <form action="{{ route('admin.thong_bao.store') }}" method="POST" class="p-10 space-y-8" x-data="{ target: 'tat_ca' }">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Tiêu đề -->
                <div class="space-y-2 md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tiêu đề thông báo (*)</label>
                    <input type="text" name="tieu_de" required placeholder="VD: Bảo trì hệ thống định kỳ..." 
                           class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-sm p-4">
                </div>

                <!-- Loại & Mức độ -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Phân loại</label>
                    <select name="loai" class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-sm p-4">
                        <option value="he_thong">Thông báo hệ thống</option>
                        <option value="chung">Thông báo chung</option>
                        <option value="lich_hoc">Thay đổi lịch học</option>
                        <option value="diem_so">Cập nhật điểm số</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mức độ hiển thị</label>
                    <select name="muc_do" class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-sm p-4">
                        <option value="info">Thông tin (Xanh dương)</option>
                        <option value="success">Thành công (Xanh lá)</option>
                        <option value="warning">Cảnh báo (Vàng)</option>
                        <option value="danger">Quan trọng (Đỏ)</option>
                    </select>
                </div>

                <!-- Nội dung -->
                <div class="space-y-2 md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nội dung chi tiết (*)</label>
                    <textarea name="noi_dung" rows="5" required placeholder="Nhập nội dung thông báo tại đây..." 
                              class="w-full rounded-[2rem] border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-medium text-sm p-6"></textarea>
                </div>

                <!-- URL -->
                <div class="space-y-2 md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Đường dẫn đính kèm (Tùy chọn)</label>
                    <input type="text" name="url" placeholder="VD: https://academy.com/news/1" 
                           class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-sm p-4">
                </div>

                <!-- Đối tượng nhận -->
                <div class="space-y-4 md:col-span-2 p-6 bg-blue-50 rounded-[2rem] border border-blue-100">
                    <label class="block text-[10px] font-black text-blue-600 uppercase tracking-widest ml-1">Đối tượng nhận thông báo</label>
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center space-x-2 cursor-pointer group">
                            <input type="radio" name="gui_den" value="tat_ca" x-model="target" checked class="text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-black text-slate-700 uppercase tracking-tighter">Gửi tất cả</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer group">
                            <input type="radio" name="gui_den" value="giang_vien" x-model="target" class="text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-black text-slate-700 uppercase tracking-tighter">Tất cả Giảng viên</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer group">
                            <input type="radio" name="gui_den" value="hoc_vien" x-model="target" class="text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-black text-slate-700 uppercase tracking-tighter">Tất cả Học viên</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer group">
                            <input type="radio" name="gui_den" value="cu_the" x-model="target" class="text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-black text-slate-700 uppercase tracking-tighter">Chọn người dùng cụ thể</span>
                        </label>
                    </div>

                    <!-- Select cụ thể (hiện khi chọn cu_the) -->
                    <div x-show="target === 'cu_the'" x-transition class="mt-6">
                        <select name="user_ids[]" multiple class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-sm p-4 min-h-[150px]">
                            @foreach($giangViens as $gv)
                                <option value="{{ $gv->id }}">{{ $gv->name }} (GV - {{ $gv->giangVienProfile->ma_giang_vien ?? '' }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-blue-400 mt-2 font-bold uppercase italic">* Nhấn giữ phím Ctrl để chọn nhiều người.</p>
                    </div>
                </div>
            </div>

            <div class="pt-6 text-center border-t border-slate-50">
                <button type="submit" class="inline-flex items-center px-20 py-4 bg-slate-900 hover:bg-slate-800 text-white rounded-[2rem] font-black uppercase tracking-widest text-xs transition shadow-2xl hover:scale-105 active:scale-95">
                    GỬI THÔNG BÁO NGAY
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
