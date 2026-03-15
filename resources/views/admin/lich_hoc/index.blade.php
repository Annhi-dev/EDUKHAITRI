@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Thời khóa biểu</h2>
        <p class="text-sm text-gray-600">Quản lý lịch học toàn trung tâm</p>
    </div>
    <div x-data="{ view: 'table' }" class="bg-gray-200 p-1 rounded-lg flex">
        <button @click="view = 'table'; document.getElementById('table-view').classList.remove('hidden'); document.getElementById('calendar-view').classList.add('hidden')" :class="view === 'table' ? 'bg-white shadow-sm' : ''" class="px-4 py-1 rounded-md text-sm font-bold transition">Bảng</button>
        <button @click="view = 'calendar'; document.getElementById('calendar-view').classList.remove('hidden'); document.getElementById('table-view').classList.add('hidden'); renderCalendar()" :class="view === 'calendar' ? 'bg-white shadow-sm' : ''" class="px-4 py-1 rounded-md text-sm font-bold transition">Lịch</button>
    </div>
</div>

<!-- Table View -->
<div id="table-view" class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-4 border-b bg-gray-50">
        <form action="{{ route('admin.lich_hoc.index') }}" method="GET" class="flex gap-4">
            <select name="lop_hoc_id" class="rounded-lg border-gray-300 text-sm">
                <option value="">-- Tất cả lớp --</option>
                @foreach($lopHocs as $lop)
                    <option value="{{ $lop->id }}" {{ request('lop_hoc_id') == $lop->id ? 'selected' : '' }}>{{ $lop->ma_lop }} - {{ $lop->ten_lop }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-bold">Lọc</button>
        </form>
    </div>
    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Ngày</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Thứ</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Lớp</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Giảng viên</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Giờ</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Phòng</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($lichHocs as $lich)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ \Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">Thứ {{ $lich->thu_trong_tuan }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $lich->lopHoc->ma_lop }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $lich->lopHoc->giangVien->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ substr($lich->gio_bat_dau, 0, 5) }} - {{ substr($lich->gio_ket_thuc, 0, 5) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $lich->phong_hoc ?? '---' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-gray-100 uppercase">{{ $lich->trang_thai }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t">
        {{ $lichHocs->links() }}
    </div>
</div>

<!-- Calendar View -->
<div id="calendar-view" class="bg-white rounded-xl shadow-sm p-6 hidden">
    <div id="calendar"></div>
</div>

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<script>
    let calendar = null;
    function renderCalendar() {
        if (calendar) return;
        
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'vi',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: '{{ route("admin.lich_hoc.events") }}',
            eventClick: function(info) {
                alert('Lớp: ' + info.event.title + '\nPhòng: ' + info.event.extendedProps.phong_hoc + '\nTrạng thái: ' + info.event.extendedProps.trang_thai);
            }
        });
        calendar.render();
    }
</script>

<style>
    .fc-event { cursor: pointer; padding: 2px 4px; font-size: 0.85em; }
    .fc-toolbar-title { font-size: 1.25em !important; font-weight: bold; color: #4b5563; }
</style>
@endsection
