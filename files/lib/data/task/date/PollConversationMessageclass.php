<?php

namespace wcf\data\task\date;

use wcf\data\conversation\message\ConversationMessage;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\IPollObject;
use wcf\system\poll\ConversationPollHandler;

class PollConversationMessage extends DatabaseObjectDecorator implements IPollObject
{
    protected static $baseClass = ConversationMessage::class;

    /**
     * @inheritDoc
     */
    public function canVote()
    {
        return ConversationPollHandler::getInstance()->canVote();
    }
}