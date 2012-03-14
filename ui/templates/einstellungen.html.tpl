{include file="header.html.tpl" ansicht="Einstellungen" menupunkt="einstellungen"}
{if is_array($errors) && !empty($errors)}
{foreach from=$errors item=error}
<div class="alert fade-in alert-error">
    <a class="close" data-dismiss="alert" href="#">×</a>
    {$error|__|escape:html}
</div>
{/foreach}{/if}
<form action="{"einstellungen"|___}" method="post" class="form-horizontal" id="einstellungen">
 <fieldset>
    <div class="control-group">
        <label class="control-label" for="pw_alt">{"Aktuelles Passwort:"|__}</label>
        <div class="controls">
            <input class="password" type="password" name="pw_alt" id="pw_alt"/>
            <span class="help-inline" style="opacity:0;">{"Bitte das aktuelle Passwort eingeben."|__}</span>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="pw_neu">{"Neues Passwort:"|__}</label>
        <div class="controls">
            <input class="password" type="password" name="pw_neu" id="pw_neu" />
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label" for="pw_neu2">{"Passwort bestätigen:"|__}</label>
        <div class="controls">
            <input class="password" type="password" name="pw_neu2" id="pw_neu2" /><span class="help-inline" style="opacity:0;">{"Die Bestätigung muss mit dem Passwort übereinstimmen."|__}</span>
        </div>
    </div>
    <div class="form-actions">
         <button class="btn btn-primary" type="submit" name="changepassword" value="1" />{"Passwort ändern"|__}</button>
    </div>
 </fieldset>
</form>

{literal}
<script>
$("#einstellungen").submit(
    function() {
        var noError = true;
        if($("#pw_alt").val() == "") {
            $("#pw_alt").parent().parent().attr("class", "control-group error");
           $("#pw_alt").next().animate({"opacity": "1"}, "fast");
           noError = false;
        }
        if($("#pw_neu").val() != $("#pw_neu2").val() )  {
           $("#pw_neu2").parent().parent().attr("class", "control-group error");
           $("#pw_neu2").next().animate({"opacity": "1"}, "fast");
            noError = false;
        }
        return noError
    });

</script>
{/literal}
{include file="footer.html.tpl"}
