<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 5/13/2016
 * Time: 4:34 PM
 */


/**
 * Bai 1
 * @param $a
 * @return int
 */
function test($a)
{
    $len = count($a) + 1;
    $sum = array_sum($a);
    return (($len + 1) * ($len / 2) - $sum);
}

$a = [1, 2, 3, 4, 5, 6, 7, 9, 10, 11];
echo test($a);


/**
 * Bai 2
 * @param $a
 * @param $b
 * @param $c
 * @return int
 */
function tamGiac($a, $b, $c)
{
    if ($a > 0 && $b > 0 && $c > 0) {
        return (($a + $b) > $c && ($b + $c) > $a && ($a + $c) > $b) ? 1 : 0;
    } else return 0;
}

/**
 * @param $a
 * @return int
 */
function bai_2($a)
{
    $len = count($a);
    if ($len <= 0) return 0;
    $count = 0;
    for ($i = 0; $i <= $len - 2; $i++) {
        for ($j = $i + 1; $j <= $len - 2; $j++) {
            if (tamGiac($i, $j, $j + 1)) $count++;
        }
    }
    return $count;
}

$a = [1, 2, 3, 4, 5, 6, 7];
echo bai_2($a);


/**************
 * Bai 3
 */
print("Nhap vao van ban: ");
$str = '';
$str = trim(fgets(STDIN));
$str = preg_replace('!\s+!', ' ', $str);

$num_word = 0; //so tu cua thu tin
$count_normal = 0; //so tu co kich thuoc binh thuong
$count_larger = 0; //so tu co kich thuoc vuot qua 7 ky tu

foreach (explode(" ", $str) as $item) {
    (strlen($item) > 7) ? $count_larger++ : $count_normal++;
    $num_word++;
}

printf("So tu: %d \n", $num_word);
printf("So tu co kich thuoc binh thuong: %d x 100 = %d d\n", $count_normal, $count_normal * 100);
printf("So tu co kich thuoc > 7 ky tu: %d x 200 = %d d\n", $count_larger, $count_larger * 200);
printf("Tong cong: %d d\n", $count_normal * 100 + $count_larger * 200);


/**************
 * Bai 4
 */
print("Nhap vao ngay dd/mm/yyyy: ");
$str = trim(fgets(STDIN));
$info = explode("/", $str);

if (isset($info) && is_array($info)) {
    $date = new DateTime();
    $date->setDate((int)$info[2], (int)$info[1], (int)$info[0]);
}

$jd = gregoriantojd($info[1], $info[0], $info[2]);
$stt = jddayofweek($jd, 0);
($stt == 0) ? $stt = 6 : $stt--;

$nameDay = ["Thu Hai", "Thu Ba", "Thu Tu", "Thu Nam", "Thu Sau", "Thu Bay", "Chu Nhat"];
$flag = [0, 0, 0, 0, 0, 0, 0];
$flag[$stt] = 1;

foreach ($flag as $key => $val) {
    echo $nameDay[$key] . " : ";
    if ($val != 1) {
        echo date("d/m/Y", strtotime(($key - $stt) . " days", $date->getTimestamp()));
        echo "\n";
    } else echo $date->format("d/m/Y") . "\n";
}