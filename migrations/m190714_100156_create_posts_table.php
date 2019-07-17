<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%posts}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m190714_100156_create_posts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%posts}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'deleted_at' => $this->integer(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-posts-user_id}}',
            '{{%posts}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-posts-user_id}}',
            '{{%posts}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

        // creates index for column `deleted_at`
        $this->createIndex(
            '{{%idx-posts-deleted_at}}',
            '{{%posts}}',
            'deleted_at'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-posts-user_id}}',
            '{{%posts}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-posts-user_id}}',
            '{{%posts}}'
        );

        // drops index for column `deleted_at`
        $this->dropIndex(
            '{{%idx-posts-deleted_at}}',
            '{{%posts}}'
        );

        $this->dropTable('{{%posts}}');
    }
}
