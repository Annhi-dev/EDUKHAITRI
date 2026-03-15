@php
    $role = Auth::user()->role;
    $menuItems = [];

    if ($role === 'admin') {
        $menuItems = [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'dashboard'],
            ['label' => 'Quản lý người dùng', 'route' => '#', 'icon' => 'users'],
            ['label' => 'Thời khóa biểu', 'route' => '#', 'icon' => 'calendar'],
            ['label' => 'Đánh giá chất lượng', 'route' => '#', 'icon' => 'star'],
            ['label' => 'Khóa học & Lớp', 'route' => '#', 'icon' => 'book'],
            ['label' => 'Báo cáo', 'route' => '#', 'icon' => 'chart'],
            ['label' => 'Cấu hình hệ thống', 'route' => '#', 'icon' => 'cog'],
        ];
        $activeColor = 'bg-purple-600';
        $hoverColor = 'hover:bg-purple-100';
    } elseif ($role === 'giang_vien') {
        $menuItems = [
            ['label' => 'Dashboard', 'route' => 'gv.dashboard', 'icon' => 'dashboard'],
            ['label' => 'Lịch dạy', 'route' => '#', 'icon' => 'calendar'],
            ['label' => 'Quản lý lớp', 'route' => '#', 'icon' => 'users'],
            ['label' => 'Điểm danh', 'route' => '#', 'icon' => 'check'],
            ['label' => 'Quản lý điểm', 'route' => '#', 'icon' => 'academic-cap'],
            ['label' => 'Đánh giá học viên', 'route' => '#', 'icon' => 'star'],
        ];
        $activeColor = 'bg-green-600';
        $hoverColor = 'hover:bg-green-100';
    } elseif ($role === 'hoc_vien') {
        $menuItems = [
            ['label' => 'Dashboard', 'route' => 'hv.dashboard', 'icon' => 'dashboard'],
            ['label' => 'Lịch học', 'route' => '#', 'icon' => 'calendar'],
            ['label' => 'Kết quả học tập', 'route' => '#', 'icon' => 'academic-cap'],
            ['label' => 'Điểm danh', 'route' => '#', 'icon' => 'check'],
            ['label' => 'Đánh giá khóa học', 'route' => '#', 'icon' => 'star'],
        ];
        $activeColor = 'bg-blue-600';
        $hoverColor = 'hover:bg-blue-100';
    }
@endphp

<div class="w-1/4 bg-white overflow-hidden shadow-sm sm:rounded-lg mr-4">
    <div class="p-6 text-gray-900">
        <ul class="space-y-2">
            @foreach ($menuItems as $item)
                @php
                    $isActive = $item['route'] !== '#' && request()->routeIs($item['route']);
                @endphp
                <li>
                    <a href="{{ $item['route'] !== '#' ? route($item['route']) : '#' }}" 
                       class="block px-4 py-2 {{ $isActive ? $activeColor . ' text-white' : $hoverColor }} rounded transition duration-150">
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
            <hr class="my-4">
            <li>
                <a href="{{ route('profile.edit') }}" 
                   class="block px-4 py-2 {{ request()->routeIs('profile.edit') ? $activeColor . ' text-white' : $hoverColor }} rounded transition duration-150">
                    Hồ sơ cá nhân
                </a>
            </li>
        </ul>
    </div>
</div>
