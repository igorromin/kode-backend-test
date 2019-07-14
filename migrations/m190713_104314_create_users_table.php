<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m190713_104314_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull()->unique(),
            'password_hash' => $this->string(60),
            'token' => $this->string(60),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        //batchInsert менее информативен
        $this->insert('{{%users}}', [
            'username' => 'user1',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('123456ad'),
            'token' => '',
        ]);

        $this->insert('{{%users}}', [
            'username' => 'user2',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('123456ad123'),
            'token' => '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
