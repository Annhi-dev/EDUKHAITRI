@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.khoa_hoc.index') }}" class="text-purple-600 hover:text-purple-800 flex items-center mb-2">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Quay lại danh sách
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Chỉnh sửa Khóa học: {{ $khoa_hoc->ten_khoa_hoc }}</h2>
</div>

<div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 max-w-2xl">
    <form action="{{ route('admin.khoa_hoc.update', $khoa_hoc->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PATCH')
        <div>
            <label class="block text-sm font-medium text-gray-700">Mã khóa học (*)</label>
            <input type="text" name="ma_khoa_hoc" value="{{ old('ma_khoa_hoc', $khoa_hoc->ma_khoa_hoc) }}" required class="w-full rounded-lg border-gray-300">
            @error('ma_khoa_hoc') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Tên khóa học (*)</label>
            <input type="text" name="ten_khoa_hoc" value="{{ old('ten_khoa_hoc', $khoa_hoc->ten_khoa_hoc) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Mô tả</label>
            <textarea name="mo_ta" rows="3" class="w-full rounded-lg border-gray-300">{{ old('mo_ta', $khoa_hoc->mo_ta) }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Số buổi (*)</label>
                <input type="number" name="so_buoi" value="{{ old('so_buoi', $khoa_hoc->so_buoi) }}" required class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tiết mỗi buổi (*)</label>
                <input type="number" name="so_tiet_moi_buoi" value="{{ old('so_tiet_moi_buoi', $khoa_hoc->so_tiet_moi_buoi) }}" required class="w-full rounded-lg border-gray-300">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Học phí (*)</label>
            <input type="number" name="hoc_phi" value="{{ old('hoc_phi', $khoa_hoc->hoc_phi) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
            <select name="trang_thai" class="w-full rounded-lg border-gray-300">
                <option value="dang_mo" {{ $khoa_hoc->trang_thai == 'dang_mo' ? 'selected' : '' }}>Đang mở</option>
                <option value="da_ket_thuc" {{ $khoa_hoc->trang_thai == 'da_ket_thuc' ? 'selected' : '' }}>Đã kết thúc</option>
                <option value="tam_dung" {{ $khoa_hoc->trang_thai == 'tam_dung' ? 'selected' : '' }}>Tạm dừng</option>
            </select>
        </div>
        <div class="pt-4">
            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg font-bold">Cập nhật khóa học</button>
        </div>
    </form>
</div>
@endsection
