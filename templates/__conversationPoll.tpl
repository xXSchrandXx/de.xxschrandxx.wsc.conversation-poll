{if $messageIDToPoll[$message->getObjectID()]|isset && $messageIDToPoll[$message->getObjectID()]}
	<div class="jsInlineEditorHideContent">
		{include file='poll' poll=$messageIDToPoll[$message->getObjectID()]}
	</div>

