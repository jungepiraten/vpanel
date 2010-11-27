<form action="{if isset($mitglied)}{"mitglieder_details"|___:$mitglied.mitgliedid}{else}{"mitglieder_create"|___:$mitgliedschaft.mitgliedschaftid}{/if}" method="post">
 <fieldset>
 <table>
     <tr>
         <td>{"Mitgliedsart:"|__}</td>
        <td><select name="mitgliedschaftid" onChange="toggleMitgliedschaft()">{foreach from=$mitgliedschaften item=m}<option value="{$m.mitgliedschaftid}" {if isset($mitgliedrevision) and $m.mitgliedschaftid == $mitgliedrevision.mitgliedschaft.mitgliedschaftid or $m.mitgliedschaftid == $mitgliedschaft.mitgliedschaftid}selected="selected"{/if}>{$m.label|escape:html}</option>{/foreach}</select></td>
     </tr>
     <tr>
         <td>{"Typ:"|__}</td>
         <td>
          <input type="radio" onChange="toggleJurNatPerson()" name="persontyp" value="nat" {if isset($mitgliedrevision.natperson)}checked="checked"{/if} /> {"Natürliche Person"|__}
          <input type="radio" onChange="toggleJurNatPerson()" name="persontyp" value="jur" {if isset($mitgliedrevision.jurperson)}checked="checked"{/if} /> {"Juristische Person"|__}
         </td>
     </tr>
     <tr id="nat_0">
         <td><label for="name">{"Name:"|__}</label></td>
         <td><input class="vorname" type="text" name="vorname" size="20" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.vorname|escape:html}{/if}" /> <input class="name" type="text" name="name" size="20" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.name|escape:html}{/if}" /></td>
     </tr>
     <tr id="nat_1">
         <td><label for="geburtsdatum">{"Geboren:"|__}</label></td>
         <td><input class="geburtsdatum" type="text" name="geburtsdatum" size="20" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.geburtsdatum|date_format:"%d.%m.%Y"}{/if}" /></td>
     </tr>
     <tr id="nat_2">
         <td><label for="nationalitaet">{"Nationalität:"|__}</label></td>
         <td><input class="nationalitaet" type="text" name="nationalitaet" size="20" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.nationalitaet|escape:html}{/if}" /></td>
     </tr>
     <tr id="jur_0">
         <td><label for="firma">{"Firma:"|__}</label></td>
         <td><input class="firma" type="text" name="firma" size="40" value="{if isset($mitgliedrevision.jurperson)}{$mitgliedrevision.jurperson.label|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <td><label for="strasse">{"Adresse:"|__}</label></td>
         <td><input class="strasse" type="text" name="strasse" size="37" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.strasse|escape:html}{/if}" /> <input class="hausnummer" type="text" name="hausnummer" size="3" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.hausnummer|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <td><label for="ortid">{"Ort:"|__}</label></td>
         <td><select name="ortid" onChange="showhideNeuerOrt()"><option value="">{"Neu"|__}</option>{foreach from=$orte item=ort}<option value="{$ort.ortid|escape:html}" {if $ort.ortid == $mitgliedrevision.kontakt.ort.ortid}selected="selected"{/if}>{$ort.plz|escape:html} {$ort.label|escape:html}</option>{/foreach}</select></td>
     </tr>
     <tr id="neuerort">
         <td><label for="plz">&nbsp;</label></td>
         <td><input class="plz" type="text" name="plz" size="5" /> <input class="ort" type="text" name="ort" size="35" /> <select name="stateid">{foreach from=$states item=state}<option value="{$state.stateid|escape:html}">{$state.label|escape:html} ({$state.country.label})</option>{/foreach}</select></td>
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
     <tr id="mitglied_pp">
         <td><label for="mitglied_pp">{"Mitglied PP:"|__}</label></td>
         <td><input class="mitglied_piraten" type="checkbox" name="mitglied_piraten" {if isset($mitgliedrevision) and $mitgliedrevision.mitglied_piraten}checked="checked"{/if}" /></td>
     </tr>
     <tr>
         <td><label for="verteiler_eingetragen">{"Verteiler Eingetragen:"|__}</label></td>
         <td><input class="verteiler_eingetragen" type="checkbox" name="verteiler_eingetragen" {if isset($mitgliedrevision) and $mitgliedrevision.verteiler_eingetragen}checked="checked"{/if}" /></td>
     </tr>
     <tr id="beitrag">
         <td><label for="beitrag">{"Beitrag:"|__}</label></td>
         <td><input class="beitrag" type="text" name="beitrag" size="5" value="{if isset($mitgliedrevision)}{$mitgliedrevision.beitrag|string_format:"%.2f"|escape:html}{else}{$mitgliedschaft.defaultbeitrag|string_format:"%.2f"|escape:html}{/if}" /> EUR</td>
     </tr>
     <tr>
         <td><input class="submit" type="submit" name="save" value="{"Speichern"|__}" /></td>
     </tr>
 </table>
 </fieldset>
</form>
{literal}
<script type="text/javascript">
function toggleJurNatPerson() {
	var nat_display = 'none';
	var jur_display = 'none';
	if (document.getElementsByName('persontyp')[0].checked) {
		nat_display = 'table-row';
	}
	if (document.getElementsByName('persontyp')[1].checked) {
		jur_display = 'table-row';
	}
	var d, i;
	i = 0;
	do {
		d = document.getElementById('nat_' + i++);
		if (d != null) {
			d.style.display = nat_display;	
		}
	} while (d != null);
	i = 0;
	do {
		d = document.getElementById('jur_' + i++);
		if (d != null) {
			d.style.display = jur_display;	
		}
	} while (d != null);
}
toggleJurNatPerson();

function showhideNeuerOrt() {
	if (document.getElementsByName('ortid')[0].value == '') {
		document.getElementById('neuerort').style.display = 'table-row';
	} else {
		document.getElementById('neuerort').style.display = 'none';
        document.getElementsByClassName('plz')[0].value = '';
        document.getElementsByClassName('ort')[0].value = '';
        document.getElementsByName('stateid')[0].selectedIndex = 0;
	}
}
showhideNeuerOrt();

showhideNeuerOrt();
function toggleMitgliedschaft() {
    var art = document.getElementsByName('mitgliedschaftid')[0].options[document.getElementsByName('mitgliedschaftid')[0].selectedIndex].text
    document.getElementsByName('titleart')[0].innerHTML = art;
	switch (art) {
	case "Ordentliches Mitglied":
		document.getElementById('beitrag').style.display = 'none';
        document.getElementsByName('beitrag')[0].value = "12.00";
		document.getElementById('mitglied_pp').style.display = 'table-row';
		break;
	case "Fördermitglied":
		document.getElementById('beitrag').style.display = 'table-row';
        document.getElementsByName('beitrag')[0].value = "12.00";
		document.getElementById('mitglied_pp').style.display = 'none';
        document.getElementsByName('mitglied_piraten')[0].checked = false;
		break;
	case "Ehrenmitglied":
		document.getElementById('beitrag').style.display = 'none';
        document.getElementsByName('beitrag')[0].value = "0";
		document.getElementById('mitglied_pp').style.display = 'none';
        document.getElementsByName('mitglied_piraten')[0].checked = false;
		break;
	default:
		document.getElementById('beitrag').style.display = 'table-row';
        document.getElementsByName('beitrag')[0].value = "12.00";
		document.getElementById('mitglied_pp').style.display = 'table-row';
	}
}
toggleMitgliedschaft()
</script>
{/literal}
