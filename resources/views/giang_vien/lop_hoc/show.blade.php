@extends('layouts.giang_vien')

@section('title', 'Chi tiết lớp học')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('gv.lop_hoc.index') }}" class="text-emerald-600 hover:text-emerald-800 flex items-center font-bold text-sm transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Danh sách lớp
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Học viên</p>
        <p class="text-3xl font-black text-slate-800">{{ $thongKe['so_hv_dang_hoc'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Buổi đã dạy</p>
        <p class="text-3xl font-black text-emerald-600">{{ $thongKe['so_buoi_hoan_thanh'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Buổi còn lại</p>
        <p class="text-3xl font-black text-blue-600">{{ $thongKe['tong_so_buoi'] - $thongKe['so_buoi_hoan_thanh'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Chuyên cần TB</p>
        <p class="text-3xl font-black text-amber-500">--%</p>
    </div>
</div>

<div x-data="{ tab: 'hoc_vien' }">
    <!-- Tabs Nav -->
    <div class="flex space-x-1 bg-slate-200 p-1 rounded-2xl mb-6 w-fit">
        <button @click="tab = 'hoc_vien'" :class="tab === 'hoc_vien' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-600'" class="px-6 py-2 rounded-xl text-sm font-black transition duration-200">Học viên</button>
        <button @click="tab = 'lich_hoc'" :class="tab === 'lich_hoc' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-600'" class="px-6 py-2 rounded-xl text-sm font-black transition duration-200">Lịch học</button>
        <button @click="tab = 'diem_so'" :class="tab === 'diem_so' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-600'" class="px-6 py-2 rounded-xl text-sm font-black transition duration-200">Điểm số</button>
        <button @click="tab = 'diem_danh'" :class="tab === 'diem_danh' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-600'" class="px-6 py-2 rounded-xl text-sm font-black transition duration-200">Điểm danh tổng hợp</button>
    </div>

    <!-- Tab Content -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- Tab: Học viên -->
        <div x-show="tab === 'hoc_vien'" class="p-0">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50">
                        <th class="px-8 py-4">Mã HV</th>
                        <th class="px-8 py-4">Họ tên</th>
                        <th class="px-8 py-4 text-center">Chuyên cần</th>
                        <th class="px-8 py-4 text-center">Điểm TB</th>
                        <th class="px-8 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($lopHoc->hocViens as $hv)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-8 py-4 font-mono font-bold text-sm text-emerald-600">{{ $hv->hocVienProfile->ma_hoc_vien }}</td>
                            <td class="px-8 py-4">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full object-cover mr-3" src="{{ $hv->avatar ? asset('storage/'.$hv->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($hv->name) }}" alt="">
                                    <span class="text-sm font-bold text-slate-700">{{ $hv->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-4 text-center">--%</td>
                            <td class="px-8 py-4 text-center font-bold text-slate-400">Chưa có</td>
                            <td class="px-8 py-4 text-right">
                                <a href="#" class="text-emerald-600 font-bold text-xs hover:underline">Chi tiết</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tab: Lịch học -->
        <div x-show="tab === 'lich_hoc'" class="p-0 hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50">
                        <th class="px-8 py-4">Ngày học</th>
                        <th class="px-8 py-4">Giờ</th>
                        <th class="px-8 py-4">Phòng</th>
                        <th class="px-8 py-4">Trạng thái</th>
                        <th class="px-8 py-4 text-right">Sĩ số</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($lichHocsSapToi->merge($lichHocsDaQua) as $lich)
                        <tr>
                            <td class="px-8 py-4 text-sm font-bold text-slate-700">{{ \Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m/Y') }}</td>
                            <td class="px-8 py-4 text-sm text-slate-500">{{ substr($lich->gio_bat_dau, 0, 5) }} - {{ substr($lich->gio_ket_thuc, 0, 5) }}</td>
                            <td class="px-8 py-4 text-sm text-slate-500">{{ $lich->phong_hoc }}</td>
                            <td class="px-8 py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $lich->trang_thai === 'hoan_thanh' ? 'text-slate-400' : 'text-emerald-600' }}">
                                    {{ $lich->trang_thai }}
                                </span>
                            </td>
                            <td class="px-8 py-4 text-right text-sm font-bold text-slate-700">--/{{ $lopHoc->hocViens->count() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tab placeholders -->
        <div x-show="tab === 'diem_so'" class="p-12 text-center text-slate-400 italic hidden">Chức năng quản lý điểm đang được cập nhật...</div>
        <div x-show="tab === 'diem_danh'" class="p-12 text-center text-slate-400 italic hidden">Chức năng thống kê điểm danh đang được cập nhật...</div>
    </div>
</div>
@endsection
