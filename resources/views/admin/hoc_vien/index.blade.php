@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Quản lý Học viên</h2>
        <p class="text-sm text-gray-600">Danh sách tất cả học viên trong hệ thống</p>
    </div>
    <a href="{{ route('admin.hoc_vien.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center shadow-md transition duration-150">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Thêm học viên
    </a>
</div>

<!-- Bộ lọc -->
<div class="bg-white p-6 rounded-xl shadow-sm mb-6">
    <form action="{{ route('admin.hoc_vien.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
            <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm theo tên, email, mã HV..." class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
        </div>
        <div>
            <label for="trang_thai" class="block text-sm font-medium text-gray-700 mb-1">Tình trạng</label>
            <select name="trang_thai" id="trang_thai" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                <option value="">Tất cả tình trạng</option>
                <option value="dang_hoc" {{ ($filters['trang_thai'] ?? '') == 'dang_hoc' ? 'selected' : '' }}>Đang học</option>
                <option value="bao_luu" {{ ($filters['trang_thai'] ?? '') == 'bao_luu' ? 'selected' : '' }}>Bảo lưu</option>
                <option value="da_tot_nghiep" {{ ($filters['trang_thai'] ?? '') == 'da_tot_nghiep' ? 'selected' : '' }}>Đã tốt nghiệp</option>
                <option value="da_nghi" {{ ($filters['trang_thai'] ?? '') == 'da_nghi' ? 'selected' : '' }}>Đã nghỉ</option>
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg transition duration-150 flex-1">Tìm kiếm</button>
            <a href="{{ route('admin.hoc_vien.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition duration-150">Xóa</a>
        </div>
    </form>
</div>

<!-- Bảng dữ liệu -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã HV</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Học viên</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Trường TN</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ngày nhập học</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tình trạng</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($hocViens as $hv)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-bold text-purple-600">{{ $hv->hocVienProfile->ma_hoc_vien ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img class="h-10 w-10 rounded-full object-cover mr-3 border border-gray-200" src="{{ $hv->avatar ? asset('storage/'.$hv->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($hv->name) }}" alt="{{ $hv->name }}">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $hv->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $hv->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $hv->hocVienProfile->truong_tot_nghiep ?? 'Chưa cập nhật' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $hv->hocVienProfile->ngay_nhap_hoc ? \Carbon\Carbon::parse($hv->hocVienProfile->ngay_nhap_hoc)->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $status = $hv->hocVienProfile->trang_thai ?? 'dang_hoc';
                                $colors = [
                                    'dang_hoc' => 'bg-blue-100 text-blue-800',
                                    'bao_luu' => 'bg-yellow-100 text-yellow-800',
                                    'da_tot_nghiep' => 'bg-green-100 text-green-800',
                                    'da_nghi' => 'bg-red-100 text-red-800',
                                ];
                                $labels = [
                                    'dang_hoc' => 'Đang học',
                                    'bao_luu' => 'Bảo lưu',
                                    'da_tot_nghiep' => 'Đã tốt nghiệp',
                                    'da_nghi' => 'Đã nghỉ',
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$status] ?? 'bg-gray-100' }}">
                                {{ $labels[$status] ?? 'Không rõ' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.hoc_vien.show', $hv->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-2 rounded-lg transition duration-150" title="Chi tiết">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('admin.hoc_vien.edit', $hv->id) }}" class="text-amber-600 hover:text-amber-900 bg-amber-50 p-2 rounded-lg transition duration-150" title="Chỉnh sửa">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                </a>
                                <form action="{{ route('admin.hoc_vien.reset_password', $hv->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn reset mật khẩu của học viên này về password123?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 p-2 rounded-lg transition duration-150" title="Reset mật khẩu">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                    </button>
                                </form>
                                <form action="{{ route('admin.hoc_vien.destroy', $hv->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa học viên này?')">
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
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            Không tìm thấy học viên nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $hocViens->appends($filters)->links() }}
    </div>
</div>
@endsection
