<form action="{if isset($beitrag)}{"beitraege_details"|___:$beitrag.beitragid}{else}{"beitraege_create"|___}{/if}" method="post">
 <fieldset>
 <table>
    <tr>
        <td><label for="label">{"Titel:"|__}</label></td>
        <td><input type="text" name="label" value="{if isset($beitrag)}{$beitrag.label|escape:html}{/if}" /></td>
    </tr>
    <tr>
        <td><label for="hoehe">{"Beitrag:"|__}</label></td>
        <td><input type="text" name="hoehe" value="{if isset($beitrag)}{$beitrag.hoehe|string_format:"%.2f"}{/if}" /></td>
    <tr>
    </tr>
        <td colspan="2"><input class="submit" type="submit" name="save" value="{"Speichern"|__}" /></td>
    </tr>
 </table>
 </fieldset>
</form>
