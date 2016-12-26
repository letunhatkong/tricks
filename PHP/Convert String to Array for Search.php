<?php
$txtUser = preg_replace('!\s+!', ' ', $txtUser); // convert more space to one space
$searchArr = array();
if (isset($txtUser) && trim($txtUser) !== "") {
    $txtUser = ltrim($txtUser, " "); // Remove all space left side
    $txtUser = rtrim($txtUser, " "); // Remove all space right side
    array_push($searchArr, $txtUser);
    $searchArr += explode(" ", $txtUser);
}
if (count($searchArr) > 0) {
// Do
}