<html>
<head>
<link rel="stylesheet" type="text/css" href="ui/style.css" />
<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
<script type="text/javascript" src="ui/jquery-1.4.4.js"></script>
<script type="text/javascript" src="ui/jquery.progressbar.js"></script>
<title>{$ansicht} &bull; VPanel</title>
{literal}
<script type="text/javascript">

function VPanel_Dropdownsearch() {
	this.inputq = $('#search');
	this.overlay = $('#dropdownsearch');
	this.list = $('#dropdownsearch ul');
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

VPanel_Dropdownsearch.prototype = {
	init: function() {
		this.inputq.keydown(this.keyDown.createDelegate(this))
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
			case 38: //down
				e.preventDefault();
				this._prev();
				break;
			case 13: //enter
				if(this.current >= 0 && this.current < this.data.length) {
					this.inputq.val(this.data[this.current].label);
					this._close();
					e.preventDefault();
				}
				break;
			case 27: //esc
				this.inputq.blur();
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
		var q = this.inputq.val();
		if(q.trim() == "") {
			this._close();
		} else {
			this.search(q);
		}
	},
	
	onFocus: function() {
		this.onChange();
	},
	search: function(query) {
		$.post("{/literal}{"mitglieder_json"|___}{literal}",{
			q: query
		}, this._open.createDelegate(this) ,'json');
	},
		
	_renderData: function(data) {
		this.list.html("")
		this.data = data;
		for(i in data) {
			$('<li></li>').append(
				$('<a></a>').text(data[i].label).attr('href',data[i].label)
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
	dropdownsearch = new VPanel_Dropdownsearch();
});

</script>
<style type="text/css">
#dropdownsearch
	{z-index:5;}
#dropdownsearch ul
	{list-style:none; padding:0px;}
#dropdownsearch ul li
	{padding-top:5px; padding-bottom:5px;}
#dropdownsearch ul li.selected
	{background-color:#cccccc;}
</style>
{/literal}
</head>
<body>
<div class="header">
<span class="logo"><span style="color:#ff8d00; font-weight:bolder; font-size:125%;">V</span>Panel</span>
<div class="login">
{if not $session->isSignedIn()}
 <a href="{"login"|___}">{"Anmelden"|__}</a>
{else}
 <a href="{"logout"|___}" class="logout">{"Abmelden"|__}</a>
{/if}
</div>
<form action="">
 <fieldset>
  <input type="text" id="search" name="search" autocomplete="off" />
  <div id="dropdownsearch"><ul></ul></div>
 </fieldset>
</form>
</div>
<div class="navigation">
<ul>
<li><a href="{"index"|___}">{"Startseite"|__}</a></li>
{if $session->isAllowed("users_show")}
<li><a href="{"users"|___}">{"Benutzerverwaltung"|__}</a></li>
{/if}
{if $session->isAllowed("roles_show")}
<li><a href="{"roles"|___}">{"Rollenverwaltung"|__}</a></li>
{/if}
{if $session->isAllowed("mitglieder_show")}
<li><a href="{"mitglieder"|___}">{"Mitgliederverwaltung"|__}</a></li>
{/if}
{if $session->isAllowed("mailtemplates_show")}
<li><a href="{"mailtemplates"|___}">{"Mailverwaltung"|__}</a></li>
{/if}
{if $session->isAllowed("statistik_show")}
<li><a href="{"statistik"|___}">{"Mitgliederstatistik"|__}</a></li>
{/if}
</ul>
</div>

<div class="content">
