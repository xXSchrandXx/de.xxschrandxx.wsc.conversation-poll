/**
 * @module WoltLabSuite/Core/Conversation/Ui/Message/Poll
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import { AjaxCallbackObject, AjaxCallbackSetup, ResponseData } from "WoltLabSuite/Core/Ajax/Data";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import DomUtil from "WoltLabSuite/Core/Dom/Util";

type ResponseGetPoll = {
    pollID: number | null;
    template: string | null;
};

export class Poll {
    constructor(id: number) {
        EventHandler.add("com.woltlab.wcf.redactor", `autosaveDestroy_messageEditor${id}`, () => {
            var element = document.getElementById(`message${id}`);
            if (element == null) {
                return;
            }
            var messageBody = element.querySelector(".messageBody") as HTMLElement;
            if (messageBody == null) {
                return;
            }
            var messagePoll = messageBody.querySelector(".pollContainer");
            if (messagePoll == null) {
                return;
            }
            messagePoll.parentElement!.remove();
            this.handle(id, messageBody);
        });
    }
    async handle(id: number, messageBody: HTMLElement): Promise<void> {
        const response = (await Ajax.dboAction("getPoll", "wcf\\data\\conversation\\message\\PollConversationMessageAction")
          .objectIds([id])
          .dispatch()) as ResponseGetPoll;
        if (response.pollID == null || response.template == null) {
            return;
        }
        var pollContainer = document.createElement("div");
        pollContainer.className = "jsInlineEditorHideContent";
        DomUtil.setInnerHtml(pollContainer, response.template);

        messageBody.insertAdjacentElement("afterbegin", pollContainer);
    }
}
export default Poll;
