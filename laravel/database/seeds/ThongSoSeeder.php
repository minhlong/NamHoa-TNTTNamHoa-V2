<?php

use Illuminate\Database\Seeder;

class ThongSoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('thong_so')->delete();

        $this->DiHoc();
        $this->command->info('Create fake data for Di Hoc! Finish');
        $this->DiLe();
        $this->command->info('Create fake data for Di Le! Finish');
        $this->NganhHoc();
        $this->command->info('Create fake data for Nganh Hoc! Finish');
        $this->CapHoc();
        $this->command->info('Create fake data for Cap Hoc! Finish');
        $this->DoiHoc();
        $this->command->info('Create fake data for Doi Hoc! Finish');
        $ngayBatDau = date('Y-m-d');
        \App\KhoaHoc::hienTaiHoacTaoMoi($ngayBatDau);
        \App\KhoaHoc::hienTaiHoacTaoMoi(date('Y-m-d', strtotime("$ngayBatDau -1 year -1 day")));
        $this->command->info('Create fake data for Khoa Hoc! Finish');
    }

    /**
     * @param $arr
     * @param $loaiDuLieu
     */
    public function InsertData($arr, $loaiDuLieu)
    {
        $iCounter = 0;
        foreach ($arr as $id => $value) {
            \App\ThongSo::create([
                'loai_du_lieu' => $loaiDuLieu,
                'trang_thai' => 'HIEN_THI',
                'ky_hieu' => $id,
                'ten' => $value,
                'thu_tu' => $iCounter++,
            ]);
        }
    }

    public function DiHoc()
    {
        $arr = [
            'K' => 'Vắng Không Phép',
            'P' => 'Vắng Có Phép',
        ];

        $this->InsertData($arr, 'DI_HOC');
    }

    public function DiLe()
    {
        $arr = [
            'T' => 'Phiếu Trắng',
            'H' => 'Phiếu Hồng',
            'K' => 'Vắng Không Phép',
            'P' => 'Vắng Có Phép',
        ];

        $this->InsertData($arr, 'DI_LE');
    }

    public function NganhHoc()
    {
        $arr = [
            'AU_NHI' => 'Ấu Nhi',
            'THIEU_NHI' => 'Thiếu Nhi',
            'NGHIA_SI' => 'Nghĩa Sĩ',
            'HT_DU_BI' => 'Huynh Trưởng Dự Bị',
        ];

        $this->InsertData($arr, 'NGANH');
    }

    public function CapHoc()
    {
        $arr = [
            'CHIEN_CON' => 'Chiên Con',
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
        ];

        $this->InsertData($arr, 'CAP');
    }

    public function DoiHoc()
    {
        $arr = [
            1 => 1,
            2 => 2,
            3 => 3,
        ];

        $this->InsertData($arr, 'DOI');
    }
}
