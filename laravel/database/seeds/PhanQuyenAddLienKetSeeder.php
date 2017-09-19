<?php

use Illuminate\Database\Seeder;

class PhanQuyenAddLienKetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->command->info('Create fake data for Phan Quyen Lien Ket ! Start');

        $model = new \App\PhanQuyen();
        $model->ten = 'lien-ket';
        $model->ten_hien_thi = 'Liên Kết';
        $model->ghi_chu = 'Tạo liên kết URL';
        $model->save();

        $this->command->info('Create fake data for Phan Quyen Lien Ket! Finished');
    }
}
