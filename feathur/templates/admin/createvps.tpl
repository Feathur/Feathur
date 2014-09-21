<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2">
{%if isset|ServerList == false}
	<div class="formnote">
		Unfortunately there are no servers added to feathur, so you can not create a VPS.
		<br><br>
		Add a server first, then try again.
	</div>
{%/if}
{%if isset|ServerList == true}
	<script type="text/javascript">
		$(document).ready(function() {
            $(".chosen-select").chosen();
			$("#ServerSelection").change(function() {
				var id = $("#ServerSelection option:selected").attr('value');
                if(id == "z"){
                    $("#CreateForm").html("<p class='formnote'>Please select a server to create a vps on.</p>");
                    return;
                }
				var type =  $("#Server" + id).attr('var');
				$.getJSON("admin.php?view=createvps&action=load_form&type=" + type + " ",function(result){
					$("#CreateForm").html(result.content);
				});
			});
		});
	</script>
    <form name="create" action="admin.php?view=createvps&action=create" method="post" class="pure-form pure-form-aligned">
    <h3 class="title">Create VPS</h3>
        <div class="pure-control-group">
            <label for="user">Select Client:</label>
            <select id="user" name="user" class="chosen-select" style="height: 38px;">
                <option value="z"><i>Select Client</i></option>
                {%foreach user in UserList}
                    <option value="{%?user[id]}">{%?user[email]}</option>
                {%/foreach}
            </select>
        </div>
        <hr>
        <div class="pure-control-group">
            <label for="ServerSelection">Select Server:</label>
            <select name="server" id="ServerSelection">
                <option value="z">--- Choose One ---</option>
                {%foreach server in ServerList}
                    <option value="{%?server[id]}" class="TemplateList" var="{%?server[type]}" id="Server{%?server[id]}">{%?server[name]} ({%?server[type]})</option>
                {%/foreach}
            </select>
        </div>
        <div id="CreateForm"><p class='formnote'>Please select a server to create a vps on.</p></div>
    </form>
{%/if}
</div>