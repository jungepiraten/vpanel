{foreach from=$errors item=error}
<div class="error">{$error|__|escape:html}</div>
{/foreach}
