<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SynSeeder extends Seeder
{
    private $connection;

    public function __construct()
    {
        // TODO: Implement __construct() method.
        $this->connection = DB::connection('mysql_old');
    }

    /**
     * Run the database seeds.
     */
    public function run()
    {
        Model::unguard();
        $this->call('ThongSoSeeder');
        $this->call('PhanQuyenSeeder');

        $this->generateTaiKhoan();

        $admin = \App\TaiKhoan::whereHoVaTen('Hồ Minh Long')->first();
        $banhoctap = \App\NhomTaiKhoan::whereTenHienThi('Ban Học Tập')->first();
        $admin->nhom_tai_khoan()->attach($banhoctap->id);

        $this->generateChuyenCanHocLuc();
        $this->generateLopHoc();
    }

    private function generateChuyenCanHocLuc()
    {
        $this->command->info('Clear Data Chuyen Can and Hoc Luc!');
        DB::table('diem_danh')->delete();
        DB::table('diem_so')->delete();

        $diem_danh = $this->connection->table('user_score')
            ->get();

        $stmpDataChuyenCan = [];
        foreach ($diem_danh as $item) {
            if ($item->type == 'CHUYEN_CAN') {
                $stmpDataChuyenCan[ $item->user_id ][ $item->date ]['created_at'] = $item->created_at;
                $stmpDataChuyenCan[ $item->user_id ][ $item->date ]['updated_at'] = $item->updated_at;
                switch ($item->scoretype) {
                    case 'LE':
                        $stmpDataChuyenCan[ $item->user_id ][ $item->date ] ['di_le'] = $item->score;
                        break;
                    case 'HOC':
                        $stmpDataChuyenCan[ $item->user_id ][ $item->date ] ['di_hoc'] = $item->score;
                        break;
                }
            } elseif ($item->type == 'HOC_LUC') {
                \App\DiemSo::create([
                    'tai_khoan_id' => $item->user_id,
                    'khoa_hoc_id' => 2014,
                    'dot' => str_replace('DOT_', '', $item->scoretype),
                    'lan' => str_replace('LAN_', '', $item->data),
                    'diem' => str_replace(',', '.', $item->score),
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ]);
            }
        }

        foreach ($stmpDataChuyenCan as $userid => $arrUser) {
            foreach ($arrUser as $date => $item) {
                $newItem = \App\DiemDanh::create([
                    'tai_khoan_id' => $userid,
                    'ngay' => $date,
                    'di_le' => @$item['di_le'],
                    'di_hoc' => @$item['di_hoc'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                ]);
            }
        }
    }

    private function generateLopHoc()
    {
        $this->command->info('Clear Data Lop Hoc!');
        DB::table('lop_hoc')->delete();
        DB::table('taikhoan_lophoc')->delete();

        $lop_hoc = $this->connection->table('classes')
            ->get();

        foreach ($lop_hoc as $item) {
            if ($item->team == 'HUYNH_TRUONG_DU_BI') {
                $item->team = 'HT_DU_BI';
            }
            $newItem = \App\LopHoc::create([
                'khoa_hoc_id' => $item->course,
                'nganh' => $item->team,
                'cap' => $item->level,
                'doi' => $item->crew,
                'vi_tri_hoc' => $item->location,
                'tro_giang' => $item->note ? [$item->note] : null,
                'tai_khoan_cap_nhat' => $item->updater,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);

            $user_class = $this->connection->table('user_class')
                ->where('class_id', $item->id)
                ->get();

            foreach ($user_class as $item) {
                switch ($item->rank) {
                    case 'O_LAI':
                        $item->rank = 'O_LAI_LOP';
                        break;
                    case 'HANG_I':
                    case 'HANG_II':
                    case 'HANG_III':
                        $item->rank = str_replace('HANG_', '', $item->rank);
                        break;
                }

                $newItem->hoc_vien()->attach($item->user_id, [
                    'chuyen_can' => $item->chuyencan,
                    'hoc_luc' => $item->hocluc,
                    'xep_hang' => $item->rank,
                    'ghi_chu' => $item->note,
                    'tai_khoan_cap_nhat' => $item->updater,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ]);
            }

            $newItem->tinhTongKet();
        }
    }

    private function generateTaiKhoan()
    {
        $this->command->info('Clear Data Tai Khoan!');
        DB::table('tai_khoan')->delete();
        DB::table('than_nhan')->delete();
        DB::table('gia_pha')->delete();

        $this->command->info('Create Tai Khoan!');
        $users = $this->connection->table('user')
            ->where('id', '<>', 'admin')
            ->get();

        foreach ($users as $item) {
            $newItem = \App\TaiKhoan::create([
                'id' => $item->id,
                'trang_thai' => 'HOAT_DONG',
                'mat_khau' => $item->passwd,
                'loai_tai_khoan' => $item->usertype,
                'ten_thanh' => $item->saint,
                'ho_va_ten' => $item->username,
                'gioi_tinh' => $item->gender ? 'NAM' : 'NU',
                'ngay_sinh' => $item->birthday,
                'ngay_rua_toi' => $item->baptism_date,
                'ngay_ruoc_le' => $item->eucharist_date,
                'ngay_them_suc' => $item->confirmation_date,
                'email' => $item->email,
                'dien_thoai' => $item->phone,
                'dia_chi' => $item->address,
                'giao_ho' => $item->parish,
                'ghi_chu' => $item->note,
                'remember_token' => $item->remember_token,
                'tai_khoan_cap_nhat' => $item->updater,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);

            $user_dear = $this->connection->table('user_dear')
                ->where('user_id', $item->id)
                ->get();

            foreach ($user_dear as $item) {
                $newItem->luuThanNhan([
                    'loai_quan_he' => $item->relationship,
                    'ho_va_ten' => $item->saint.' '.$item->dearname,
                    'dien_thoai' => $item->phone,
                    'ghi_chu' => $item->address,
                    'tai_khoan_cap_nhat' => $item->updater,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ]);
            }
        }
    }
}
