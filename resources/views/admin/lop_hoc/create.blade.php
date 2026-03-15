@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.lop_hoc.index') }}" class="text-purple-600 hover:text-purple-800 flex items-center mb-2">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Quay lại danh sách
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Tạo Lớp học & Tự động tạo Lịch</h2>
</div>

<form action="{{ route('admin.lop_hoc.store') }}" method="POST" class="space-y-6" id="create-class-form">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Section 1: Thông tin lớp -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Thông tin lớp học</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Tên lớp học (*)</label>
                    <input type="text" name="ten_lop" value="{{ old('ten_lop') }}" required class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mã lớp (*)</label>
                    <input type="text" name="ma_lop" value="{{ old('ma_lop') }}" required placeholder="VD: L001" class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Thời lượng khóa học</label>
                    <select id="thoi_luong" class="w-full rounded-lg border-gray-300 bg-purple-50">
                        <option value="">Chọn thời lượng</option>
                        <option value="1">1 Tháng</option>
                        <option value="2">2 Tháng</option>
                        <option value="3">3 Tháng</option>
                        <option value="4">4 Tháng</option>
                        <option value="5">5 Tháng</option>
                        <option value="6">6 Tháng</option>
                        <option value="12">12 Tháng (1 Năm)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sĩ số tối đa</label>
                    <input type="number" name="si_so_toi_da" value="{{ old('si_so_toi_da', 30) }}" required class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Khóa học (*)</label>
                    <select name="khoa_hoc_id" required class="w-full rounded-lg border-gray-300">
                        <option value="">Chọn khóa học</option>
                        @foreach($khoaHocs as $kh)
                            <option value="{{ $kh->id }}">{{ $kh->ma_khoa_hoc }} - {{ $kh->ten_khoa_hoc }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Giảng viên phụ trách (*)</label>
                    <select name="giang_vien_id" required class="w-full rounded-lg border-gray-300">
                        <option value="">Chọn giảng viên</option>
                        @foreach($giangViens as $gv)
                            <option value="{{ $gv->id }}">{{ $gv->name }} ({{ $gv->giangVienProfile->ma_giang_vien }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ngày bắt đầu (*)</label>
                    <input type="date" name="ngay_bat_dau" id="ngay_bat_dau" value="{{ date('Y-m-d') }}" required class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ngày kết thúc (*)</label>
                    <input type="date" name="ngay_ket_thuc" id="ngay_ket_thuc" required class="w-full rounded-lg border-gray-300">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Phòng học</label>
                    <input type="text" name="phong_hoc" placeholder="VD: P.101" class="w-full rounded-lg border-gray-300">
                </div>
            </div>
        </div>

        <!-- Section 2: Cấu hình lịch -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Cấu hình lịch học tự động</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Chọn các thứ trong tuần (*)</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['2', '3', '4', '5', '6', '7', 'CN'] as $thu)
                            <label class="inline-flex items-center p-2 border rounded-lg cursor-pointer hover:bg-purple-50 transition">
                                <input type="checkbox" name="thu_trong_tuan[]" value="{{ $thu }}" class="hidden peer">
                                <span class="text-sm font-bold text-gray-600 peer-checked:text-purple-600">Thứ {{ $thu }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Thời lượng mỗi buổi</label>
                        <select id="thoi_luong_buoi" class="w-full rounded-lg border-gray-300 bg-indigo-50">
                            <option value="">Tự chọn</option>
                            <option value="45">45 phút (1 tiết)</option>
                            <option value="90">90 phút (2 tiết)</option>
                            <option value="120">120 phút (2 giờ)</option>
                            <option value="180">180 phút (3 giờ)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Giờ bắt đầu (*)</label>
                        <input type="time" name="gio_bat_dau" id="gio_bat_dau" value="07:30" required class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Giờ kết thúc (*)</label>
                        <input type="time" name="gio_ket_thuc" id="gio_ket_thuc" required class="w-full rounded-lg border-gray-300">
                    </div>
                </div>
                <div class="pt-4 border-t">
                    <button type="button" onclick="previewSchedules()" class="w-full bg-indigo-50 text-indigo-700 font-bold py-2 rounded-lg hover:bg-indigo-100 transition mb-3">
                        Xem trước danh sách buổi học
                    </button>
                    <button type="submit" class="w-full bg-purple-600 text-white font-bold py-3 rounded-lg shadow-lg hover:bg-purple-700 transition">
                        Tạo lớp & Lịch học
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal Preview -->
<div id="preview-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[80vh] overflow-hidden flex flex-col">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="font-bold text-lg">Xem trước lịch học</h3>
            <button onclick="closePreview()" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <div class="p-4 overflow-y-auto flex-1" id="preview-content">
            <!-- Content AJAX -->
        </div>
        <div class="p-4 border-t text-right">
            <button onclick="closePreview()" class="bg-gray-100 px-4 py-2 rounded-lg font-bold">Đóng</button>
        </div>
    </div>
</div>

<script>
    const ngayBatDauInput = document.getElementById('ngay_bat_dau');
    const ngayKetThucInput = document.getElementById('ngay_ket_thuc');
    const thoiLuongSelect = document.getElementById('thoi_luong');
    const gioBatDauInput = document.getElementById('gio_bat_dau');
    const gioKetThucInput = document.getElementById('gio_ket_thuc');
    const thoiLuongBuoiSelect = document.getElementById('thoi_luong_buoi');

    // === Xử lý Ngày ===
    function updateEndDate() {
        const startDateVal = ngayBatDauInput.value;
        const months = parseInt(thoiLuongSelect.value);
        if (startDateVal && months) {
            const date = new Date(startDateVal);
            date.setMonth(date.getMonth() + months);
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            ngayKetThucInput.value = `${y}-${m}-${d}`;
            validateDates();
        }
    }

    function validateDates() {
        const start = new Date(ngayBatDauInput.value);
        const end = new Date(ngayKetThucInput.value);
        if (ngayBatDauInput.value && ngayKetThucInput.value) {
            if (end <= start) {
                ngayKetThucInput.setCustomValidity('Ngày kết thúc phải lớn hơn ngày bắt đầu');
                ngayKetThucInput.reportValidity();
            } else {
                ngayKetThucInput.setCustomValidity('');
            }
        }
    }

    // === Xử lý Giờ ===
    function updateEndTime() {
        const startTimeVal = gioBatDauInput.value;
        const minutes = parseInt(thoiLuongBuoiSelect.value);
        if (startTimeVal && minutes) {
            const [hours, mins] = startTimeVal.split(':').map(Number);
            const date = new Date();
            date.setHours(hours, mins + minutes);
            const h = String(date.getHours()).padStart(2, '0');
            const m = String(date.getMinutes()).padStart(2, '0');
            gioKetThucInput.value = `${h}:${m}`;
            validateTimes();
        }
    }

    function validateTimes() {
        if (gioBatDauInput.value && gioKetThucInput.value) {
            if (gioKetThucInput.value <= gioBatDauInput.value) {
                gioKetThucInput.setCustomValidity('Giờ kết thúc phải sau giờ bắt đầu');
                gioKetThucInput.reportValidity();
            } else {
                gioKetThucInput.setCustomValidity('');
            }
        }
    }

    // Listeners Ngày
    ngayBatDauInput.addEventListener('change', () => {
        if (thoiLuongSelect.value) updateEndDate();
        else validateDates();
    });
    thoiLuongSelect.addEventListener('change', updateEndDate);
    ngayKetThucInput.addEventListener('change', validateDates);

    // Listeners Giờ
    gioBatDauInput.addEventListener('change', () => {
        if (thoiLuongBuoiSelect.value) updateEndTime();
        else validateTimes();
    });
    thoiLuongBuoiSelect.addEventListener('change', updateEndTime);
    gioKetThucInput.addEventListener('change', validateTimes);

    function previewSchedules() {
        const previewContent = document.getElementById('preview-content');
        previewContent.innerHTML = '<p class="text-center py-4 text-gray-500 italic">Đang tính toán lịch...</p>';
        document.getElementById('preview-modal').classList.remove('hidden');

        fetch('{{ route("admin.lop_hoc.preview") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                ngay_bat_dau: ngayBatDauInput.value,
                ngay_ket_thuc: ngayKetThucInput.value,
                thu_trong_tuan: Array.from(document.querySelectorAll('input[name="thu_trong_tuan[]"]:checked')).map(el => el.value)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                previewContent.innerHTML = '<p class="text-center py-4 text-red-500">Không có buổi học nào được tạo. Vui lòng kiểm tra lại ngày và các thứ đã chọn.</p>';
                return;
            }
            let html = '<table class="w-full text-left border-collapse"><thead class="bg-gray-50"><tr><th class="p-2 border">STT</th><th class="p-2 border">Ngày học</th><th class="p-2 border">Thứ</th></tr></thead><tbody>';
            data.forEach((item, index) => {
                html += `<tr><td class="p-2 border">${index + 1}</td><td class="p-2 border">${item.ngay}</td><td class="p-2 border">${item.thu}</td></tr>`;
            });
            html += '</tbody></table>';
            previewContent.innerHTML = html;
        });
    }

    function closePreview() {
        document.getElementById('preview-modal').classList.add('hidden');
    }
</script>

<style>
    input[type="checkbox"]:checked + span {
        color: #7c3aed;
        background-color: #f5f3ff;
        border-color: #7c3aed;
    }
</style>
@endsection
