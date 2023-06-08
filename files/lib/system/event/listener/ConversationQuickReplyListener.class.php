<?php

namespace wcf\system\event\listener;

use wcf\data\object\type\ObjectTypeCache;
use wcf\data\poll\Poll;
use wcf\data\poll\PollEditor;
use wcf\data\poll\PollList;
use wcf\system\poll\ConversationPollHandler;
use wcf\system\poll\PollManager;
use wcf\system\WCF;

class ConversationQuickReplyListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     * 
     * @param \wcf\system\message\QuickReplyManager $eventObj
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_POLL || !ConversationPollHandler::getInstance()->canStartPublicPoll()) {
            return;
        }

        $this->$eventName($eventObj, $parameters);
    }

    /**
     * @param \wcf\system\message\QuickReplyManager $eventObj
     */
    public function allowedDataParameters($eventObj, &$parameters)
    {
        array_push($parameters['allowedDataParameters'], 'poll');
    }

    /**
     * @param \wcf\system\message\QuickReplyManager $eventObj
     */
    public function validateParameters($eventObj, &$parameters)
    {
        if (!array_key_exists('poll', $parameters['data'])) {
            return;
        }
        $pollManager = PollManager::getInstance();
        $pollManager->setObject(ConversationPollHandler::CONVERSATION_POLL_TYPE, 0);
        $pollManager->readFormParameters($parameters['data']['poll']);
        $pollManager->validate();
        $parameters['data']['pollID'] = $pollManager->save();
        unset($parameters['data']['poll']);
    }

    /**
     * @param \wcf\system\message\QuickReplyManager $eventObj
     * @param \wcf\data\conversation\message\ConversationMessage[] $messages
     */
    public function createdMessage($eventObj, &$messages)
    {
        $pollIDs = [];
        foreach ($messages as $message) {
            $pollEditor = new PollEditor(new Poll($message->pollID));
            $pollEditor->update([
                'objectID' => $message->getObjectID()
            ]);
            array_push($pollIDs, $message->pollID);
        }

        if (empty($pollIDs)) {
            return;
        }

        $polls = PollManager::getInstance()->getPolls($pollIDs);

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
