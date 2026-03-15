@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Đánh giá Giảng viên</h2>
        <p class="text-sm text-gray-600">Tổng hợp và đánh giá chất lượng giảng dạy</p>
    </div>
    <form action="{{ route('admin.danh_gia.giang_vien') }}" method="GET" class="flex space-x-2">
        <select name="ky_hoc" class="rounded-lg border-gray-300 text-sm">
            <option value="1" {{ $kyHoc == 1 ? 'selected' : '' }}>Kỳ 1</option>
            <option value="2" {{ $kyHoc == 2 ? 'selected' : '' }}>Kỳ 2</option>
        </select>
        <select name="nam_hoc" class="rounded-lg border-gray-300 text-sm">
            @for($y = date('Y'); $y >= 2023; $y--)
                <option value="{{ $y }}" {{ $namHoc == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm">Lọc</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Giảng viên</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Điểm từ HV</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Điểm chuyên môn</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Tổng điểm</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Xếp loại</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($giangViens as $gv)
                @php $dg = $danhGias[$gv->id] ?? null; @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $gv->name }}</div>
                        <div class="text-xs text-gray-500">{{ $gv->giangVienProfile->ma_giang_vien }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-blue-600">{{ $dg ? number_format($dg->diem_tb_tu_hoc_vien, 1) : '---' }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-purple-600">{{ $dg ? number_format($dg->diem_chuyen_mon, 1) : '---' }}</td>
                    <td class="px-6 py-4 text-sm font-bold">{{ $dg ? number_format($dg->diem_tong, 1) : '---' }}</td>
                    <td class="px-6 py-4">
                        @if($dg)
                            <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-purple-100 text-purple-700 uppercase">{{ $dg->xep_loai }}</span>
                        @else
                            <span class="text-gray-400 italic text-xs">Chưa đánh giá</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="openModal({{ $gv->id }}, '{{ $gv->name }}', {{ $dg ? $dg->diem_chuyen_mon : 0 }}, '{{ $dg ? $dg->nhan_xet_admin : '' }}')" class="text-purple-600 font-bold hover:underline">Đánh giá</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Đánh giá -->
<div id="eval-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="font-bold text-lg mb-1">Đánh giá Giảng viên</h3>
        <p id="modal-gv-name" class="text-purple-600 font-bold mb-4"></p>
        <form id="eval-form" method="POST">
            @csrf
            <input type="hidden" name="ky_hoc" value="{{ $kyHoc }}">
            <input type="hidden" name="nam_hoc" value="{{ $namHoc }}">
            
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">Điểm chuyên môn (0-10)</label>
                <input type="number" name="diem_chuyen_mon" id="modal-diem" step="0.1" min="0" max="10" required class="w-full rounded-lg border-gray-300">
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">Nhận xét từ Admin</label>
                <textarea name="nhan_xet_admin" id="modal-nhan-xet" class="w-full rounded-lg border-gray-300" rows="3"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 font-bold">Hủy</button>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg font-bold">Lưu kết quả</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id, name, diem, nhanXet) {
        document.getElementById('modal-gv-name').innerText = name;
        document.getElementById('modal-diem').value = diem;
        document.getElementById('modal-nhan-xet').value = nhanXet;
        document.getElementById('eval-form').action = `/admin/danh-gia/giang-vien/${id}`;
        document.getElementById('eval-modal').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('eval-modal').classList.add('hidden');
    }
</script>
@endsection
