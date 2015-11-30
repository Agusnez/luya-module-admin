<?php

namespace admin\apis;

use Yii;
use admin\models\Lang;
use admin\models\Property;

/**
 * Delivers default values for the specifing table. It means it does not return a key numeric array,
 * it does only return 1 assoc array wich reperents the default row.
 *
 * @todo replace this controller with common api
 * @author nadar
 */
class DefaultsController extends \admin\base\RestController
{
    public function actionLang()
    {
        return Lang::getDefault();
    }

    /*
    public function actionProperties()
    {
        $data = [];
        foreach (Property::find()->all() as $item) {
            $object = Property::getObject($item->class_name);
            $data[] = [
                'id' => $item->id,
                'var_name' => $object->varName(),
                'option_json' => $object->options(),
                'label' => $object->label(),
                'type' => $object->type(),
                'default_value' => $object->defaultValue(),
            ];
        }

        return $data;
    }
    */
    
    public function actionCache()
    {
        if (Yii::$app->has('cache')) {
            Yii::$app->cache->flush();
        }

        $user = Yii::$app->adminuser->identity;
        $user->force_reload = 0;
        $user->save(false);

        return true;
    }
}
