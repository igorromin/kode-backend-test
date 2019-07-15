<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%likes}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%posts}}`
 * - `{{%users}}`
 */
class m190714_164151_create_junction_table_for_posts_and_users_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%likes}}', [
            'post_id' => $this->integer(),
            'user_id' => $this->integer(),
            'PRIMARY KEY(post_id, user_id)',
        ]);

        // creates index for column `posts_id`
        $this->createIndex(
            '{{%idx-likes-post_id}}',
            '{{%likes}}',
            'post_id'
        );

        // add foreign key for table `{{%posts}}`
        $this->addForeignKey(
            '{{%fk-likes-post_id}}',
            '{{%likes}}',
            'post_id',
            '{{%posts}}',
            'id',
            'CASCADE'
        );

        // creates index for column `users_id`
        $this->createIndex(
            '{{%idx-likes-user_id}}',
            '{{%likes}}',
            'user_id'
        );

        // add foreign key for table `{{%users}}`
        $this->addForeignKey(
            '{{%fk-likes-user_id}}',
            '{{%likes}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%posts}}`
        $this->dropForeignKey(
            '{{%fk-likes-post_id}}',
            '{{%likes}}'
        );

        // drops index for column `posts_id`
        $this->dropIndex(
            '{{%idx-likes-post_id}}',
            '{{%likes}}'
        );

        // drops foreign key for table `{{%users}}`
        $this->dropForeignKey(
            '{{%fk-likes-user_id}}',
            '{{%likes}}'
        );

        // drops index for column `users_id`
        $this->dropIndex(
            '{{%idx-likes-user_id}}',
            '{{%likes}}'
        );

        $this->dropTable('{{%likes}}');
    }
}
