<?php

namespace wcf\system\event\listener;

use wcf\data\conversation\message\ConversationMessageEditor;
use wcf\data\conversation\message\PollConversationMessage;
use wcf\data\poll\Poll;
use wcf\page\ConversationPage;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\poll\ConversationPollHandler;
use wcf\system\poll\PollManager;
use wcf\system\WCF;

class ConversationShowListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     * @param ConversationPage $eventObj
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_POLL || !ConversationPollHandler::getInstance()->canVote()) {
            return;
        }

        $pollManager = PollManager::getInstance();
        $pollManager->setObject(ConversationPollHandler::CONVERSATION_POLL_TYPE, 0);
        $pollManager->assignVariables();

        /** @var \wcf\data\conversation\message\ViewableConversationMessageList */
        $conversationMessageList = $eventObj->objectList;

        $messageIDToPoll = [];
        foreach ($conversationMessageList as $conversationMessage) {
            if (!isset($conversationMessage->pollID)) {
                continue;
            }
            $poll = new Poll($conversationMessage->pollID);
            if (!$poll->getObjectID()) {
                $editor = new ConversationMessageEditor($conversationMessage->getDecoratedObject());
                $editor->update([
                    'pollID' => null
                ]);
                continue;
            }
            $poll->getOptions();
            $poll->setRelatedObject(new PollConversationMessage($conversationMessage));
        }

        if (empty($messageIDToPoll)) {
            return;
        }

        WCF::getTPL()->assign([
            'messageIDToPoll' => $messageIDToPoll,
        ]);
    }
}
