        {if $session->isSignedIn()}
		</div>
	</div>
	{else}
	</div>
	{/if}
</div>

<hr />

<footer>
	<p>&copy; Junge Piraten e.V. 2012</p>
</footer>

{literal}
<script type="text/javascript">
$("tr").hover(
	function() {
		$(this).find("span.close").stop().animate({"opacity": "0.5"}, "medium");
	},
	function() {
		$(this).find("span.close").stop().animate({"opacity": "0.2"}, "medium");
	});
</script>
{/literal}
</body>
</html>
