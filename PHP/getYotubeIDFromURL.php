<?php
/**
 * Created by PhpStorm.
 * User: Samuel Kong
 * Date: 4/25/2016
 * Time: 10:45 AM
 */


$video = '$product->getVideoLink()';
$_video = $video;
if(isset($video) && !is_null($video)) {
    $_video = $video;
    if (strpos($video, 'youtube') > 0) {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $video, $matches);
        $_video = 'https://www.youtube.com/embed/' . $matches[0];
    }
}