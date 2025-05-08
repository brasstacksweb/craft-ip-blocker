<?php

namespace brasstacksweb\craftipblocker\records;

use craft\db\ActiveRecord;
use craft\db\SoftDeleteTrait;

/**
 * Attempt record.
 *
 * @property int    $id
 * @property string $pattern
 * @property string $ip
 * @property string $dateCreated
 * @property string $dateUpdated
 * @property string $uid
 */
class Attempt extends ActiveRecord
{
    use SoftDeleteTrait;

    public static function tableName()
    {
        return '{{%ipblocker_attempts}}';
    }
}
