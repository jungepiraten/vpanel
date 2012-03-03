{include file="header.html.tpl" ansicht="Einstellungen"}
{if is_array($errors) && !empty($errors)}
{foreach from=$errors item=error}
<div class="error">{$error|__|escape:html}</div>
{/foreach}{/if}
<p class="pagetitle">Einstellungen</p>
<form action="{"einstellungen"|___}" method="post">
 <fieldset>
 <table>
    <tr>
         <td><label for="pw_alt">{"Aktuelles Passwort:"|__}</label></td>
         <td><input class="password" type="password" name="pw_alt" /></td>
    </tr>
    <tr>
         <td><label for="pw_neu">{"Neues Passwort:"|__}</label></td>
         <td><input class="password" type="password" name="pw_neu" /></td>
    </tr>
    <tr>
         <td><label for="pw_neu2">{"Passwort bestätigen:"|__}</label></td>
         <td><input class="password" type="password" name="pw_neu2" /></td>
    </tr>
    <tr>
         <td><input class="submit" type="submit" name="changepassword" value="{"Passwort ändern"|__}" /></td>
    </tr>
 </table>
 </fieldset>
</form>
{include file="footer.html.tpl"}
