<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 5/13/2016
 * Time: 4:39 PM
 */


/**
 * Action Download file
 */
function actionDownload()
{
    $file = Yii::app()->request->getParam('file');
    $modelArchives = Archives::model()->find('path=:path', array(':path' => $file));
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-type:application/octet-stream");
    header('Content-Disposition: attachment; filename="' . $modelArchives->fileName . '"');
    $contentArchive = Yii::getPathOfAlias('webroot') . Yii::app()->params["archiveFolderPath"] . '/' . $file;
    echo file_get_contents($contentArchive);
    exit;
}

