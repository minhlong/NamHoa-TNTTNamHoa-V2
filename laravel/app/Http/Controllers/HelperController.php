<?php
namespace App\Http\Controllers;

use App\Services\Library;

class HelperController extends Controller
{
    public function home()
    {
        return view('index');
    }

    public function storeEvents()
    {
        $output[] = '======= Pull Git =======';
        exec('cd ' . app_path() . '; git pull 2>&1', $output);

        return implode(PHP_EOL, $output);
    }

    public function resizeImages(Library $library)
    {
        ini_set('max_execution_time', 3000);
        $dirCurr = $library->getProfilePath();
        $dirNew = $dirCurr . "/new";
        $files = scandir($dirNew);
        foreach ($files as $name) {
            if (preg_match('/png$/', $name) ||
                preg_match('/PNG$/', $name) ||
                preg_match('/jpg$/', $name) ||
                preg_match('/JPG$/', $name)
            ) {
                $img = \Image::make("$dirNew/$name");
                if ($img->width() > 400) {
                    $img->resize(400, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                $img->save("$dirCurr/$img->filename.png");
            }
        }
    }

    public function getDownloadFile($fileName)
    {
        return \Response::download("/tmp/$fileName");
    }
}
