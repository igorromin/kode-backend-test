<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%posts_fields}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%posts}}`
 */
class m190714_100858_create_posts_fields_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%posts_fields}}', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer()->notNull(),
            'type' => $this->string(16)->notNull(),
            'value' => $this->text()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        // creates index for column `post_id`
        $this->createIndex(
            '{{%idx-posts_fields-post_id}}',
            '{{%posts_fields}}',
            'post_id'
        );

        // add foreign key for table `{{%posts}}`
        $this->addForeignKey(
            '{{%fk-posts_fields-post_id}}',
            '{{%posts_fields}}',
            'post_id',
            '{{%posts}}',
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
            '{{%fk-posts_fields-post_id}}',
            '{{%posts_fields}}'
        );

        // drops index for column `post_id`
        $this->dropIndex(
            '{{%idx-posts_fields-post_id}}',
            '{{%posts_fields}}'
        );

        $this->dropTable('{{%posts_fields}}');
    }
}
