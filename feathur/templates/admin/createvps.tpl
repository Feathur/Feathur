{%if isset|ServerList == false}
	<br><br>
	<div align="center">
		Unfortunately there are no servers added to feathur, so you can not create a VPS.
		<br><br>
		Add a server then try again.
	</div>
{%/if}
{%if isset|ServerList == true}
	<script type="text/javascript">
		$(document).ready(function() {
			$("#ServerSelection").change(function() {
				var id = $(this).attr('value');
				var type =  $("#Server" + id).attr('var');
				$.getJSON("admin.php?view=createvps&action=load_form&type=" + type + " ",function(result){
					$("#CreateForm").html(result.content);
				});
			});
		});
	</script>
	<br><br>
	<div align="center">
		<div class="simplebox grid740" style="text-align:left;">
			<div class="titleh">
				<h3>Create VPS</h3>
			</div>
			<div class="body">
				<form name="create" action="admin.php?view=createvps&action=create" method="post">
					<div class="st-form-line">	
						<span class="st-labeltext">Select Client:</span>	
						<select id="user" name="user" style="width:520px;">
							<option value="z">--- Choose One ---</option>
							{%foreach user in UserList}
								<option value="{%?user[id]}">{%?user[email]}</option>
							{%/foreach}
						</select>
						<div class="clear"></div>
					</div>
					<div class="st-form-line">	
						<span class="st-labeltext">Select Server:</span>	
						<select name="server" id="ServerSelection" style="width:520px;">
							<option value="z">--- Choose One ---</option>
							{%foreach server in ServerList}
								<option value="{%?server[id]}" class="TemplateList" var="{%?server[type]}" id="Server{%?server[id]}">{%?server[name]} ({%?server[type]})</option>
							{%/foreach}
						</select>
						<div class="clear"></div>
					</div>
					<div id="CreateForm"></div>
				</form>
			</div>
		</div>
	</div>
{%/if}