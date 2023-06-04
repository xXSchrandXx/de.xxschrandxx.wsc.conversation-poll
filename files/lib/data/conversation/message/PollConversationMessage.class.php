<?php

namespace wcf\data\conversation\message;

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