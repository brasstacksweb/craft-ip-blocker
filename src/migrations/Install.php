<?php

namespace brasstacksweb\craftipblocker\migrations;

use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{
    public function safeUp(): bool
    {
        if ($this->createTables()) {
            $this->createIndexes();

            // Refresh the db schema caches
            \Craft::$app->db->schema->refresh();
        }

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropTableIfExists('{{%ipblocker_attempts}}');
        $this->dropTableIfExists('{{%ipblocker_blocks}}');

        return true;
    }

    protected function createTables(): bool
    {
        if (\Craft::$app->db->schema->getTableSchema('{{%ipblocker_attempts}}') !== null
            || \Craft::$app->db->schema->getTableSchema('{{%ipblocker_blocks}}') !== null) {
            return false;
        }

        $this->createTable('{{%ipblocker_attempts}}', [
            'id' => $this->primaryKey(),
            'pattern' => $this->string()->notNull(),
            'ip' => $this->string()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'dateDeleted' => $this->dateTime()->null(),
            'uid' => $this->uid(),
        ]);

        $this->createTable('{{%ipblocker_blocks}}', [
            'id' => $this->primaryKey(),
            'ip' => $this->string()->notNull(),
            'expires' => $this->dateTime()->notNull(),
            'reason' => $this->string()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        return true;
    }

    protected function createIndexes(): void
    {
        $this->createIndex('count_attempts', '{{%ipblocker_attempts}}', ['ip', 'pattern', 'dateCreated'], false);
        $this->createIndex('block_expired', '{{%ipblocker_blocks}}', ['ip', 'expires'], false);
    }
}
