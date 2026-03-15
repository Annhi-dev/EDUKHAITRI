@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Quản lý Giảng viên</h2>
        <p class="text-sm text-gray-600">Danh sách tất cả giảng viên trong hệ thống</p>
    </div>
    <a href="{{ route('admin.giang_vien.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center shadow-md transition duration-150">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Thêm giảng viên
    </a>
</div>

<!-- Bộ lọc -->
<div class="bg-white p-6 rounded-xl shadow-sm mb-6">
    <form action="{{ route('admin.giang_vien.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
            <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm theo tên, email, mã GV..." class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
        </div>
        <div>
            <label for="trang_thai" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="trang_thai" id="trang_thai" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                <option value="">Tất cả trạng thái</option>
                <option value="dang_day" {{ ($filters['trang_thai'] ?? '') == 'dang_day' ? 'selected' : '' }}>Đang dạy</option>
                <option value="nghi_phep" {{ ($filters['trang_thai'] ?? '') == 'nghi_phep' ? 'selected' : '' }}>Nghỉ phép</option>
                <option value="da_nghi" {{ ($filters['trang_thai'] ?? '') == 'da_nghi' ? 'selected' : '' }}>Đã nghỉ</option>
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg transition duration-150 flex-1">Tìm kiếm</button>
            <a href="{{ route('admin.giang_vien.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition duration-150">Xóa</a>
        </div>
    </form>
</div>

<!-- Bảng dữ liệu -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã GV</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Giảng viên</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Chuyên môn</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Học vị</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kích hoạt</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($giangViens as $gv)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-bold text-purple-600">{{ $gv->giangVienProfile->ma_giang_vien ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img class="h-10 w-10 rounded-full object-cover mr-3 border border-gray-200" src="{{ $gv->avatar ? asset('storage/'.$gv->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($gv->name) }}" alt="{{ $gv->name }}">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $gv->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $gv->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $gv->giangVienProfile->chuyen_mon ?? 'Chưa cập nhật' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $gv->giangVienProfile->hoc_vi ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $status = $gv->giangVienProfile->trang_thai ?? 'dang_day';
                                $colors = [
                                    'dang_day' => 'bg-green-100 text-green-800',
                                    'nghi_phep' => 'bg-yellow-100 text-yellow-800',
                                    'da_nghi' => 'bg-red-100 text-red-800',
                                ];
                                $labels = [
                                    'dang_day' => 'Đang dạy',
                                    'nghi_phep' => 'Nghỉ phép',
                                    'da_nghi' => 'Đã nghỉ',
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$status] ?? 'bg-gray-100' }}">
                                {{ $labels[$status] ?? 'Không rõ' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('admin.giang_vien.toggle', $gv->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $gv->is_active ? 'bg-purple-600' : 'bg-gray-200' }}">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $gv->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.giang_vien.show', $gv->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-2 rounded-lg transition duration-150" title="Chi tiết">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('admin.giang_vien.edit', $gv->id) }}" class="text-amber-600 hover:text-amber-900 bg-amber-50 p-2 rounded-lg transition duration-150" title="Chỉnh sửa">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                </a>
                                <form action="{{ route('admin.giang_vien.reset_password', $gv->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn reset mật khẩu của giảng viên này về password123?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 p-2 rounded-lg transition duration-150" title="Reset mật khẩu">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                    </button>
                                </form>
                                <form action="{{ route('admin.giang_vien.destroy', $gv->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa giảng viên này? Thao tác này sẽ đánh dấu tài khoản là không hoạt động.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded-lg transition duration-150" title="Xóa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            Không tìm thấy giảng viên nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $giangViens->appends($filters)->links() }}
    </div>
</div>
@endsection
