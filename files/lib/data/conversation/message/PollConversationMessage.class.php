<?php

namespace wcf\data\conversation\message;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\IPollContainer;
use wcf\system\poll\ConversationPollHandler;

/**
 * @property-read int $pollID
 */
class PollConversationMessage extends DatabaseObjectDecorator implements IPollContainer
{
    protected static $baseClass = ConversationMessage::class;

    /**
     * @inheritDoc
     */
    public function getPollID()
    {
        return $this->pollID;
    }

    /**
     * @inheritDoc
     */
    public function canVote()
    {
        if (!$this->isVisible()) {
            return false;
        }
        return ConversationPollHandler::getInstance()->canVote();
    }
}