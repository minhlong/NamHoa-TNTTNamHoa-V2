<?php

use Illuminate\Database\Seeder;

class LienKetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->command->info('Create fake data for Lien Ket! Start');

        DB::table('lien_ket')->delete();

        // Add Nhom Tai Khoan
        $model = new \App\LienKet();
        $model->slug = 'slug';
        $model->url = 'http://youtube.com/abcxyz';
        $model->save();

        $this->command->info('Create fake data for Lien Ket! Finished');
    }
}
