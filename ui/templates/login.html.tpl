{include file="header.html.tpl" ansicht="Login"}
{if is_array($errors) && !empty($errors)}
{foreach from=$errors item=error}
<div class="alert fade-in alert-error">
    <a class="close" data-dismiss="alert" href="#">×</a>
    {$error|__|escape:html}
</div>
{/foreach}{/if}
<form action="{"login"|___}" method="post" class="form-horizontal">
 <fieldset>
 <input type="hidden" name="redirect" value="{if isset($smarty.post.redirect)}{$smarty.post.redirect|stripslashes|escape:html}{else}{$smarty.server.REQUEST_URI|escape:html}{/if}" />
    <div class="control-group">
        <label class="control-label" for="username">{"Username:"|__}</label>
        <div class="controls">
            <input class="username" type="text" name="username" />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="password">{"Passwort:"|__}</label>
        <div class="controls">
            <input class="password" type="password" name="password" />
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary submit" type="submit" name="login" value="1" />{"Anmelden"|__}</button>
        <button class="btn">{"Abbrechen"|__}</button>
    </div>
 </fieldset>
</form>
{include file="footer.html.tpl"}
