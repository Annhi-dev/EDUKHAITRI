@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Quản lý Lớp học</h2>
        <p class="text-sm text-gray-600">Danh sách các lớp đang và sắp mở</p>
    </div>
    <a href="{{ route('admin.lop_hoc.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center shadow-md transition duration-150">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Tạo lớp học mới
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Mã Lớp</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Tên lớp / Khóa học</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Giảng viên</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Sĩ số</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Khai giảng</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($lopHocs as $lop)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-mono font-bold text-purple-600">{{ $lop->ma_lop }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $lop->ten_lop }}</div>
                        <div class="text-xs text-gray-500">{{ $lop->khoaHoc->ten_khoa_hoc }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $lop->giangVien->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $lop->hocViens->count() }} / {{ $lop->si_so_toi_da }}</td>
                    <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($lop->ngay_bat_dau)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-bold rounded-full {{ $lop->trang_thai == 'dang_hoc' ? 'bg-green-100 text-green-800' : ($lop->trang_thai == 'sap_khai_giang' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ $lop->trang_thai == 'dang_hoc' ? 'Đang học' : ($lop->trang_thai == 'sap_khai_giang' ? 'Sắp mở' : 'Kết thúc') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.lop_hoc.show', $lop->id) }}" class="text-purple-600 hover:text-purple-900 font-bold">Chi tiết</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
