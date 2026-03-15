@extends('layouts.giang_vien')

@section('title', 'Danh sách học viên')

@section('content')
<div class="mb-6">
    <a href="{{ route('gv.lop_hoc.show', $lopHoc->id) }}" class="text-emerald-600 hover:text-emerald-800 flex items-center font-bold text-sm transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Quay lại chi tiết lớp
    </a>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/30">
        <div>
            <h2 class="text-2xl font-black text-slate-800">{{ $lopHoc->ten_lop }}</h2>
            <p class="text-slate-500 font-medium">Danh sách toàn bộ học viên của lớp</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="relative">
                <input type="text" placeholder="Tìm tên, mã học viên..." class="pl-10 pr-4 py-2 rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-bold w-64">
                <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-2xl text-sm font-black transition shadow-lg shadow-emerald-100 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                XUẤT EXCEL
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                    <th class="px-8 py-6">STT</th>
                    <th class="px-8 py-6">Thông tin học viên</th>
                    <th class="px-8 py-6">Mã học viên</th>
                    <th class="px-8 py-6 text-center">Chuyên cần</th>
                    <th class="px-8 py-6 text-center">Điểm TB</th>
                    <th class="px-8 py-6 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($hocViens as $index => $hv)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-8 py-6 text-sm font-bold text-slate-400">{{ $index + 1 }}</td>
                        <td class="px-8 py-6">
                            <div class="flex items-center">
                                <img class="h-12 w-12 rounded-2xl object-cover mr-4 border-2 border-white shadow-sm" src="{{ $hv->avatar ? asset('storage/'.$hv->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($hv->name).'&background=f1f5f9&color=475569' }}" alt="">
                                <div>
                                    <p class="text-sm font-black text-slate-700 leading-tight">{{ $hv->name }}</p>
                                    <p class="text-[10px] font-medium text-slate-400 leading-tight">{{ $hv->email }}</p>
                                    <p class="text-[10px] font-bold text-emerald-600 leading-tight mt-1">{{ $hv->phone ?? '---' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="font-mono font-black text-slate-400 text-xs px-3 py-1 bg-slate-100 rounded-lg">{{ $hv->hocVienProfile->ma_hoc_vien ?? 'N/A' }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-black text-slate-700">--%</span>
                                <div class="w-12 h-1 bg-slate-100 rounded-full mt-1">
                                    <div class="h-full bg-emerald-400 rounded-full" style="width: 0%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="text-sm font-black text-slate-300 italic">Chưa nhập</span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end space-x-2">
                                <button title="Hồ sơ chi tiết" class="p-2 text-slate-400 hover:text-emerald-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </button>
                                <button title="Nhập điểm" class="p-2 text-slate-400 hover:text-blue-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
