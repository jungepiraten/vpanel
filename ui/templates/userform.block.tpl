<form action="{if isset($user)}{"users_details"|___:$user.userid}{else}{"user_create"|___}{/if}" method="post" class="userform">
 <fieldset class="userform">
 <table>
    <tr>
        <td><label for="username">{"Username:"|__}</label></td>
        <td><input class="username" type="text" name="username" value="{if isset($user)}{$user.username|escape:html}{/if}" /></td>
    </tr>
    <tr>
        <td><label for="password">{"Passwort:"|__}</label></td>
        <td><input class="password" type="password" name="password" /></td>
    </tr>
    <tr>
        <td><label for="password">{"APIKey:"|__}</label></td>
        <td><input type="radio" name="apikey" value="generate" /> {"Generieren"|__} <input type="radio" name="apikey" value="remove" /> {"Entfernen"|__}<br />{$user.apikey}</td>
    </tr>
    <tr>
        <td><label for="defaultdokumentkategorieid">{"Standard-Dokumentenkategorie:"|__}</label></td>
        <td>{if isset($user)}
         {include file=dokumentkategoriedropdown.block.tpl fieldname=defaultdokumentkategorieid defaulttext="(Übersicht)" selecteddokumentkategorieid=$user.defaultdokumentkategorieid}
         {else}
         {include file=dokumentkategoriedropdown.block.tpl fieldname=defaultdokumentkategorieid defaulttext="(Übersicht)"}
         {/if}</td>
    </tr>
    <tr>
        <td><label for="defaultdokumentstatusid">{"Standard-Dokumentenstatus:"|__}</label></td>
        <td>{if isset($user)}
         {include file=dokumentstatusdropdown.block.tpl fieldname=defaultdokumentstatusid defaulttext="(Übersicht)" selecteddokumentstatusid=$user.defaultdokumentstatusid}
         {else}
         {include file=dokumentstatusdropdown.block.tpl fieldname=defaultdokumentstatusid defaulttext="(Übersicht)"}
         {/if}</td>
    </tr>
    <tr>
        <td colspan="2"><input class="submit" type="submit" name="save" value="{"Speichern"|__}" /></td>
    </tr>
 </table>
 </fieldset>
</form>
