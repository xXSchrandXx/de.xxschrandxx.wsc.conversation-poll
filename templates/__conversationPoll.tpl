{if $messageIDToPoll[$message->getObjectID()]|isset && $messageIDToPoll[$message->getObjectID()]}
	<div class="jsInlineEditorHideContent">
		{include file='poll' poll=$messageIDToPoll[$message->getObjectID()]}
	</div>
{/if}

<script data-relocate="true">
  require([
    'WoltLabSuite/Core/Conversation/Ui/Message/Poll'
  ], function (
    { Poll }
  ) {
    new Poll({$message->getObjectID()});
  })
</script>