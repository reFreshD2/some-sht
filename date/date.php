<?php

echo time() . PHP_EOL;
echo date("Сегодня - j/m/y, сейчас H:i") . PHP_EOL;
echo date("Завтра - j/m/y, в это время будет H:i", strtotime("next day")) . PHP_EOL;

$now = new DateTime('now');
echo 'Формат у DateTime: ' . $now->format("H:i") . PHP_EOL;

$yesterday = new DateTime('now'); //not yet
$yesterday->sub(new DateInterval("P1D"));

$diff = $now->diff($yesterday->sub(new DateInterval("P1D")));
echo 'DateTime diff:' . $diff->d . " " . $diff->h . ":" . $diff->i . PHP_EOL;

echo 'Относительный формат дат:' . PHP_EOL;
echo date("j/m/y", strtotime('next Tuesday')) . PHP_EOL;
$date = new DateTime('next Tuesday');
echo $date->format('j/m/y') . PHP_EOL;

$mydate = (new DateTimeImmutable())->setTimestamp(time());
echo $mydate->getTimezone()->getName() . PHP_EOL;
