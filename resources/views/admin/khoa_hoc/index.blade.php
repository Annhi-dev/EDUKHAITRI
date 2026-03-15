@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Quản lý Khóa học</h2>
        <p class="text-sm text-gray-600">Danh mục các khóa học của trung tâm</p>
    </div>
    <a href="{{ route('admin.khoa_hoc.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center shadow-md transition duration-150">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Thêm khóa học
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Mã KH</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Tên khóa học</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Số buổi</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Học phí</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($khoaHocs as $kh)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-mono font-bold text-purple-600">{{ $kh->ma_khoa_hoc }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $kh->ten_khoa_hoc }}</td>
                    <td class="px-6 py-4">{{ $kh->so_buoi }} buổi</td>
                    <td class="px-6 py-4">{{ number_format($kh->hoc_phi) }} VNĐ</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-bold rounded-full {{ $kh->trang_thai == 'dang_mo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $kh->trang_thai == 'dang_mo' ? 'Đang mở' : ($kh->trang_thai == 'da_ket_thuc' ? 'Kết thúc' : 'Tạm dừng') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.khoa_hoc.edit', $kh->id) }}" class="text-amber-600 hover:text-amber-900">Sửa</a>
                        <form action="{{ route('admin.khoa_hoc.destroy', $kh->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Xóa khóa học?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
