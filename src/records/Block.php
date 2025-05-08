<?php

namespace brasstacksweb\craftipblocker\records;

use craft\db\ActiveRecord;

/**
 * Block record.
 *
 * @property int    $id
 * @property string $ip
 * @property string $expires
 * @property string $reason
 * @property string $dateCreated
 * @property string $dateUpdated
 * @property string $uid
 */
class Block extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ipblocker_blocks}}';
    }
}
