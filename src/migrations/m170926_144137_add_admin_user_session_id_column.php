<?php

use yii\db\Migration;

class m170926_144137_add_admin_user_session_id_column extends Migration
{
    public function safeUp()
    {
        $this->addColumn('admin_user_login', 'session_id', $this->string()->notNull());
    }

    public function safeDown()
    {
        $this->dropColumn('admin_user_login', 'session_id');
    }
}
