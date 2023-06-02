<?php

namespace wcf\system\poll;

use BadMethodCallException;
use wcf\data\conversation\message\ConversationMessageList;
use wcf\data\poll\Poll;
use wcf\system\poll\AbstractPollHandler;
use wcf\system\WCF;

class ConversationPollHandler extends AbstractPollHandler
{
    /**
     * Conversation ObjectType name
     */
    const CONVERSATION_POLL_TYPE = 'com.woltlab.wcf.conversation.message';

    /**
     * @inheritDoc
     */
    public function canStartPublicPoll()
    {
        return WCF::getSession()->getPermission('user.conversation.canStartPublicPoll') ? true : false;
    }

    /**
     * @inheritDoc
     */
    public function canVote()
    {
        return WCF::getSession()->getPermission('user.conversation.canVote') ? true : false;
    }

    /**
     * @inheritDoc
     */
    public function getRelatedObject(Poll $poll)
    {
        $conversationMessageList = new ConversationMessageList();
        $conversationMessageList->setObjectIDs([$poll->objectID]);
        $conversationMessageList->readObjects();
        try {
            $conversationMessage = $conversationMessageList->getSingleObject();
        } catch (BadMethodCallException $e) {
            return;
        }
        if ($conversationMessage && $conversationMessage->pollID == $poll->pollID) {
            return $conversationMessage;
        }
    }
}
