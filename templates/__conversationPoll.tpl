{if $__showPoll|isset && $__showPoll && $messageIDToPoll[$message->getObjectID()]|isset && $messageIDToPoll[$message->getObjectID()]}
	<div class="jsInlineEditorHideContent">
		<p>test</p>
		{include file='poll' poll=$messageIDToPoll[$message->getObjectID()]}
	</div>
{/if}