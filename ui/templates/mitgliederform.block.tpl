<form action="{if isset($mitglied)}{"mitglieder_details"|___:$mitglied.mitgliedid}{else}{"mitglieder_create"|___:$mitgliedschaft.mitgliedschaftid}{/if}" method="post">
 <fieldset>
 {if isset($dokument)}<input type="hidden" name="dokumentid" value="{$dokument.dokumentid}" />{/if}
 <table>
     <tr>
        <th>{"Mitgliedsart:"|__}</th>
        <td><select name="mitgliedschaftid" onChange="toggleMitgliedschaft()">{foreach from=$mitgliedschaften item=m}<option value="{$m.mitgliedschaftid}" {if isset($mitgliedrevision) and $m.mitgliedschaftid == $mitgliedrevision.mitgliedschaft.mitgliedschaftid or $m.mitgliedschaftid == $mitgliedschaft.mitgliedschaftid}selected="selected"{/if}>{$m.label|escape:html}</option>{/foreach}</select></td>
     </tr>
     <tr>
         <th>{"Typ:"|__}</th>
         <td>
          <input type="radio" onChange="toggleJurNatPerson()" name="persontyp" value="nat" {if isset($mitgliedrevision.natperson)}checked="checked"{/if} /> {"Natürliche Person"|__}
          <input type="radio" onChange="toggleJurNatPerson()" name="persontyp" value="jur" {if isset($mitgliedrevision.jurperson)}checked="checked"{/if} /> {"Juristische Person"|__}
         </td>
     </tr>
     <tr id="nat_0">
         <th><label for="name">{"Name:"|__}</label></th>
         <td><input class="vorname" type="text" name="vorname" size="20" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.vorname|escape:html}{/if}" /> <input class="name" type="text" name="name" size="20" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.name|escape:html}{/if}" /></td>
     </tr>
     <tr id="nat_1">
         <th><label for="geburtsdatum">{"Geboren:"|__}</label></th>
         <td><input class="geburtsdatum" type="text" name="geburtsdatum" size="20" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.geburtsdatum|date_format:"%d.%m.%Y"}{/if}" /></td>
     </tr>
     <tr id="nat_2">
         <th><label for="nationalitaet">{"Nationalität:"|__}</label></th>
         <td><input class="nationalitaet" type="text" name="nationalitaet" size="20" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.nationalitaet|escape:html}{/if}" /></td>
     </tr>
     <tr id="jur_0">
         <th><label for="firma">{"Firma:"|__}</label></th>
         <td><input class="firma" type="text" name="firma" size="40" value="{if isset($mitgliedrevision.jurperson)}{$mitgliedrevision.jurperson.label|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <th><label for="strasse">{"Adresse:"|__}</label></th>
         <td><input class="strasse" type="text" name="strasse" size="37" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.strasse|escape:html}{/if}" /> <input class="hausnummer" type="text" name="hausnummer" size="3" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.hausnummer|escape:html}{/if}" /></td>
     </tr>
     <tr>
      <th><label for="ortid">{"Ort:"|__}</label></th>
      <td>
       <input class="plz" type="text" name="plz" id="plz" size="5" autocomplete="off"
        value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.ort.plz|escape:html}{/if}" />
       <input class="ort" type="text" name="ort" id="ort" size="35" autocomplete="off"
        value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.ort.label|escape:html}{/if}" />
       <select name="stateid" id="state">
        {foreach from=$states item=state}
         <option value="{$state.stateid|escape:html}" id="state{$state.stateid|escape:html}"
          {if isset($mitgliedrevision.kontakt) and
              $mitgliedrevision.kontakt.ort.state.stateid == $state.stateid}selected="selected"{/if}>
          {$state.label|escape:html} ({$state.country.label})
         </option>
        {/foreach}
       </select>
       <div id="dropdownorte"><ul></ul></div>
      </td>
     </tr>
     <tr>
         <th><label for="telefon">{"Telefonnummer:"|__}</label></th>
         <td><input class="telefon" type="text" name="telefon" size="30" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.telefon|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <th><label for="handy">{"Handynummer:"|__}</label></th>
         <td><input class="handy" type="text" name="handy" size="30" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.handy|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <th><label for="email">{"EMail-Adresse:"|__}</label></th>
         <td><input class="email" type="text" name="email" size="40" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.email|escape:html}{/if}" /></td>
     </tr>
     <tr id="mitglied_pp">
         <th><label for="mitglied_pp">{"Mitglied PP:"|__}</label></th>
         <td><input class="mitglied_piraten" type="checkbox" name="mitglied_piraten" {if isset($mitgliedrevision) and $mitgliedrevision.mitglied_piraten}checked="checked"{/if}" /></td>
     </tr>
     <tr>
         <th><label for="verteiler_eingetragen">{"Verteiler Eingetragen:"|__}</label></th>
         <td><input class="verteiler_eingetragen" type="checkbox" name="verteiler_eingetragen" {if isset($mitgliedrevision) and $mitgliedrevision.verteiler_eingetragen}checked="checked"{/if}" /></td>
     </tr>
     <tr id="beitrag">
         <th><label for="beitrag">{"Beitrag:"|__}</label></th>
         <td><input class="beitrag" type="text" name="beitrag" size="5" onChange="beitragEdited=true;" value="{if isset($mitgliedrevision)}{$mitgliedrevision.beitrag|string_format:"%.2f"|escape:html}{else}{$mitgliedschaft.defaultbeitrag|string_format:"%.2f"|escape:html}{/if}" /> EUR</td>
     </tr>
     <tr>
         <th colspan="2"><input class="submit" type="submit" name="save" value="{"Speichern"|__}" /></th>
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

