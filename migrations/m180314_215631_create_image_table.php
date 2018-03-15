<?php

use yii\db\Migration;

/**
 * Handles the creation of table `image`.
 */
class m180314_215631_create_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('image', [
            'id' => $this->primaryKey(),
            'file_id' => $this->integer()->notNull(),
            'url' => $this->text(),
            'number' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-image-file_id',
            'image',
            'file_id'
        );

        $this->addForeignKey(
            'fk-image-file_id',
            'image',
            'file_id',
            'file',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey(
            'fk-image-file_id',
            'image'
        );

        $this->dropIndex(
            'idx-image-file_id',
            'image'
        );

        $this->dropTable('image');
    }
}
