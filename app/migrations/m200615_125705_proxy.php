<?php

use yii\db\Migration;

/**
 * Class m200615_125705_proxy
 */
class m200615_125705_proxy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('proxy', [
            'id' => $this->primaryKey(),
            'address' => $this->string(17),
            'port' => $this->smallInteger(),
            'type' => $this->string(10),
            'anonymity' => $this->smallInteger(),
            'country_id' => $this->integer(),
        ]);

        $this->createIndex('address_port_type', 'proxy', ['address', 'port', 'type']);

        $this->addForeignKey('fk_proxy_country_id', 'proxy', 'country_id', 'proxy_country', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_proxy_country_id', 'proxy');

        $this->dropTable('proxy');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200615_125705_proxy cannot be reverted.\n";

        return false;
    }
    */
}
