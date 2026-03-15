@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.hoc_vien.index') }}" class="text-purple-600 hover:text-purple-800 flex items-center mb-2">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Quay lại danh sách
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Thêm Học viên mới</h2>
</div>

<form action="{{ route('admin.hoc_vien.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cột trái: Thông tin tài khoản -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Thông tin tài khoản</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Thông tin học tập</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã học viên <span class="text-red-500">*</span></label>
                        <input type="text" name="ma_hoc_vien" value="{{ old('ma_hoc_vien') }}" required placeholder="Ví dụ: HV001" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        @error('ma_hoc_vien') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trường tốt nghiệp</label>
                        <input type="text" name="truong_tot_nghiep" value="{{ old('truong_tot_nghiep') }}" placeholder="Ví dụ: ĐH Bách Khoa..." class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày nhập học</label>
                        <input type="date" name="ngay_nhap_hoc" value="{{ old('ngay_nhap_hoc') }}" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Thông tin cá nhân</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                        <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh') }}" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                        <div class="mt-2 flex space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="gioi_tinh" value="nam" {{ old('gioi_tinh') == 'nam' ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">Nam</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="gioi_tinh" value="nu" {{ old('gioi_tinh') == 'nu' ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">Nữ</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="gioi_tinh" value="khac" {{ old('gioi_tinh') == 'khac' ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">Khác</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số CMND/CCCD</label>
                        <input type="text" name="so_cmnd" value="{{ old('so_cmnd') }}" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                        <textarea name="dia_chi" rows="2" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">{{ old('dia_chi') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Avatar & Actions -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Ảnh đại diện</h3>
                <div class="flex flex-col items-center">
                    <div class="relative w-32 h-32 mb-4">
                        <img id="avatar-preview" src="https://ui-avatars.com/api/?name=New+Student&size=128" class="w-full h-full rounded-full object-cover border-4 border-purple-100 shadow-inner" alt="Avatar preview">
                    </div>
                    <label class="cursor-pointer bg-purple-50 text-purple-700 px-4 py-2 rounded-lg hover:bg-purple-100 transition duration-150">
                        <span>Chọn ảnh</span>
                        <input type="file" name="avatar" class="hidden" onchange="previewImage(this)">
                    </label>
                    <p class="text-xs text-gray-500 mt-2">Định dạng: JPG, PNG, JPEG. Tối đa: 2MB</p>
                    @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition duration-150 mb-3">
                    Lưu học viên
                </button>
                <a href="{{ route('admin.hoc_vien.index') }}" class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-lg transition duration-150">
                    Hủy bỏ
                </a>
            </div>
        </div>
    </div>
</form>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
