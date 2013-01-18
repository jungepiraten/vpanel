<div style="float:right;">
	{foreach from=$badges item=badge}
		<span class="label" style="background-color:{$badge.color};">{$badge.label|escape:html}</span>
	{/foreach}
</div>
