<?php

namespace admin\storage;

class Image
{
    public function create($fileId, $filterId = 0)
    {
        $file = \yii::$app->luya->storage->file->getPath($fileId);
        $info = \yii::$app->luya->storage->file->getInfo($fileId);
        $imagine = new \Imagine\Gd\Imagine();
        $image = $imagine->open($file);
        $fileName = $filterId.'_'.$info->name_new_compound;
        
        if (empty($filterId)) {
            $save = $image->save(\yii::$app->luya->storage->dir.$fileName);
        } else {
            $model = \admin\models\StorageFilter::find()->where(['id' => $filterId])->one();
            if (!$model) {
                throw new \Exception("could not find the provided filter id '$filterId'.");
            }
            $newimage = $model->applyFilter($image, $imagine);
            $save = $newimage->save(\yii::$app->luya->storage->dir.$fileName);
        }
        

        if ($save) {
            $model = new \admin\models\StorageImage();
            $model->setAttributes([
                'file_id' => $fileId,
                'filter_id' => $filterId,
            ]);
            if ($model->save()) {
                return $model->id;
            }
        }

        return false;
    }

    // @web/storage/the-originame_name_$filterId_$fileIdf.jpg
    public function get($imageId)
    {
        // get the real full image path to display this file.
        $data = \admin\models\StorageImage::find()->where(['id' => $imageId])->with("file")->one();
        if (!$data) {
            return false;
        }
        $fileName = implode([$data->filter_id, $data->file->name_new_compound], "_");

        return \luya\helpers\ArrayHelper::toObject([
            "filter_id" => $data->filter_id,
            "file_id" => $data->file_id,
            "image_id" => $data->id,
            "file_source" => $data->file->name_new_compound,
            "image_source" => $fileName,
            "source" => \yii::$app->luya->storage->httpDir.$fileName,
        ]);
    }
}
