{include file=header.html.tpl}
{if is_array($errors) && !empty($errors)}{include file=errors.html.tpl errors=$errors}{/if}
<p class="pagetitle">Login</p>
<form action="{"login"|___}" method="post">
 <fieldset>
 <input type="hidden" name="redirect" value="{if isset($smarty.post.redirect)}{$smarty.post.redirect|stripslashes|escape:html}{else}{$smarty.server.REQUEST_URI|escape:html}{/if}" />
 <table>
    <tr>
         <td><label for="username">{"Username:"|__}</label></td>
         <td><input class="username" type="text" name="username" /></td>
    </tr>
    <tr>
         <td><label for="password">{"Passwort:"|__}</label></td>
         <td><input class="password" type="password" name="password" /></td>
    </tr>
    <tr>
         <td><input class="submit" type="submit" name="login" value="{"Anmelden"|__}" /></td>
    </tr>
 </table>
 </fieldset>
</form>
{include file=footer.html.tpl}
