<?php

function someMagic(string $filepath, string $adds): void
{
    $f = fopen($filepath, 'ra+');

    echo is_file($filepath) . PHP_EOL;
    echo filetype($filepath) . PHP_EOL;

    while (!feof($f)) {
        echo fgets($f);
    }

    if (fwrite($f, $adds)) {
        echo 'SUCCESS' . PHP_EOL;
    }

    fclose($f);
}

someMagic('files/myfile.txt', "work запись\n");

function createAndWrite(string $adds)
{
    $tmp = tmpfile();
    fwrite($tmp, $adds);
    fseek($tmp, 0);
    return $tmp;
}

function readTmp($tmp): void
{
    echo fread($tmp, 1024);
}

$tmp = createAndWrite('Ого, временный файл');
readTmp($tmp);
