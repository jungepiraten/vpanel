<html>
<head>
{literal}
<style type="text/css">

* {margin: 0px; padding: 0px;}

</style>
{/literal}
</head>
<body>
{section name=partloop loop=$parts}
<img src="{"file_tokenget_part"|___:$file.fileid:$token:$smarty.section.partloop.index}" />
{/section}
</body>
</html>
