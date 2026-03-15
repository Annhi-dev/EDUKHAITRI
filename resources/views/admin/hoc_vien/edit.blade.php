@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.hoc_vien.index') }}" class="text-purple-600 hover:text-purple-800 flex items-center mb-2">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Quay lại danh sách
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Chỉnh sửa Học viên: {{ $hocVien->name }}</h2>
</div>

<form action="{{ route('admin.hoc_vien.update', $hocVien->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PATCH')
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cột trái: Thông tin tài khoản -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Thông tin tài khoản</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $hocVien->name) }}" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $hocVien->email) }}" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu (để trống nếu không đổi)</label>
                        <input type="password" name="password" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu mới</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone', $hocVien->phone) }}" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Thông tin học tập</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã học viên <span class="text-red-500">*</span></label>
                        <input type="text" name="ma_hoc_vien" value="{{ old('ma_hoc_vien', $hocVien->hocVienProfile->ma_hoc_vien) }}" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        @error('ma_hoc_vien') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trường tốt nghiệp</label>
                        <input type="text" name="truong_tot_nghiep" value="{{ old('truong_tot_nghiep', $hocVien->hocVienProfile->truong_tot_nghiep) }}" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày nhập học</label>
                        <input type="date" name="ngay_nhap_hoc" value="{{ old('ngay_nhap_hoc', $hocVien->hocVienProfile->ngay_nhap_hoc) }}" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 text-red-600 font-bold">Tình trạng học tập</label>
                        <select name="trang_thai" required class="w-full rounded-lg border-red-300 focus:border-red-500 focus:ring-red-500 bg-red-50">
                            <option value="dang_hoc" {{ old('trang_thai', $hocVien->hocVienProfile->trang_thai) == 'dang_hoc' ? 'selected' : '' }}>Đang học</option>
                            <option value="bao_luu" {{ old('trang_thai', $hocVien->hocVienProfile->trang_thai) == 'bao_luu' ? 'selected' : '' }}>Bảo lưu</option>
                            <option value="da_tot_nghiep" {{ old('trang_thai', $hocVien->hocVienProfile->trang_thai) == 'da_tot_nghiep' ? 'selected' : '' }}>Đã tốt nghiệp</option>
                            <option value="da_nghi" {{ old('trang_thai', $hocVien->hocVienProfile->trang_thai) == 'da_nghi' ? 'selected' : '' }}>Đã nghỉ</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Thông tin cá nhân</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                        <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh', $hocVien->hocVienProfile->ngay_sinh) }}" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                        <div class="mt-2 flex space-x-4">
                            @foreach(['nam' => 'Nam', 'nu' => 'Nữ', 'khac' => 'Khác'] as $val => $label)
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gioi_tinh" value="{{ $val }}" {{ old('gioi_tinh', $hocVien->hocVienProfile->gioi_tinh) == $val ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số CMND/CCCD</label>
                        <input type="text" name="so_cmnd" value="{{ old('so_cmnd', $hocVien->hocVienProfile->so_cmnd) }}" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                        <textarea name="dia_chi" rows="2" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">{{ old('dia_chi', $hocVien->hocVienProfile->dia_chi) }}</textarea>
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
                        <img id="avatar-preview" src="{{ $hocVien->avatar ? asset('storage/'.$hocVien->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($hocVien->name).'&size=128' }}" class="w-full h-full rounded-full object-cover border-4 border-purple-100 shadow-inner" alt="Avatar preview">
                    </div>
                    <label class="cursor-pointer bg-purple-50 text-purple-700 px-4 py-2 rounded-lg hover:bg-purple-100 transition duration-150">
                        <span>Thay đổi ảnh</span>
                        <input type="file" name="avatar" class="hidden" onchange="previewImage(this)">
                    </label>
                    <p class="text-xs text-gray-500 mt-2 text-center">Để trống nếu không muốn thay đổi ảnh</p>
                    @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition duration-150 mb-3">
                    Cập nhật thông tin
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
