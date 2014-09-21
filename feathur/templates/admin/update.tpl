<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$("#ForceUpdate").click(function() {
			loading(1);
			$.getJSON("admin.php?view=update&action=force",function(result){
                $("#page").html(result.content);
                loading(0);
			});
		});
	});
</script>

{%if isset|Errors == true}
	{%foreach error in Errors}
		<div align="center">
        	<div class="alert {%?error[type]}box" style="width:60%;">
				{%?error[result]}
			</div>
		</div>
	{%/foreach}
{%/if}
{%if isset|Updates == true}
	{%foreach info in Updates}
		<div class="pure-u-1">
        
            {%if isempty|Outdated == false}
                <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2" style="margin: 10px 0;">
                    <div class="alert warningbox static-alert">
                        <p>Your copy of Feathur is out of date. Please update as soon as possible!</p>
                    </div>
                </div>
            {%/if}
        
			<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2" style="margin: 0;">
                <div class="outlined whitebox">
                    <h3 class="title">Updates</h3>
                    <div>
                        <div align="center" class="pure-g">
                            <br>
                            <div class="pure-u-1-2">
                                {%if isempty|AutomaticUpdates == true}
                                    <a class="pure-button button-green pure-button-primary button-xlarge" href="admin.php?view=update&action=automatic&value=1">Enable Automatic Updates</a>
                                {%/if}
                                {%if isempty|AutomaticUpdates == false}
                                    <a class="pure-button button-red pure-button-primary button-xlarge" href="admin.php?view=update&action=automatic&value=0">Disable Automatic Updates</a>
                                {%/if}
                            </div>
                            <div class="pure-u-1-2">
                                <a id="ForceUpdate" class="pure-button button-orange pure-button-primary button-xlarge"><i class="fa fa-download"></i> Force Update</a>
                            </div>
                            <br><br>
                            <p class="formnote pure-u-1">Your Version: <b>{%?info[your_version]}</b>  -  Current Version: <b>{%?FeathurVersion}</b></p>
                            <br><br>
                        </div>
                    </div>
                </div>
			</div>
		</div>
	{%/foreach}
{%/if}