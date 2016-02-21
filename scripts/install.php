<?php

$root = dirname(dirname(dirname(dirname(__DIR__))));

copyFile('aura.route.php', "{$root}/var/conf");
copyFile('phinx.php', "{$root}/var/db");
copyFile('create_db.php', "{$root}/bin");
copyFile('.env', $root);

function copyFile($file, $dest)
{
    $target = "{$dest}/{$file}";
    $dir = (new SplFileInfo($target))->getPathInfo();
    if(! $dir->isDir()) {
        mkdir($dir->getPathname());
    }
    $target = "{$dest}/{$file}";
    if (file_exists($target) && file_get_contents($target)) {
        return;
    }
    $source = __DIR__ . '/files/' . $file;
    error_log("created: {$target}");
    copy($source, $target);
}
