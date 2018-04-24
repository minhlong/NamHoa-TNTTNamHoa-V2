<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoBackup extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'system:backup {mail : Emails nhận sao lưu. Ex: email1@mail.com;email2@mail.com}';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Sao lưu dữ liệu hệ thống. Ex: system:backup \'mail1@mail.com;mail2@mail.com\'';

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $mailTo = explode(';', $this->argument('mail'));
        $database = \DB::connection()->getDatabaseName();
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $dateTime = date('Ymd_His');
        $tmpFilePath = '/tmp/' . $dateTime . '_' . $database . '_backup.sql';
        exec("mysqldump -u $username --password=$password $database > $tmpFilePath");
        exec('cd ' . app_path() . ';git config --get remote.origin.url', $sourcePath);
        \Mail::send('emails-backup', ['sourcePath' => array_shift($sourcePath)], function ($message) use ($tmpFilePath, $mailTo, $dateTime) {
            $message->from(env('MAIL_USERNAME'), 'Backup System');
            $message->to($mailTo)->subject("[Backup-System][$dateTime] - Website Đoàn Thiếu Nhi");
            $message->attach($tmpFilePath);
        });
    }
}
