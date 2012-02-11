{literal}
<script type="text/javascript">

Function.prototype.createDelegate = function(scope) {
        var fn = this;
        return function() {
                return fn.apply(scope, arguments);
        }
}

function VPanel_DropdownMitgliederSuche() {
	this.inputq = $('#mitgliedersuche');
	this.overlay = $('#dropdownmitgliedersuche');
	this.list = $('#dropdownmitgliedersuche ul');
	this.data = [];
	this.current = -1;
	this.active = false;
	this.ignoreKey = false;
	this.interval = null;
	this.query = null;
	this.init();
}

VPanel_DropdownMitgliederSuche.prototype = {
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
					{/literal}{if isset($mitgliedsuchehandler)}{$mitgliedsuchehandler}(this.data[this.current]);{else}location.href = this.data[this.current].location;{/if}{literal}
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
		this._cancel();
		this._close();
	},

	onChange: function() {
		this._cancel();
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
		this.query = $.post("{/literal}{"mitglieder_json"|___}{literal}",{
			q: query
		}, this._open.createDelegate(this) ,'json');
		this.inputq.addClass("loading");
	},
	
	_cancel: function() {
		if (this.query != null) {
			this.query.abort();
			this.query = null;
			this.inputq.removeClass("loading");
		}
	},
	_renderData: function(data) {
		this.inputq.removeClass("loading");
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
		this._cancel();
		this.overlay.hide();	
	}
}

$(function() {
	dropdownmitgliedersuche = new VPanel_DropdownMitgliederSuche();
});

</script>
<style type="text/css">
.suche
	{z-index:5;}
.suche ul
	{list-style:none; padding:0px;}
.suche ul li
	{padding-top:5px; padding-bottom:5px;}
.suche ul li.selected
	{background-color:#cccccc;}
</style>
{/literal}
<form action="" class="suche">
 <fieldset class="suche">
  <input type="text" id="mitgliedersuche" name="mitgliedersuche" autocomplete="off" />
  <div id="dropdownmitgliedersuche"><ul></ul></div>
 </fieldset>
</form>
