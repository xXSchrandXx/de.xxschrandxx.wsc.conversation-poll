<?php

namespace wcf\system\event\listener;

use wcf\data\conversation\message\ConversationMessageEditor;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\poll\ConversationPollHandler;
use wcf\system\poll\PollManager;
use wcf\system\WCF;

class ConversationPollAddListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_POLL || !ConversationPollHandler::getInstance()->canStartPublicPoll()) {
            return;
        }

        $this->$eventName($eventObj);
    }

    /**
     * @param \wcf\form\ConversationEditForm $eventObj
     */
    public function readParameters($eventObj)
    {
        if (ConversationPollHandler::getInstance()->canStartPublicPoll()) {
            if ($eventObj instanceof \wcf\form\ConversationDraftEditForm) {
                $conversationMessage = $eventObj->conversation->getFirstMessage();
                PollManager::getInstance()->setObject(ConversationPollHandler::CONVERSATION_POLL_TYPE, $conversationMessage->getObjectID(), $conversationMessage->pollID);
            } else {
                PollManager::getInstance()->setObject(ConversationPollHandler::CONVERSATION_POLL_TYPE, 0);
            }
        }
    }

    /**
     * @param \wcf\form\ConversationAddForm $eventObj
     */
    public function readFormParameters($eventObj)
    {
        if (ConversationPollHandler::getInstance()->canStartPublicPoll()) {
            PollManager::getInstance()->readFormParameters();
        }
    }

    /**
     * @param \wcf\form\ConversationAddForm $eventObj
     */
    public function validate($eventObj)
    {
        if (ConversationPollHandler::getInstance()->canStartPublicPoll()) {
            PollManager::getInstance()->validate();
        }
    }

    /**
     * @param \wcf\form\ConversationEditForm $eventObj
     */
    public function saved($eventObj)
    {
        if ($eventObj->objectAction->getActionName() == 'create') {
            /** @var \wcf\data\conversation\Conversation */
            $conversation = $eventObj->objectAction->getReturnValues()['returnValues'];
            $conversationMessage = $conversation->getFirstMessage();
            $pollID = PollManager::getInstance()->save($conversationMessage->getObjectID());
            if ($pollID) {
                $taskEditor = new ConversationMessageEditor($conversationMessage);
                $taskEditor->update(array(
                    'pollID' => $pollID
                ));
            }
        } else if ($eventObj->objectAction->getActionName() == 'update') {
            foreach ($eventObj->objectAction->getObjects() as $conversation) {
                $conversationMessage = $conversation->getFirstMessage();
                $pollID = PollManager::getInstance()->save($conversationMessage->getObjectID());
                if ($pollID) {
                    $taskEditor = new ConversationMessageEditor($conversationMessage);
                    $taskEditor->update(array(
                        'pollID' => $pollID
                    ));
                }
            }
        }
    }

    /**
     * @param \workplace\form\TaskEditForm $eventObj
     */
    public function assignVariables($eventObj)
    {
        if (ConversationPollHandler::getInstance()->canStartPublicPoll()) {
            PollManager::getInstance()->assignVariables();
        }
    }
}
