<?php

use yii\db\Migration;

/**
 * Handles the creation of table `file`.
 */
class m180312_151917_create_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('file', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'extension' => $this->string()->notNull(),
            'size' => $this->float()->notNull(),
            'uploaded_at' => $this->timestamp()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
            'deleted_at' => $this->timestamp()->notNull()->defaultExpression("CURRENT_TIMESTAMP + (30 * INTERVAL '1 minute')"),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('file');
    }
}
