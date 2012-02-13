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
<img src="{"file_tokenget_part"|___:$file.fileid:$token:$smarty.section.partloop.index}" width="100%" onClick="if (this.hasAttribute('width')) this.removeAttribute('width'); else this.setAttribute('width', '100%');" />
{/section}
</body>
</html>
