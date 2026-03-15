# Hệ thống Quản lý Giảng viên – Học viện (EDUKHAITRI)

Dự án quản lý đào tạo toàn diện với 3 vai trò: Admin, Giảng viên và Học viên.

## Công nghệ sử dụng
- Laravel 11
- Tailwind CSS & Alpine.js
- MySQL / SQLite
- Spatie Permission (Phân quyền)
- FullCalendar (Lịch học)
- Chart.js (Báo cáo biểu đồ)
- Maatwebsite/Excel & Barryvdh/DomPDF (Xuất dữ liệu)

## Hướng dẫn cài đặt

1. **Clone dự án:**
   ```bash
   git clone https://github.com/your-repo/edukhaitri.git
   cd edukhaitri
   ```

2. **Cài đặt phụ thuộc:**
   ```bash
   composer install
   npm install
   ```

3. **Cấu hình môi trường:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Chỉnh sửa file .env để kết nối Database.*

4. **Database & Seeding:**
   ```bash
   php artisan migrate
   php artisan db:seed --class=RolePermissionSeeder
   php artisan db:seed --class=UserSeeder
   ```

5. **Tạo link lưu trữ:**
   ```bash
   php artisan storage:link
   ```

6. **Build assets & Chạy dự án:**
   ```bash
   npm run build
   php artisan serve
   ```

## Tài khoản mặc định (Test)
- **Admin:** admin@academy.com / password
- **Giảng viên:** gv1@academy.com / password
- **Học viên:** hv1@academy.com / password

---
&copy; 2026 EDUKHAITRI. All rights reserved.
