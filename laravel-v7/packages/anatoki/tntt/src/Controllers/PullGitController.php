<?php

namespace TNTT\Controllers;

class PullGitController extends Controller
{
    public function postAutoPull()
    {
        $output[] = '======= Pull Git =======';
        exec('cd '.base_path().'; git pull 2>&1', $output);

        return implode(PHP_EOL, $output);
    }
}
