<?php

namespace luya\admin\behaviors;

use Yii;
use yii\db\ActiveRecord;
use yii\web\Application;
use yii\base\Behavior;
use yii\helpers\Json;

/**
 * LogBehavior stores informations when active records are updated, inserted or deleted.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class LogBehavior extends Behavior
{
    public $route = '';

    public $api = '';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'eventAfterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'eventAfterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'eventAfterDelete',
        ];
    }
    
    /**
     * The value to transform to json.
     * 
     * @param string|array $array
     * @return string
     */
    private function toJson($array)
    {
        $array = (array) $array;
        
        return Json::encode($array);
    }
    
    /**
     * After delete event.
     *
     * @param \yii\base\Event $event
     */
    public function eventAfterDelete($event)
    {
        if (Yii::$app instanceof Application) {
            Yii::$app->db->createCommand()->insert('admin_ngrest_log', [
                'user_id' => is_null(Yii::$app->adminuser->getIdentity()) ? 0 : Yii::$app->adminuser->getId(),
                'timestamp_create' => time(),
                'route' => $this->route,
                'api' => $this->api,
                'is_insert' => false,
                'is_update' => false,
                'is_delete' => true,
                'attributes_json' => $this->toJson($event->sender->getAttributes()),
                'table_name' => $event->sender->tableName(),
                'pk_value' => implode("-", $event->sender->getPrimaryKey(true)),
            ])->execute();
        }
    }

    /**
     * After insert event.
     *
     * @param \yii\db\AfterSaveEvent $event
     */
    public function eventAfterInsert($event)
    {
        if (Yii::$app instanceof Application) {
            Yii::$app->db->createCommand()->insert('admin_ngrest_log', [
                'user_id' => is_null(Yii::$app->adminuser->getIdentity()) ? 0 : Yii::$app->adminuser->getId(),
                'timestamp_create' => time(),
                'route' => $this->route,
                'api' => $this->api,
                'is_insert' => true,
                'is_update' => false,
                'attributes_json' => $this->toJson($event->sender->getAttributes()),
                'attributes_diff_json' => null,
                'table_name' => $event->sender->tableName(),
                'pk_value' => implode("-", $event->sender->getPrimaryKey(true)),
            ])->execute();
        }
    }

    /**
     * After Update.
     *
     * @param \yii\db\AfterSaveEvent $event
     */
    public function eventAfterUpdate($event)
    {
        if (Yii::$app instanceof Application) {
            Yii::$app->db->createCommand()->insert('admin_ngrest_log', [
                'user_id' => is_null(Yii::$app->adminuser->getIdentity()) ? 0 : Yii::$app->adminuser->getId(),
                'timestamp_create' => time(),
                'route' => $this->route,
                'api' => $this->api,
                'is_insert' => false,
                'is_update' => true,
                'attributes_json' => $this->toJson($event->sender->getAttributes()),
                'attributes_diff_json' => $this->toJson($event->changedAttributes),
                'table_name' => $event->sender->tableName(),
                'pk_value' => implode("-", $event->sender->getPrimaryKey(true)),
            ])->execute();
        }
    }
}
