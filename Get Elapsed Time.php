<?php
// Get Elapsed Time
function getElapseTime($time)
{
    $txt = "";
    $u = 0;
    $tokens = array(
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );
    foreach ($tokens as $unit => $text) {
        if ($time > $unit) {
            $u = floor($time / $unit);
            $txt = $text;
            break;
        }
    }
    return $u . ' ' . $txt . (($u > 1) ? 's' : '') . ' ago';
}

function getTiming($date)
{
    $time = strtotime($date);
    $timeDiff = time() - $time;
    $timeDiff = ($timeDiff < 1) ? 2 : $timeDiff;
    return $this->getElapseTime($timeDiff);
}

function actionTest()
{
    $txt = "2015-10-20 4:50:00";
    $d = $this->getTiming($txt);
    echo date("d.m.Y H:i:s", strtotime("now")) . "<br>";
    echo date("d.m.Y H:i:s", strtotime($txt)) . "<br> ";
    echo $d;
}