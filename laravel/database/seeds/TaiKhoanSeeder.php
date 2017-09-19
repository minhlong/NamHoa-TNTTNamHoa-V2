<?php

use Illuminate\Database\Seeder;

class TaiKhoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('tai_khoan')->delete();
        DB::table('than_nhan')->delete();
        DB::table('gia_pha')->delete();
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 50; ++$i) {
            $this->createTaiKhoan($faker);
        }
        $this->command->info('Data: tai_khoan, nguoi_than! Finished');
    }

    /**
     * @param $faker
     */
    public function createTaiKhoan($faker)
    {
        $loaiTaiKhoan = \App\TaiKhoan::$loaiTaiKhoan;
        $taiKhoan = \App\TaiKhoan::taoTaiKhoan([
            'loai_tai_khoan' => $loaiTaiKhoan[ array_rand($loaiTaiKhoan) ],
            'trang_thai' => 'HOAT_DONG',
            'ten_thanh' => $faker->firstName,
            'ho_va_ten' => $faker->name,
            'ngay_sinh' => $faker->datetime,
            'ngay_rua_toi' => $faker->datetime,
            'ngay_ruoc_le' => $faker->datetime,
            'ngay_them_suc' => $faker->datetime,
            'email' => $faker->companyEmail,
            'dien_thoai' => $faker->phoneNumber,
            'dia_chi' => $faker->address,
        ]);

        if ($taiKhoan->loai_tai_khoan == $loaiTaiKhoan[0]) {
            $this->createThanNhan($taiKhoan, $faker);
        }
    }

    /**
     * @param $taiKhoan
     * @param $faker
     */
    public function createThanNhan(\App\TaiKhoan $taiKhoan, $faker)
    {
        $loaiQuanHe = \App\ThanNhan::$loaiQuanHe;

        $taiKhoan->luuThanNhan([
            'loai_quan_he' => $loaiQuanHe[ array_rand($loaiQuanHe) ],
            'ho_va_ten' => $faker->firstName,
            'dien_thoai' => $faker->phoneNumber,
            'ghi_chu' => $faker->address,
            'tai_khoan_cap_nhat' => $taiKhoan->id,
        ]);
    }
}
