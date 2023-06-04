<?php

namespace wcf\system\event\listener;

use wcf\data\conversation\message\ConversationMessageList;
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

        /** @var ConversationMessageList */
        $conversationMessageList = $eventObj->objectList;

        $pollIDs = [];
        foreach ($conversationMessageList as $conversationMessage) {
            if (!isset($conversationMessage->pollID)) {
                continue;
            }
            array_push($pollIDs, $conversationMessage->pollID);
        }

        if (empty($pollIDs)) {
            return;
        }

        $polls = $pollManager->getPolls($pollIDs);

        if (empty($polls)) {
            return;
        }

        $messageIDToPoll = [];
        foreach ($polls as $poll) {
            $poll->setRelatedObject(ConversationPollHandler::getInstance()->getRelatedObject($poll));
            $messageIDToPoll[$poll->objectID] = $poll;
        }

        if (empty($messageIDToPoll)) {
            return;
        }

        WCF::getTPL()->assign([
            'messageIDToPoll' => $messageIDToPoll,
        ]);
    }
}
