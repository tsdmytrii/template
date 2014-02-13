<?php

$newline = "\r\n";
$version = '1.0';
$componentsDir = 'js/components/user/';

if (isset($argv) && count($argv) > 1) {
    if (isset($argv[1]))
        $version = $argv[1];

    if (isset($argv[2]))
        $componentsDir = $argv[2];
}

function remove_old_production_dir($componentsDir, $version, $newline) {

    if (is_dir($componentsDir)) {
        $dirObserve = scandir($componentsDir);

        foreach ($dirObserve as $val) {

            if (strpos($val, '.') === false) {
                if (is_dir($componentsDir . $val) !== false) {

                    remove_dir($componentsDir . $val . '/production', $version, $newline);

                    remove_old_production_dir($componentsDir . $val . '/sub_components/', $version, $newline);
                }
            }
        }
    }
}

function remove_dir($dir, $version, $newline) {

    $objs = glob($dir . "/*");
    if (count($objs) > 0) {
        foreach ($objs as $obj) {
            if (strpos($obj, $version) === false) {
                if (is_dir($obj)) {
                    remove_dir($obj, $version, $newline);
                } else {
                    if (unlink($obj))
                        echo ' ----X File: ' . $obj . ' deleted!' . $newline;
                    else
                        echo ' ----X File: ' . $obj . ' delete error!' . $newline;
                }
            }
        }
    }

    $checkProduction = explode('production', $dir);

    if (isset($checkProduction[1]) && $checkProduction[1] != '') {
        if (rmdir($dir))
            echo ' ----X Folder: ' . $dir . ' deleted!' . $newline;
        else
            echo ' ----X Folder: ' . $dir . ' delete error!' . $newline;
    }
}

echo $newline . '//---------------Removing old production directories!' . $newline . $newline;

remove_old_production_dir($componentsDir, $version, $newline);

?>