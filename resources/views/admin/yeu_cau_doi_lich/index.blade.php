@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Yêu cầu đổi lịch</h2>
        <p class="text-sm text-gray-600">Duyệt hoặc từ chối các yêu cầu từ giảng viên</p>
    </div>
</div>

<div x-data="{ tab: 'cho_duyet' }" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex border-b border-gray-100 bg-gray-50">
        <button @click="tab = 'cho_duyet'" :class="tab === 'cho_duyet' ? 'border-purple-600 text-purple-600 bg-white' : ''" class="px-6 py-4 text-sm font-bold border-b-2 border-transparent transition duration-150">Chờ duyệt</button>
        <button @click="tab = 'da_duyet'" :class="tab === 'da_duyet' ? 'border-purple-600 text-purple-600 bg-white' : ''" class="px-6 py-4 text-sm font-bold border-b-2 border-transparent transition duration-150">Đã duyệt</button>
        <button @click="tab = 'tu_choi'" :class="tab === 'tu_choi' ? 'border-purple-600 text-purple-600 bg-white' : ''" class="px-6 py-4 text-sm font-bold border-b-2 border-transparent transition duration-150">Đã từ chối</button>
    </div>

    <div class="p-6">
        <div x-show="tab === 'cho_duyet'">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider border-b">
                        <th class="pb-3">Giảng viên</th>
                        <th class="pb-3">Lớp</th>
                        <th class="pb-3">Lịch gốc</th>
                        <th class="pb-3">Lịch mới</th>
                        <th class="pb-3">Lý do</th>
                        <th class="pb-3 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($yeuCaus->where('trang_thai', 'cho_duyet') as $yc)
                        <tr>
                            <td class="py-4">
                                <div class="font-medium text-gray-900">{{ $yc->giangVien->name }}</div>
                            </td>
                            <td class="py-4 text-sm">{{ $yc->lichHoc->lopHoc->ma_lop }}</td>
                            <td class="py-4 text-xs">
                                <div>{{ \Carbon\Carbon::parse($yc->lichHoc->ngay_hoc)->format('d/m/Y') }}</div>
                                <div class="text-gray-500">{{ substr($yc->lichHoc->gio_bat_dau, 0, 5) }} - {{ substr($yc->lichHoc->gio_ket_thuc, 0, 5) }}</div>
                            </td>
                            <td class="py-4 text-xs font-bold text-purple-600">
                                <div>{{ \Carbon\Carbon::parse($yc->ngay_muon_doi)->format('d/m/Y') }}</div>
                                <div>{{ substr($yc->gio_bat_dau_moi, 0, 5) }} - {{ substr($yc->gio_ket_thuc_moi, 0, 5) }}</div>
                                <div class="text-gray-500 font-normal">Phòng: {{ $yc->phong_hoc_moi ?? '---' }}</div>
                            </td>
                            <td class="py-4 text-sm text-gray-600 max-w-xs truncate">{{ $yc->ly_do }}</td>
                            <td class="py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    <form action="{{ route('admin.yeu_cau.duyet', $yc->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-xs font-bold">Duyệt</button>
                                    </form>
                                    <button onclick="openRejectModal({{ $yc->id }})" class="bg-red-500 text-white px-3 py-1 rounded text-xs font-bold">Từ chối</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-gray-500">Không có yêu cầu nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Tab Da Duyet & Tu Choi similar structure -->
        <div x-show="tab === 'da_duyet'" class="hidden">
             <!-- Simplified for now -->
             <p class="text-center py-10 text-gray-500">Danh sách các yêu cầu đã được xử lý.</p>
        </div>
        <div x-show="tab === 'tu_choi'" class="hidden">
             <p class="text-center py-10 text-gray-500">Danh sách các yêu cầu bị từ chối.</p>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="font-bold text-lg mb-4 text-red-600">Từ chối yêu cầu đổi lịch</h3>
        <form id="reject-form" method="POST">
            @csrf @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">Lý do từ chối</label>
                <textarea name="ghi_chu_admin" required class="w-full rounded-lg border-gray-300" rows="3"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-gray-600 font-bold">Hủy</button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold">Xác nhận từ chối</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(id) {
        const form = document.getElementById('reject-form');
        form.action = `/admin/yeu-cau-doi-lich/${id}/tu-choi`;
        document.getElementById('reject-modal').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('reject-modal').classList.add('hidden');
    }
</script>
@endsection
