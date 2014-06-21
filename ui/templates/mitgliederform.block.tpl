<form action="{if isset($mitglied)}{"mitglieder_details"|___:$mitglied.mitgliedid}{else}{"mitglieder_create"|___:$mitgliedtemplate.mitgliedtemplateid}{/if}" method="post" class="form-horizontal">
 <fieldset>
 {if isset($dokument)}<input type="hidden" name="dokumentid" value="{$dokument.dokumentid}" />{/if}

<div class="control-group">
	<label class="control-label" for="eintritt">Eintrittsdatum:</label>
	<div class="controls">
		<input type="date" name="eintritt" value="{if isset($mitglied)}{$mitglied.eintritt|date_format:"%Y-%m-%d"}{else}{$smarty.now|date_format:"%Y-%m-%d"}{/if}" />
	</div>
</div>

{if isset($mitglied.austritt)}
<div class="control-group">
	<label class="control-label" for="austritt">Austrittsdatum:</label>
	<div class="controls">
		<input type="date" name="austritt" value="{$mitglied.austritt|date_format:"%Y-%m-%d"}" />
	</div>
</div>
{/if}

<div class="control-group">
    <label class="control-label" for="gliederungid">{"Gliederung:"|__}</label>
    <div class="controls">
        {if isset($mitgliedtemplate) && isset($mitgliedtemplate.gliederung)}
         <input type="hidden" name="gliederungid" value="{$mitgliedtemplate.gliederung.gliederungid}" />
         {$mitgliedtemplate.gliederung.label|escape:html}
        {elseif isset($mitgliedrevision.gliederung)}
         {include file=gliederungdropdown.block.tpl defaulttext="(auswählen)" selectedgliederungid=$mitgliedrevision.gliederung.gliederungid}
        {else}
         {include file=gliederungdropdown.block.tpl defaulttext="(auswählen)" selectedgliederungid=$session->getDefaultGliederungID()}
        {/if}
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="mitgliedschaftid">{"Mitgliedsart:"|__}</label>
    <div class="controls">
        {if isset($mitgliedtemplate) && isset($mitgliedtemplate.mitgliedschaft)}
          <input type="hidden" name="mitgliedschaftid" value="{$mitgliedtemplate.mitgliedschaft.mitgliedschaftid}" />
          {$mitgliedtemplate.mitgliedschaft.label|escape:html}
         {else}
          <select name="mitgliedschaftid">
           {foreach from=$mitgliedschaften item=m}<option value="{$m.mitgliedschaftid}" {if $m.mitgliedschaftid == $mitgliedrevision.mitgliedschaft.mitgliedschaftid}selected="selected"{/if}>{$m.label|escape:html}</option>{/foreach}
          </select>
         {/if}
    </div>
</div>

<input type="hidden" name="persontyp" value="{if isset($mitgliedrevision.natperson) or $data.natperson}nat{else if isset($mitgliedrevision.jurperson) or $data.jurperson}jur{/if}" />
<div class="tabbable">
 <ul class="nav nav-pills">
  <li {if isset($mitgliedrevision.natperson) or $data.natperson}class="active"{/if}><a href="#nat" data-toggle="tab" onclick="$('input[name=persontyp]').val('nat');">{"Natürliche Person"|__}</a></li>
  <li {if isset($mitgliedrevision.jurperson) or $data.jurperson}class="active"{/if}><a href="#jur" data-toggle="tab" onclick="$('input[name=persontyp]').val('jur');">{"Juristische Person"|__}</a></li>
 </ul>
 <div class="tab-content">
  <div class="tab-pane well {if isset($mitgliedrevision.natperson) or $data.natperson}active{/if}" id="nat">
   <div class="control-group">
    <label class="control-label" for="anrede">{"Anrede:"|__}</label>
    <div class="controls">
     <input type="text" name="anrede" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.anrede|escape:html}{else}{$data.anrede|escape:html}{/if}" style="width:4em;" />
    </div>
   </div>

   <div class="control-group">
    <label class="control-label" for="name">{"Name:"|__}</label>
    <div class="controls">
     <input type="text" name="vorname" autocomplete="off" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.vorname|escape:html}{else}{$data.vorname|escape:html}{/if}" />
     <input type="text" name="name" autocomplete="off" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.name|escape:html}{else}{$data.name|escape:html}{/if}" />
    </div>
   </div>

   <div class="control-group">
    <label class="control-label" for="geburtsdatum">{"Geboren:"|__}</label>
    <div class="controls">
     <input type="date" name="geburtsdatum" autocomplete="off" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.geburtsdatum|date_format:"%Y-%m-%d"}{else}{$data.geburtsdatum}{/if}" />
    </div>
   </div>

   <div class="control-group">
    <label class="control-label" for="nationalitaet">{"Nationalität:"|__}</label>
    <div class="controls">
     <input type="text" name="nationalitaet" value="{if isset($mitgliedrevision.natperson)}{$mitgliedrevision.natperson.nationalitaet|escape:html}{else}{$data.nationalitaet|escape:html}{/if}" />
    </div>
   </div>
  </div>
  <div class="tab-pane well {if isset($mitgliedrevision.jurperson) or $data.jurperson}active{/if}" id="jur">
   <div class="control-group">
    <label class="control-label" for="firma">{"Firma:"|__}</label>
    <div class="controls">
     <input type="text" name="firma" autocomplete="off" value="{if isset($mitgliedrevision.jurperson)}{$mitgliedrevision.jurperson.label|escape:html}{else}{$data.firma|escape:html}{/if}" />
    </div>
   </div>
  </div>
 </div>
