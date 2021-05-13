<?php

require_once __DIR__ . '/SellDTO.php';

function parseDrom(string $url)
{
    $doc = file_get_contents($url);
    $doc = mb_convert_encoding($doc, "utf-8", "windows-1251");
    $replaced = [
        '/<span.*?>/',
        '/<a.*?>/',
        '/<\/span>/',
        '/<\/a>/',
        '/&nbsp;/',
        '/<!-- -->/',
    ];
    $doc = preg_replace($replaced, '', $doc);

    $properties = [];
    preg_match_all(
        '/<tr.*?><th.*?>(?\'name\'[А-Яа-я,\s]+)<\/th><td.*?>(?\'value\'[А-Яа-я.,X\s\d]+)<\/td><\/tr>/u',
        $doc,
        $properties,
        PREG_SET_ORDER);
    $properties = castTypeToProperties($properties);
    $price = [];
    preg_match_all('/(\d+)\?/', $doc, $price, PREG_SET_ORDER);
    $title = [];
    preg_match_all('/<h1.*?>(.+)<\/h1>/', $doc, $title, PREG_SET_ORDER);

    $selling = new SellDTO(getMatch($title), (int)getMatch($price), $properties);
    var_dump($selling);
}

function castTypeToProperties(array $properties): array
{
    return array_map(static function ($property) {
        foreach ($property as $name => $value) {
            if ($name !== 'name' && $name !== 'value') {
                unset($property[$name]);
            }
            if ($name === 'value') {
                $match = [];
                if (preg_match_all('/(\b\d+\b)/u', $value, $match, PREG_SET_ORDER)) {
                    $result = '';
                    if (count($match) > 1) {
                        foreach ($match as $digit) {
                            $result .= $digit[0];
                        }
                    } else {
                        $result = $match[0][0];
                    }
                    $property['value'] = (int)$result;
                }
                if (preg_match('/\bда\b/ui', $value, $match)) {
                    $property['value'] = true;
                }
                if (preg_match('/\bнет\b/ui', $value, $match)) {
                    $property['value'] = false;
                }
            }
        }
        return $property;
    }, $properties);
}

function getMatch(array $match): string
{
    return $match[0][1];
}

function csv(string $url)
{
    $doc = file_get_contents($url);
    $doc = preg_replace_callback('/(\d+) (\d+)/', function ($matches) {
        return sprintf('%s%s', $matches[1], $matches[2]);
    }, $doc);
    $replaced = [
        '/,+/',
        '/№/',
        '/VIN/',
    ];
    $doc = preg_replace(['/,,,,\n/'], '*' . PHP_EOL, $doc);
    $doc = preg_replace($replaced, ';', $doc);
    $doc = preg_replace_callback('/(;\n;)([^\d])/', function ($matches) {
        return ';' . $matches[2];
    }, $doc);
    $doc = preg_replace(['/\*/'], ';', $doc);
    $url = str_replace('.', '_new.', $url);
    $newDoc = [];
    preg_match_all(
        '/^;(\d{2}\.\d{2}\.\d{4});([A-Za-zА-Яа-я\s-]+);([A-Za-zА-Яа-я\s\d-]+);([A-Za-zА-Яа-я\s\d]+);(\d+|)\n;(.*);\n/um',
        $doc,
        $newDoc,
        PREG_SET_ORDER
    );
    $f = fopen($url, 'c');
    foreach ($newDoc as $matches) {
        $repairs = explode(';', $matches[6]);
        foreach ($repairs as $repair) {
            $str = sprintf('%s;%s;%s;%s;%s;%s',
                $matches[1],
                trim($matches[2]),
                strtoupper(trim($matches[3])),
                strtoupper(trim($matches[4])),
                $matches[5],
                $repair
            );
            fwrite($f, $str . PHP_EOL);
        }
    }
    fclose($f);
}

//parseDrom('regex/drom.html');
csv('regex/misteriol.csv');
