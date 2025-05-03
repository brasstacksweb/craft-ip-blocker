<?php

namespace brasstacksweb\craftipblocker\records;

use craft\db\ActiveRecord;

class Attempt extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ipblocker_attempts}}';
    }
}