</div>

<div class="control-group">
    <label class="control-label" for="adresszusatz">{"Adresszusatz:"|__}</label>
    <div class="controls">
        <input type="text" name="adresszusatz" autocomplete="off" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.adresszusatz|escape:html}{else}{$data.adresszusatz|escape:html}{/if}" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="strasse">{"Adresse:"|__}</label>
    <div class="controls">
        <input type="text" name="strasse" autocomplete="off" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.strasse|escape:html}{else}{$data.strasse|escape:html}{/if}" />
        <input type="text" name="hausnummer" autocomplete="off" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.hausnummer|escape:html}{else}{$data.hausnummer|escape:html}{/if}" style="width:4em;" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="strasse">{"Ort:"|__}</label>
    <div class="controls">
        <input type="text" name="plz" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.ort.plz|escape:html}{else}{$data.plz|escape:html}{/if}" autocomplete="off" style="width:4em;" />
        <input type="text" name="ort" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.ort.label|escape:html}{else}{$data.ort|escape:html}{/if}" autocomplete="off" />
        <select name="stateid">
         <option value="">&nbsp;</option>
         {foreach from=$states item=state}
          <option value="{$state.stateid|escape:html}" id="state{$state.stateid|escape:html}"
           {if (isset($mitgliedrevision.kontakt) and $mitgliedrevision.kontakt.ort.state.stateid == $state.stateid) or $state.stateid == $data.stateid}selected="selected"{/if}>
           {$state.label|escape:html} ({$state.country.label})
          </option>
         {/foreach}
        </select>
        <div id="dropdownorte"><ul></ul></div>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="telefon">{"Telefonnummer:"|__}</label>
    <div class="controls">
        <input type="text" name="telefon" autocomplete="off" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.telefon|escape:html}{else}{$data.telefon|escape:html}{/if}" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="handy">{"Handynummer:"|__}</label>
    <div class="controls">
        <input type="text" name="handy" autocomplete="off" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.handy|escape:html}{else}{$data.handy|escape:html}{/if}" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="email">{"EMail-Adresse:"|__}</label>
    <div class="controls">
        <input type="text" name="email" autocomplete="off" value="{if isset($mitgliedrevision.kontakt)}{$mitgliedrevision.kontakt.email.email|escape:html}{else}{$data.email|escape:html}{/if}" />
        {if isset($mitgliedrevision.kontakt) && count($mitgliedrevision.kontakt.email.bounces) > 0}
          <a href="{"mitglieder_bouncelist"|___:$mitgliedrevision.revisionid}'" class="btn btn-info">{$mitgliedrevision.kontakt.email.bounces|@count} Bounces</a>
        {/if}
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="kontoinhaber">{"Kontoinhaber:"|__}</label>
    <div class="controls">
        <input type="text" name="kontoinhaber" autocomplete="off" value="{if isset($mitgliedrevision.kontakt) && isset($mitgliedrevision.kontakt.konto)}{$mitgliedrevision.kontakt.konto.inhaber|escape:html}{else}{$data.kontoinhaber|escape:html}{/if}" />
    </div>
</div>

<div class="control-group" id="ibanControlGroup">
    <label class="control-label" for="iban">{"Konto (IBAN):"|__}</label>
    <div class="controls iban">
        <div class="input-append">
            <input type="text" class="iban" name="iban"  autocomplete="off"value="{if isset($mitgliedrevision.kontakt) && isset($mitgliedrevision.kontakt.konto)}{$mitgliedrevision.kontakt.konto.iban|escape:html}{else}{$data.iban|escape:html}{/if}" onChange="checkIBan(this)" />
        </div>
        <span class="help-inline"></span>
    </div>
</div>
{literal}
<script type="text/javascript">
<!--

// Modulo 97 for huge numbers given as digit strings.
// JS converts huge numbers into floating points, so modulo-arthmetics will fail.
function mod97(digit_string) {
	var m = 0;
	for (var i = 0; i < digit_string.length; ++i)
		m = (m * 10 + parseInt(digit_string.charAt(i))) % 97;
	return m;
}

function iban2ibancheck(check) {
	check = check.substring(4) + check.substring(0,4);
	check = check.replace("A","10");
	check = check.replace("B","11");
	check = check.replace("C","12");
	check = check.replace("D","13");
	check = check.replace("E","14");
	check = check.replace("F","15");
	check = check.replace("G","16");
	check = check.replace("H","17");
	check = check.replace("I","18");
	check = check.replace("J","19");
	check = check.replace("K","20");
	check = check.replace("L","21");
	check = check.replace("M","22");
	check = check.replace("N","23");
	check = check.replace("O","24");
	check = check.replace("P","25");
	check = check.replace("Q","26");
	check = check.replace("R","27");
	check = check.replace("S","28");
	check = check.replace("T","29");
	check = check.replace("U","30");
	check = check.replace("V","31");
	check = check.replace("W","32");
	check = check.replace("X","33");
	check = check.replace("Y","34");
	check = check.replace("Z","35");
	return check;
}

