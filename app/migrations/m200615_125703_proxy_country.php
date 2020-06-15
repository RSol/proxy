<?php

use yii\db\Migration;

/**
 * Class m200615_125703_proxy_country
 */
class m200615_125703_proxy_country extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('proxy_country', [
            'id' => $this->primaryKey(),
            'name' => $this->string(10),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('proxy_country');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200615_125713_proxy_country cannot be reverted.\n";

        return false;
    }
    */
}
