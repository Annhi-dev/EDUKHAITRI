@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex items-center">
        <a href="{{ route('admin.lop_hoc.index') }}" class="text-purple-600 hover:text-purple-800 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Chi tiết Lớp học: {{ $lop_hoc->ten_lop }}</h2>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Cột trái: Thông tin lớp -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 text-purple-700">Thông tin chung</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase">Mã lớp</label>
                    <p class="font-mono font-bold text-lg text-purple-600">{{ $lop_hoc->ma_lop }}</p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase">Khóa học</label>
                    <p class="font-medium">{{ $lop_hoc->khoaHoc->ten_khoa_hoc }}</p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase">Giảng viên</label>
                    <p class="font-medium">{{ $lop_hoc->giangVien->name }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase">Sĩ số</label>
                        <p class="font-medium">{{ $lop_hoc->hocViens->count() }} / {{ $lop_hoc->si_so_toi_da }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase">Trạng thái</label>
                        <div>
                            <span class="px-2 py-1 text-xs font-bold rounded-full {{ $lop_hoc->trang_thai == 'dang_hoc' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $lop_hoc->trang_thai == 'dang_hoc' ? 'Đang học' : 'Sắp mở' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase">Ngày bắt đầu</label>
                        <p class="font-medium text-sm">{{ \Carbon\Carbon::parse($lop_hoc->ngay_bat_dau)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase">Ngày kết thúc</label>
                        <p class="font-medium text-sm">{{ $lop_hoc->ngay_ket_thuc ? \Carbon\Carbon::parse($lop_hoc->ngay_ket_thuc)->format('d/m/Y') : 'Chưa xác định' }}</p>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase">Phòng học mặc định</label>
                    <p class="font-medium">{{ $lop_hoc->phong_hoc ?? 'Chưa gán' }}</p>
                </div>
            </div>
        </div>

        <!-- Form thêm học viên -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 text-purple-700">Thêm học viên vào lớp</h3>
            <form action="{{ route('admin.lop_hoc.add_hv', $lop_hoc->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-1">Chọn học viên</label>
                    <select name="hoc_vien_id" required class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">-- Danh sách học viên --</option>
                        @foreach(\App\Models\User::role('hoc_vien')->where('is_active', true)->get() as $hv)
                            <option value="{{ $hv->id }}">{{ $hv->name }} ({{ $hv->hocVienProfile->ma_hoc_vien ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-purple-600 text-white font-bold py-2 rounded-lg hover:bg-purple-700 transition">
                    Thêm vào lớp
                </button>
            </form>
        </div>
    </div>

    <!-- Cột phải: Học viên & Lịch học -->
    <div class="lg:col-span-2 space-y-6">
        <div x-data="{ tab: 'students' }" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex border-b border-gray-100">
                <button @click="tab = 'students'" :class="{ 'border-purple-600 text-purple-600 bg-purple-50': tab === 'students' }" class="px-6 py-4 text-sm font-bold border-b-2 border-transparent hover:bg-gray-50 transition duration-150">Danh sách học viên</button>
                <button @click="tab = 'schedule'" :class="{ 'border-purple-600 text-purple-600 bg-purple-50': tab === 'schedule' }" class="px-6 py-4 text-sm font-bold border-b-2 border-transparent hover:bg-gray-50 transition duration-150">Lịch học chi tiết</button>
            </div>

            <div class="p-6">
                <!-- Tab: Học viên -->
                <div x-show="tab === 'students'">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider border-b">
                                <th class="pb-3">Mã HV</th>
                                <th class="pb-3">Họ tên</th>
                                <th class="pb-3">Ngày tham gia</th>
                                <th class="pb-3 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($lop_hoc->hocViens as $hv)
                                <tr>
                                    <td class="py-3 font-mono text-sm">{{ $hv->hocVienProfile->ma_hoc_vien ?? 'N/A' }}</td>
                                    <td class="py-3 font-medium text-gray-900 text-sm">{{ $hv->name }}</td>
                                    <td class="py-3 text-gray-500 text-sm">{{ \Carbon\Carbon::parse($hv->pivot->ngay_tham_gia)->format('d/m/Y') }}</td>
                                    <td class="py-3 text-right">
                                        <form action="{{ route('admin.lop_hoc.remove_hv', ['lopId' => $lop_hoc->id, 'hvId' => $hv->id]) }}" method="POST" onsubmit="return confirm('Xóa học viên khỏi lớp?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">✕</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-gray-500">Chưa có học viên nào trong lớp này.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Tab: Lịch học -->
                <div x-show="tab === 'schedule'">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider border-b">
                                <th class="pb-3">Ngày học</th>
                                <th class="pb-3">Thứ</th>
                                <th class="pb-3">Giờ học</th>
                                <th class="pb-3">Phòng</th>
                                <th class="pb-3">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($lop_hoc->lichHocs as $lich)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 text-sm font-medium">{{ \Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m/Y') }}</td>
                                    <td class="py-3 text-sm">Thứ {{ $lich->thu_trong_tuan }}</td>
                                    <td class="py-3 text-sm">{{ substr($lich->gio_bat_dau, 0, 5) }} - {{ substr($lich->gio_ket_thuc, 0, 5) }}</td>
                                    <td class="py-3 text-sm text-gray-600">{{ $lich->phong_hoc ?? '---' }}</td>
                                    <td class="py-3">
                                        @php
                                            $stColor = match($lich->trang_thai) {
                                                'da_len_lich' => 'text-blue-600',
                                                'hoan_thanh' => 'text-green-600',
                                                'huy' => 'text-red-600',
                                                'doi_lich' => 'text-yellow-600',
                                                default => 'text-gray-600'
                                            };
                                        @endphp
                                        <span class="text-xs font-bold {{ $stColor }}">{{ strtoupper($lich->trang_thai) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
