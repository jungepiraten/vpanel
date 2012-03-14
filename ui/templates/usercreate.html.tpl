{include file="header.html.tpl" ansicht="Neuen Benutzer anlegen" menupunkt="user"}
{include file="userform.block.tpl"}
{literal}
<script type="text/javascript">
$("form").submit(
    function() {
        var noError = true;
        if($("#password").val() == "") {
            $("#password").parent().parent().attr("class", "control-group error");
           $("#password").next().animate({"opacity": "1"}, "fast");
           noError = false;
        }
        return noError
    });
</script>
{/literal}
{include file="footer.html.tpl"}
