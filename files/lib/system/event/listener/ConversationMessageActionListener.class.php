<?php

namespace wcf\system\event\listener;

use wcf\data\conversation\message\PollConversationMessage;
use wcf\data\poll\Poll;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\poll\ConversationPollHandler;
use wcf\system\poll\PollManager;
use wcf\system\WCF;

class ConversationMessageActionListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     * 
     * @param \wcf\data\conversation\message\ConversationMessageAction $eventObj
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_POLL || !ConversationPollHandler::getInstance()->canStartPublicPoll()) {
            return;
        }

        $action = $eventObj->getActionName();
        if ($eventName == 'validateAction') {
            $method = 'validate'.$action;
            if (!\method_exists($this, $method)) {
                return;
            }
            $this->$method($eventObj);
        } else if ($eventName == 'finalizeAction') {
            $method = 'finalize'.$action;
            if (!\method_exists($this, $method)) {
                return;
            }
            $this->$method($eventObj);
        }
    }

    /**
     * @param \wcf\data\conversation\message\ConversationMessageAction $eventObj
     */
    public function validateBeginEdit($eventObj)
    {
        $pollManager = PollManager::getInstance();
        if (isset($eventObj->message->pollID)) {
            $pollManager->setObject(ConversationPollHandler::CONVERSATION_POLL_TYPE, $eventObj->message->messageID, $eventObj->message->pollID);
        } else {
            $pollManager->setObject(ConversationPollHandler::CONVERSATION_POLL_TYPE, $eventObj->message->messageID);
        }
        $pollManager->assignVariables();
    }

    /**
     * @param \wcf\data\conversation\message\ConversationMessageAction $eventObj
     */
    public function validateDelete($eventObj)
    {
        $pollIDs = [];
        foreach ($eventObj->getObjects() as $conversationMessage) {
            if (!isset($conversationMessage->pollID)) {
                continue;
            }
            array_push($pollIDs, $conversationMessage->pollID);
        }
        if (empty($pollIDs)) {
            return;
        }
        PollManager::getInstance()->removePolls($pollIDs);
    }

    /**
     * @param \wcf\data\conversation\message\ConversationMessageAction $eventObj
     */
    public function finalizeUpdate($eventObj)
    {
        foreach($eventObj->getObjects() as $messageEditor) {
            if (!isset($messageEditor->pollID)) {
                continue;
            }
            $pollManager = PollManager::getInstance();
            $pollManager->setObject(ConversationPollHandler::CONVERSATION_POLL_TYPE, $messageEditor->getObjectID(), $messageEditor->pollID);
            $pollManager->readFormParameters($_POST['parameters']['poll']);
            $pollManager->save();
        }
    }
}
