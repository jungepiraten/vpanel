{foreach from=$badges item=badge}
	<span class="label" style="float:right; background-color:{$badge.color};">{$badge.label|escape:html}</span>
{/foreach}
