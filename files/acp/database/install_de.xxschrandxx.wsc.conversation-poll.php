<?php

use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\PartialDatabaseTable;

return [
    // wcf1_conversation_message
    PartialDatabaseTable::create('wcf1_conversation_message')
        ->columns([
            IntDatabaseTableColumn::create('pollID')
                ->length(10)
        ])
];