var beitragEdited = {/literal}{if isset($mitglied)}true{else}false{/if}{literal};
function toggleMitgliedschaft() {
	var feld = document.getElementsByName('mitgliedschaftid')[0];
	var index = feld.selectedIndex;
	var id = feld.options[index].value;
	var name = feld.options[index].text;
	switch (id) {
	{/literal}{foreach from=$mitgliedschaften item=m}
	case "{$m.mitgliedschaftid}":
		{literal}if (!beitragEdited) {{/literal}
			document.getElementsByName('beitrag')[0].value = "{$m.defaultbeitrag}";
		{literal}}{/literal}
		break;
	{/foreach}{literal}
	}
}
toggleMitgliedschaft();

function VPanel_Dropdownorte() {
	this.inputplz = $('#plz');
	this.inputort = $('#ort');
	this.overlay = $('#dropdownorte');
	this.list = $('#dropdownorte ul');
	this.data = [];
	this.current = -1;
	this.active = false;
	this.ignoreKey = false;
	this.interval = null;
	this.init();
}

Function.prototype.createDelegate = function(scope) {
        var fn = this;
        return function() {
                return fn.apply(scope, arguments);
        }
}

VPanel_Dropdownorte.prototype = {
	init: function() {
		this.inputplz.keydown(this.keyDown.createDelegate(this))
				.blur(this.onBlur.createDelegate(this))
				.focus(this.onFocus.createDelegate(this))
				.keyup(this.onChange.createDelegate(this));
		this.inputort.keydown(this.keyDown.createDelegate(this))
				.blur(this.onBlur.createDelegate(this))
				.focus(this.onFocus.createDelegate(this))
				.keyup(this.onChange.createDelegate(this));
	},
	keyDown: function(e) {
		if(!this.active) return;
		this.ignoreKey = true;
		switch(e.keyCode) {
			case 40: //down
				e.preventDefault();
				this._next();
				break;
			case 38: //up
				e.preventDefault();
				this._prev();
				break;
			case 13: //enter
				if(this.current >= 0 && this.current < this.data.length) {
					this.inputplz.val(this.data[this.current].plz);
					this.inputort.val(this.data[this.current].ort);
					$('#state' + this.data[this.current].stateid).attr('selected', 'selected');
					this._close();
					e.preventDefault();
				}
				break;
			case 27: //esc
				this.inputplz.blur();
				this.inputort.blur();
				break;
			default:
				this.ignoreKey = false;
		}
	},
	onBlur: function() {
		this._close();
	},

	onChange: function() {
		if(this.interval != null) {
			window.clearTimeout(this.interval);
		}
		this.interval = window.setTimeout(this.triggerChange.createDelegate(this),300);
	},
	triggerChange: function() {
		if(this.ignoreKey) {
			this.ignoreKey= false;
			return;
		}
		var plzv = this.inputplz.val();
		var ortv = this.inputort.val();
		if(plzv.trim() == "" && ortv.trim() == "") {
			this._close();
		} else {
			this.search(plzv, ortv);
		}
	},
	
	onFocus: function() {
		this.onChange();
	},
	search: function(plzv, ortv) {
		$.post("{/literal}{"orte_json"|___}{literal}",{
			plz: plzv,
			ort: ortv
		}, this._open.createDelegate(this) ,'json');
	},
		
	_renderData: function(data) {
		this.list.html("")
		this.data = data;
		for(i in data) {
			$('<li></li>').append(
				$('<a></a>').text(data[i].plz + ' ' + data[i].ort).attr('href','#')
			).appendTo( this.list );
		}
	},

	_select: function(i) {
		if(!this.active) return;
		if(i < 0 || i >= this.data.length) return;
		var lis = this.list.children("li");
		lis.removeClass('selected');
		$(lis[i]).addClass('selected');
		this.current = i;
	},
	_next: function() {
		this._select(this.current+1);
	},
	_prev: function() {
		this._select(this.current-1);
	},
	_open: function(data) {
		if(data.length == 0) {
			this._close();
			return;
		}
		this._renderData(data);
		this._select(0);
		this.overlay.show();
		if(!this.active) {
			this.active = true;
			//this..focus();
		}
	},
	_close: function() {
		this.overlay.hide();	
	}
}

$(function() {
	dropdownorte = new VPanel_Dropdownorte();
});

</script>
<style type="text/css">
#dropdownorte ul
	{list-style:none; padding:0px;}
#dropdownorte ul li
	{padding-top:5px; padding-bottom:5px;}
#dropdownorte ul li.selected
	{background-color:#cccccc;}
</style>
{/literal}
