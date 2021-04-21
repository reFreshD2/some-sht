<?php

require_once __DIR__ . '/SellDTO.php';

function parseDrom(string $url)
{
    $doc = file_get_contents($url);
    $doc = mb_convert_encoding($doc, "utf-8", "windows-1251");
    $doc = str_replace('&nbsp;', ' ', $doc);
    $doc = str_replace('<!-- -->', '', $doc);

    $properties = [];
    preg_match_all(
        '/<tr.*?><th.*?>(?\'name\'[А-Яа-я,\s]+)<\/th><td.*?>(<span.*?><a.*?>|<span.*?>|<a.*?>|)(?\'value\'[А-Яа-я.,\s\d]+)(<\/span>|<\/a><\/span>|<\/a>|)<\/td><\/tr>/u',
        $doc,
        $properties,
        PREG_SET_ORDER);
    $properties = castTypeToProperties($properties);

    $selling = new SellDTO($properties);
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

parseDrom('regex/drom.html');
