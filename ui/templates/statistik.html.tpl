{include file=header.html.tpl ansicht="Mitgliederstatistik"}
<p class="pagetitle">Mitgliederstatistik</p>
<p>
{$mitgliedercount}
{foreach from=$mitgliedercountperms item=permscount}
{$permscount[1]} {$permscount[0]}
{/foreach}
</p>
<p>
{$mitgliedercount}
{foreach from=$mitgliedercountperstate item=perstatecount}
{$perstatecount[1]} {$perstatecount[0]}
{/foreach}
</p>
{include file=footer.html.tpl}
