@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Quản lý Tiêu chí đánh giá</h2>
    <p class="text-sm text-gray-600">Thiết lập các tiêu chí cho giảng viên, khóa học và học viên</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form thêm tiêu chí -->
    <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Thêm tiêu chí mới</h3>
            <form action="{{ route('admin.danh_gia.tieu_chi.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tên tiêu chí (*)</label>
                    <input type="text" name="ten_tieu_chi" required class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Loại đánh giá</label>
                    <select name="loai" required class="w-full rounded-lg border-gray-300">
                        <option value="giang_vien">Đánh giá Giảng viên</option>
                        <option value="khoa_hoc">Đánh giá Khóa học</option>
                        <option value="hoc_vien">Đánh giá Học viên</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Trọng số (1-5)</label>
                    <input type="number" name="trong_so" value="1" min="1" max="5" required class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                    <textarea name="mo_ta" rows="3" class="w-full rounded-lg border-gray-300"></textarea>
                </div>
                <button type="submit" class="w-full bg-purple-600 text-white font-bold py-2 rounded-lg hover:bg-purple-700 transition">Lưu tiêu chí</button>
            </form>
        </div>
    </div>

    <!-- Danh sách tiêu chí -->
    <div class="lg:col-span-2 space-y-6">
        @foreach(['giang_vien' => 'Giảng viên', 'khoa_hoc' => 'Khóa học', 'hoc_vien' => 'Học viên'] as $key => $label)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700 uppercase text-xs tracking-wider">Tiêu chí {{ $label }}</h3>
                </div>
                <table class="w-full text-left">
                    <tbody class="divide-y divide-gray-100">
                        @forelse($tieuChis[$key] ?? [] as $tc)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $tc->ten_tieu_chi }}</div>
                                    <div class="text-xs text-gray-500">{{ $tc->mo_ta }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">Trọng số: {{ $tc->trong_so }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.danh_gia.tieu_chi.destroy', $tc->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold" onclick="return confirm('Xóa?')">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="px-6 py-4 text-center text-gray-500 text-sm italic">Chưa có tiêu chí nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</div>
@endsection
