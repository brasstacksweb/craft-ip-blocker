<?php

namespace brasstacksweb\craftipblocker\records;

use craft\db\ActiveRecord;

class Block extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ipblocker_blocks}}';
    }
}
