<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$("#ForceUpdate").click(function() {
			$("#LoadingImage").css({visibility: "visible"});
			$.getJSON("admin.php?view=update&action=force",function(result){
					$("#page").html(result.content);
					$("#LoadingImage").css({visibility: "hidden"});
			});
		});
	});
</script>
<div id="LoadingImage" align="right" style="padding-right:10px;margin-top:10px;visibility:hidden;"><img src="./templates/default/img/loading/9.gif"></img></div>
{%if isset|Errors == true}
	{%foreach error in Errors}
		<div align="center">
        	<div class="albox small-{%?error[type]}" style="width:60%;">
				{%?error[result]}
			</div>
		</div>
	{%/foreach}
{%/if}
<br><br>
{%if isset|Updates == true}
	{%foreach info in Updates}
		{%if isempty|Outdated == false}
			<div align="center">
				Your copy of Feathur is out of date. Please update as soon as possible!
			</div>
		{%/if}
		<div class="content">
			<div class="simplebox grid360-left" style="height:300px;">
				<div class="titleh">
					<h3>Automatic Updates</h3>
				</div>
				<div class="body padding10">
					<div align="center">
						<br>
						{%if isempty|AutomaticUpdates == true}
							<a class="button-green" href="admin.php?view=update&action=automatic&value=1">Enable Automatic Updates</a>
						{%/if}
						{%if isempty|AutomaticUpdates == false}
							<a class="button-red" href="admin.php?view=update&action=automatic&value=0">Disable Automatic Updates</a>
						{%/if}
						<br><br>
					</div>
				</div>
			</div>
			<div class="simplebox grid360-right" style="height:300px;">
				<div class="titleh">
					<h3>Force Update</h3>
				</div>
				<div class="body padding10">
					<div align="center">
						Your Version: {%?info[your_version]} | Current Version: {%?info[current_version]}<br><br>
						<a id="ForceUpdate" class="icon-button"><img src="templates/default/img/icons/button/download.png" alt="icon" height="18" width="18"><span>Force Update</span></a>
					</div>
				</div>
			</div>
		</div>
	{%/foreach}
{%/if}