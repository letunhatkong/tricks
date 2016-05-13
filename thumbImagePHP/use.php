<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 5/13/2016
 * Time: 4:44 PM
 */

/**
 * Create thumbnail image
 * @param $folderPathOfThumbnail
 * @param string $directImage
 * @param int $defaultWidth
 * @return string
 */
function createThumbnailImage($folderPathOfThumbnail, $directImage = 'images/no_image.jpg', $defaultWidth = 150)
{
    $thumbnailPathCreated = "";

    if ($defaultWidth == "") {
        return $directImage;
    }

    if (file_exists($directImage)) {
        $size = getimagesize($directImage);

        // Get image width & height
        $imgWidth = $size[0];
        $imgHeight = $size[1];

        if ($imgHeight > $imgWidth) {
            if ($imgHeight != $defaultWidth) {
                $imgHeightNew = $defaultWidth;
                $scaleHeight = round($imgHeightNew / $imgHeight, 2);
                $imgWidthNew = $imgWidth * $scaleHeight;
                // Create thumbnail image
                $thumbnailPathCreated = ImageTool::resizeThumbnailImage($folderPathOfThumbnail, $directImage, $imgWidthNew, $imgHeightNew);
            }
        } else {
            if ($imgWidth != $defaultWidth) {
                $scale = round($defaultWidth / $imgWidth, 2);
                // Create thumbnail image
                $thumbnailPathCreated = ImageTool::resizeThumbnailImage($folderPathOfThumbnail, $directImage, $imgWidth*$scale, $imgHeight*$scale);
            }
        }
    }

    if ($thumbnailPathCreated != "") {
        if (substr($thumbnailPathCreated, 0, 1) == "/")
            $thumbnailPathCreated = substr($thumbnailPathCreated, 1);
    }
    return $thumbnailPathCreated;
}


$thumbFolder = Yii::getPathOfAlias('webroot').'/upload/thumbnails';
$img = Yii::getPathOfAlias('webroot') . '/upload/avatars/Tulips.jpg' ;
$this->createThumbnailImage($thumbFolder, $img  , 50);