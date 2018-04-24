<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PullGitController extends Controller
{
    public function postAutoPull()
    {
        $output[] = '======= Pull Git =======';
        exec('cd ' . app_path() . '; git pull 2>&1', $output);

        return implode(PHP_EOL, $output);
    }
}
