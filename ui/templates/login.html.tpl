{include file=header.html.tpl}
{if is_array($errors) && !empty($errors)}{include file=errors.html.tpl errors=$errors}{/if}
<form action="{"login"|___}" method="post" class="login">
 <fieldset>
  <input type="hidden" name="redirect" value="{if isset($smarty.post.redirect)}{$smarty.post.redirect|stripslashes|escape:html}{else}{$smarty.server.REQUEST_URI|escape:html}{/if}" />
  <label for="username">{"Username:"|__}</label>
  <input class="username" type="text" name="username" />
  <label for="password">{"Passwort:"|__}</label>
  <input class="password" type="password" name="password" />
  <input class="submit" type="submit" name="login" value="{"Anmelden"|__}" />
 </fieldset>
</form>
{include file=footer.html.tpl}
