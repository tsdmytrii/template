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

function make_production_dir($version = '1.0', $componentsDir = 'js/components/user/', $newline = '\n') {

    if (is_dir($componentsDir)) {
        $dir = scandir($componentsDir);

        foreach ($dir as $val) {

            if (strpos($val, '.') === false) {
                if (is_dir($componentsDir . $val) !== false) {

                    $productionDir = scandir($componentsDir . $val . '/production/');

                    $makeDir = true;

                    if (count($productionDir) > 0) {

                        foreach ($productionDir as $pD) {

                            if ($pD == $version) {

                                $makeDir = false;
                            }
                        }
                    }

                    if ($makeDir) {

                        if (mkdir($componentsDir . $val . '/production/' . $version)) {

                            echo 'Directory ' . $componentsDir . $val . '/production/' . $version . '/ created.' . $newline;
                        } else {

                            echo 'Error created directory ' . $componentsDir . $val . '/production/' . $version . '/' . $newline;
                        }
                    } else {

                        echo 'Directory ' . $componentsDir . $val . '/production/' . $version . '/ is existed' . $newline;
                    }

                    make_production_dir($version, $componentsDir . $val . '/sub_components/', $newline);
                }
            }
        }
    }
}

function make_version_js_file($version, $newline) {
    echo $newline . '//---------------Creating js version file. ';

    $text = 'steal.options.version = "' . $version . '";';
    $fp = fopen("js/version.js", "w");
    $result = fwrite($fp, $text);
    fclose($fp);

    echo $result ? 'File /js/version.js created!' : 'Error creating /js/version.js file!';
    echo $newline . $newline;
}

echo $newline . '//---------------Starting make production directories!' . $newline . $newline;

make_production_dir($version, $componentsDir, $newline);

make_version_js_file($version, $newline);

?>