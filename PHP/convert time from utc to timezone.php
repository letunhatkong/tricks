<?php

$utcDate = "2017-01-27 11:14:06";
$UTC = new \DateTimeZone("UTC");
$losTZ = new \DateTimeZone("America/Los_Angeles");
$date = new \DateTime($utcDate, $UTC );
$date->setTimezone( $losTZ );