function checkIBan(field) {
	$("#ibanControlGroup").removeClass("success error").find(".help-inline").text("");
	field.value = field.value.replace(/\s/g, "").toUpperCase();
	if (field.value != "") {
		var check = iban2ibancheck(field.value);
		if (mod97(check) != 1) {
			$("#ibanControlGroup").addClass("error").find(".help-inline").text("Ungültige IBan");
		}
	}
}
checkIBan(document.getElementsByName("iban")[0]);

//--> </script> {/literal}

<div class="control-group">
    <label class="control-label" for="bic">{"BIC:"|__}</label>
    <div class="controls">
        <input type="text" name="bic" value="{if isset($mitgliedrevision.kontakt) && isset($mitgliedrevision.kontakt.konto)}{$mitgliedrevision.kontakt.konto.bic|escape:html}{else}{$data.bic|escape:html}{/if}" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="beitrag">{"Beitrag:"|__}</label>
    <div class="controls">
        <div class="input-append">
            <input type="text" name="beitrag" value="{if isset($mitgliedrevision)}{$mitgliedrevision.beitrag|string_format:"%.2f"|escape:html}{elseif $data.beitrag != null}{$data.beitrag|string_format:"%.2f"|escape:html}{elseif isset($mitgliedtemplate)}{$mitgliedtemplate.beitrag|string_format:"%.2f"|escape:html}{/if}" style="width:4em;text-align:right;" />
            <span class="add-on">EUR</span>
        </div>
        <select name="beitragtimeformatid">
            {foreach from=$beitragtimeformats item=beitragtimeformat}
                <option value="{$beitragtimeformat.beitragtimeformatid|escape:html}" {if (isset($mitgliedrevision) && $mitgliedrevision.beitragtimeformat.beitragtimeformatid == $beitragtimeformat.beitragtimeformatid) || $data.beitragtimeformatid == $beitragtimeformat.beitragtimeformatid}selected="selected"{/if}>{$beitragtimeformat.label|escape:html}</option>
            {/foreach}
        </select>
    </div>
</div>

{foreach from=$flags item=flag}
{assign var=flagid value=$flag.flagid}
<div class="control-group">
    <label class="control-label" for="flags[{$flag.flagid}]">{$flag.label|escape:html}</label>
    <div class="controls">
        <input type="checkbox" name="flags[{$flag.flagid}]" {if isset($mitgliedrevision.flags.$flagid) or isset($data.flags.$flagid)}checked="checked"{/if} />
    </div>
</div>
{/foreach}

{foreach from=$textfields item=textfield}
{assign var=textfieldid value=$textfield.textfieldid}
{assign var=revisiontextfield value=$mitgliedrevision.textfields.$textfieldid}
<div class="control-group">
    <label class="control-label" for="textfields[{$textfield.textfieldid}]">{$textfield.label|escape:html}</label>
    <div class="controls">
        <input type="text" name="textfields[{$textfield.textfieldid}]" value="{if isset($mitgliedrevision.textfields.$textfieldid)}{$revisiontextfield.value|escape:html}{elseif isset($data.textfields.$textfieldid)}{$data.textfields.$textfieldid}{/if}" />
    </div>
</div>
{/foreach}

<div class="control-group">
    <label class="control-label" for="kommentar">{"Kommentar:"|__}</label>
    <div class="controls">
        <textarea name="kommentar" cols="10" rows="3"></textarea>
    </div>
</div>

{if !isset($mitglied)}
<div class="control-group">
    <label class="control-label" for="mailtemplateid">{"Versende Willkommensmail:"|__}</label>
    <div class="controls">
        <select name="mailtemplateid">
         <option name="">{"(keine)"|__}</option>
         {foreach from=$mailtemplates item=mailtemplate}
         <option value="{$mailtemplate.templateid|escape:html}" {if $mitgliedtemplate.createmailtemplate.templateid == $mailtemplate.templateid}selected="selected"{/if}>{$mailtemplate.label|escape:html}</option>
         {/foreach}
        </select>
    </div>
</div>
{/if}

<div class="form-actions">
    <button class="btn btn-primary submit" type="submit" name="save" value="1">{"Speichern"|__}</button>
</div>

 </fieldset>
</form>
{literal}
<script type="text/javascript">

function VPanel_Dropdownorte() {
	this.inputplz = $('input[name=plz]');
	this.inputort = $('input[name=ort]');
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
		$.post("json/orte.php",{
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
#dropdownorte ul{
	list-style:none;padding:0px;
}
#dropdownorte ul li{
	padding-top:5px;padding-bottom:5px;
}
#dropdownorte ul li.selected{
	background-color:#cccccc;
}
</style>
{/literal}

