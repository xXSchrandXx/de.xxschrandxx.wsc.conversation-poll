<?php

namespace wcf\data\conversation\message;

use wcf\data\poll\Poll;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\poll\ConversationPollHandler;
use wcf\system\WCF;

class PollConversationMessageAction extends ConversationMessageAction
{
    public function validateGetPoll()
    {
        if (!ConversationPollHandler::getInstance()->canStartPublicPoll()) {
            throw new PermissionDeniedException();
        }
    }

    public function getPoll()
    {
        $returnValues = [
            'pollID' => null,
            'template' => null
        ];
        $objectID = $this->getObjectIDs();
        $object = new ConversationMessage(reset($objectID));
        if (isset($object) && $object->getObjectID() && isset($object->pollID) && $object->pollID) {
            $poll = new Poll($object->pollID);
            if (!$poll->getObjectID()) {
                $editor = new ConversationMessageEditor($object);
                $editor->update([
                    'pollID' => null
                ]);
            } else {
                $returnValues = [
                    'pollID' => $poll->getObjectID(),
                    'template' => WCF::getTPL()->fetch('poll', 'wcf', ['poll' => $poll])
                ];
            }
        }
        return $returnValues;
    }
}
