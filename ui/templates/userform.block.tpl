<form action="{if isset($user)}{"users_details"|___:$user.userid}{else}{"user_create"|___}{/if}" method="post" class="login">
 <fieldset>
  <label for="username">{"Username:"|__}</label>
  <input class="username" type="text" name="username" value="{if isset($user)}{$user.username|escape:html}{/if}" />
  <label for="password">{"Passwort:"|__}</label>
  <input class="password" type="password" name="password" />
  <input class="submit" type="submit" name="save" value="{"Speichern"|__}" />
 </fieldset>
</form>
