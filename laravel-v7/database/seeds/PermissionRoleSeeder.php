<?php

use App\TaiKhoan;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::getQuery()->delete();
        Permission::getQuery()->delete();

        // Add Nhom Tai Khoan
        Role::getQuery()->insert([
            ['name' => 'Ban Học Tập', 'guard_name' => 'api'],
            ['name' => 'Ban Điều Hành', 'guard_name' => 'api'],
            ['name' => 'Phụ Trách Đội', 'guard_name' => 'api'],
            ['name' => 'Ban Sinh Hoạt', 'guard_name' => 'api'],
            ['name' => 'Ban Thông Tin', 'guard_name' => 'api'],
            ['name' => 'Ban Phụng Vụ', 'guard_name' => 'api'],
        ]);

        // Add Phan Quyen
        Permission::getQuery()->insert([
            [
                'name'       => 'Điểm Danh',
                'note'       => '- Chỉnh sửa Điểm Danh Chúa Nhật (Tất cả các lớp)
- Chỉnh sửa Điểm kiểm tra (Tất cả các lớp)
- Huynh Trưởng dạy chính chỉ có quyền cập nhật cho lớp của mình dạy',
                'guard_name' => 'api',
            ],
            [
                'name'       => 'Lớp Học',
                'note'       => '- Thêm, xóa, chỉnh sửa thông tin lớp học
- Viết thư mời

- Cập nhật danh sách huynh trưởng và học viên trong lớp
- Cập nhật ngày thêm sức, rước lễ cho 1 lớp',
                'guard_name' => 'api',
            ],
            [
                'name'       => 'Tài Khoản',
                'note'       => '- Chỉnh sửa thông tin Tài Khoản
- Thay đổi mật khẩu',
                'guard_name' => 'api',
            ],
            [
                'name'       => 'Tổng Kết Cuối Năm',
                'note'       => 'Chỉnh sửa thông tin xếp hạng',
                'guard_name' => 'api',
            ],
            [
                'name'       => 'Hệ Thống',
                'note'       => 'Chỉnh sửa thông tin khóa học, hệ số điểm',
                'guard_name' => 'api',
            ],
            [
                'name'       => 'Phân Quyền',
                'note'       => 'Phân quyền hệ thống, nhóm tài khoản',
                'guard_name' => 'api',
            ],
            [
                'name'       => 'Nhận xét cuối năm',
                'note'       => '- Chỉnh sửa nhận xét cuối năm (Tất cả các lớp)
- Huynh Trưởng dạy chính chỉ có quyền cập nhật cho lớp của mình',
                'guard_name' => 'api',
            ],
            [
                'name'       => 'Thiết Bị',
                'note'       => '- Quản lý thông tin thiết bị',
                'guard_name' => 'api',
            ],
        ]);

        // TaiKhoan::findOrFail('HT028')
        //     ->givePermissionTo(Permission::all())
        //     ->assignRole(Role::firstOrFail());
    }
}
