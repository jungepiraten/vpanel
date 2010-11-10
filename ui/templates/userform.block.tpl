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
    <tr>
    </tr>
        <td><input class="submit" type="submit" name="save" value="{"Speichern"|__}" /></td>
    </tr>
 </table>
 </fieldset>
</form>
