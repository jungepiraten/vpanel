<form action="{if isset($mitlglied)}{"mitglieder_details"|___:$mitglied.mitgliedid}{else}{"mitglieder_create"|___:$mitgliedschaft.mitgliedschaftid}{/if}" method="post">
 <fieldset>
 <table>
     <tr>
         <td>{"Mitgliedsart:"|__}</td>
         <td>{if isset($mitglied)}{$mitglied.mitgliedschaft.label|escape:html}{else}{$mitgliedschaft.label|escape:html}{/if}</td>
     </tr>
     <tr>
         <td><label for="name">{"Name:"|__}</label></td>
         <td><input class="vorname" type="text" name="vorname" size="20" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.vorname|escape:html}{/if}" /> <input class="name" type="text" name="name" size="20" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.name|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <td><label for="geburtsdatum">{"Geboren:"|__}</label></td>
         <td><input class="geburtsdatum" type="text" name="geburtsdatum" size="20" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.geburtsdatum|date_format:"%d.%m.%Y"}{/if}" /></td>
     </tr>
     <tr>
         <td><label for="nationalitaet">{"Nationalität:"|__}</label></td>
         <td><input class="nationalitaet" type="text" name="nationalitaet" size="20" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.nationalitaet|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <td><label for="firma">{"Firma:"|__}</label></td>
         <td><input class="firma" type="text" name="firma" size="40" value="{if isset($mitgliedrevision.jurperson)}{$mitgliedrevision.jurperson.firma|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <td><label for="strasse">{"Adresse:"|__}</label></td>
         <td><input class="strasse" type="text" name="strasse" size="37" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.strasse|escape:html}{/if}" /> <input class="hausnummer" type="text" name="hausnummer" size="3" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.hausnummer|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <td><label for="ortid">{"Ort:"|__}</label></td>
         <td><select name="ortid"><option value="">{"Neu"|__}</option>{foreach from=$orte item=ort}<option value="{$ort.ortid|escape:html}">{$ort.plz|escape:html} {$ort.label|escape:html}</option>{/foreach}</select></td>
     </tr>
     <tr>
         <td><label for="plz">&nbsp;</label></td>
         <td><input class="plz" type="text" name="plz" size="5" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.plz|escape:html}{/if}" /> <input class="ort" type="text" name="ort" size="35" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.ort|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <td><label for="stateid">{"Staat:"|__}</label></td>
         <td><select name="stateid"><option value="">{"Automatisch auswählen"|__}</option>{foreach from=$states item=state}<option value="{$state.stateid|escape:html}">{$state.label|escape:html} ({$state.country.label})</option>{/foreach}</select></td>
     </tr>
     <tr>
         <td><label for="telefon">{"Telefonnummer:"|__}</label></td>
         <td><input class="telefon" type="text" name="telefon" size="30" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.telefon|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <td><label for="handy">{"Handynummer:"|__}</label></td>
         <td><input class="handy" type="text" name="handy" size="30" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.handy|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <td><label for="email">{"EMail-Adresse:"|__}</label></td>
         <td><input class="email" type="text" name="email" size="40" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.email|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <td><label for="mitglied_pp">{"Mitglied PP:"|__}</label></td>
         <td><input class="mitglied_pp" type="checkbox" name="mitglied_pp" {if isset($mitgliedrevision.kontakt) and $mitgliedrevision.kontakt.mitglied_pp}checked="checked"{/if}" /></td>
     </tr>
     <tr>
         <td><label for="verteiler_eingetragen">{"Verteiler Eingetragen:"|__}</label></td>
         <td><input class="verteiler_eingetragen" type="checkbox" name="verteiler_eingetragen" {if isset($mitgliedrevision.kontakt) and $mitgliedrevision.kontakt.verteiler_eingetragen}checked="checked"{/if}" /></td>
     </tr>
     <tr>
         <td><label for="beitrag">{"Beitrag:"|__}</label></td>
         <td><input class="beitrag" type="text" name="beitrag" size="5" value="{if isset($mitglied)}{$mitglied.beitrag|string_format:"%.2f"|escape:html}{else}{$mitgliedschaft.defaultbeitrag|string_format:"%.2f"|escape:html}{/if}" /> EUR</td>
     </tr>
     <tr>
         <td><input class="submit" type="submit" name="save" value="{"Speichern"|__}" /></td>
     </tr>
 </table>
 </fieldset>
</form>
