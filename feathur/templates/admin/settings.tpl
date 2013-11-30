<script type="text/javascript">
	$(function() {
		$( "#tabs" ).tabs();
	});
	$(document).ready(function(){
		$('#mail-info').css('display','none');
		$('#mail').change(function(){
			if(document.getElementById('mail').value == 1){
				$('#mail-info').show('slow');
			} else if(document.getElementById('mail').value == 2){
				 $('#mail-info').show('slow'); 
			} else if(document.getElementById('mail').value == 0){
				 $('#mail-info').hide('slow'); 
			}
		});
		
		$('#mail').change();
		
		$("#SettingsForm").submit(function(event) {
			event.preventDefault();
			$("#Notice").html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			var values = $(this).serialize();
			$.ajax({
				url: "admin.php?view=settings&submit=1",
				type: "post",
				data: values,
				success: function(data){
					console.log(data);
					var result = $.parseJSON(data);
					$('#Notice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div><br><input type="submit" name="button" id="button" value="Submit" class="st-button"/>');
				}
			});
		});
	});
</script>
<br><br>
<div align="center">
	<div id="tabs" style="width:95%;">
		<ul>
			<li><a href="#tabs-1">General</a></li>
			<li><a href="#tabs-2">Mail</a></li>
			<li><a href="#tabs-3">Bandwidth</a></li>
			<li><a href="#tabs-4">Templates</a></li>
		</ul>
		<form id="SettingsForm" name="settings" method="post" action="">
			<div id="tabs-1" align="left">
				<p>
					<div class="st-form-line">	
						<span class="st-labeltext">Title: </span>
						<input name="title" type="text" class="st-forminput" id="title" style="width:400px" value="{%?Title}" /> 
						<div class="clear"></div>
					</div>
					<div class="st-form-line">	
						<span class="st-labeltext">Description: </span>
						<input name="description" type="text" class="st-forminput" id="description" style="width:400px" value="{%?Description}" /> 
						<div class="clear"></div>
					</div>
					<div class="st-form-line">	
						<span class="st-labeltext">Panel URL (without http://): </span>
						<input name="panel_url" type="text" class="st-forminput" id="panel_url" style="width:400px" value="{%?PanelURL}" /> 
						<div class="clear"></div>
					</div>
					<div class="st-form-line">	
						<span class="st-labeltext">Maintenance Mode: </span>
						<label class="margin-right10"><input type="checkbox" name="maintenance" value="1" {%if isset|Maintenance == true}{%if isempty|Maintenance == false}selected="selected"{%/if}{%/if} id="maintenance" class="uniform"/> Enabled</label>
						<div class="clear"></div>
					</div>
					<div class="st-form-line">	
						<span class="st-labeltext">Update Branch:</span>
						<select name="update_type" id="update_type" class="uniform">
							<option value="develop" {%if isset|UpdateType == true}{%if UpdateType == develop}selected="selected"{%/if}{%/if}>Development</option>
							<option value="develop-develop" {%if isset|UpdateType == true}{%if UpdateType == develop-develop}selected="selected"{%/if}{%/if}>Testing (not recommended)</option>
						</select>
						<div class="clear"></div>
					</div>
				</p>
			</div>
			<div id="tabs-2" align="left">
				<p>
					<div class="st-form-line">	
						<span class="st-labeltext">Mail Sender Type:</span>
						<select name="mail" id="mail" class="uniform">
							<option value="0" {%if isset|Mail == false}selected="selected"{%/if}>Sendmail</option>
							<option value="1" {%if isset|Mail == true}{%if Mail == 1}selected="selected"{%/if}{%/if}>Send Grid</option>
							<option value="2" {%if isset|Mail == true}{%if Mail == 2}selected="selected"{%/if}{%/if}>Mandrill</option>
						</select>
						<div class="clear"></div>
					</div>
					<div id="mail-info">
						<div class="st-form-line">	
							<span class="st-labeltext">SMTP Username: </span>
							<input name="mail_username" type="text" class="st-forminput" id="mail_username" style="width:400px" value="{%if isset|MailUsername == true}{%?MailUsername}{%/if}" /> 
							<div class="clear"></div>
						</div>
						<div class="st-form-line">	
							<span class="st-labeltext">SMTP Password: </span>
							<input name="mail_password" type="password" class="st-forminput" {%if isset|MailPassword == true}value="password"{%/if} id="mail_password" style="width:400px" /> 
							<div class="clear"></div>
						</div>
					</div>
				</p>
			</div>
			<div id="tabs-3">
				<p>
					<div class="st-form-line">	
						<span class="st-labeltext">Bandwidth Accounting: </span>
						<select name="bandwidth_accounting" id="bandwidth_accounting" class="uniform">
							<option value="upload" {%if isset|BandwidthAccounting == true}{%if BandwidthAccounting == upload}selected="selected"{%/if}{%/if}>Upload Only</option>
							<option value="download" {%if isset|BandwidthAccounting == true}{%if BandwidthAccounting == download}selected="selected"{%/if}{%/if}>Download Only</option>
							<option value="both" {%if isset|BandwidthAccounting == true}{%if BandwidthAccounting == both}selected="selected"{%/if}{%/if}>Both (Upload and Download)</option>
						</select>
						<div class="clear"></div>
					</div>
				</p>
			</div>
			<div id="tabs-4">
				<p>
					<div class="st-form-line">	
						<span class="st-labeltext">User Template: </span>
						<input name="template" type="text" class="st-forminput" id="template" style="width:400px" value="{%?Template}" /> 
						<div class="clear"></div>
					</div>
					<div class="st-form-line">	
						<span class="st-labeltext">Admin Template: </span>
						<input name="admin_template" type="text" class="st-forminput" id="admin_template" style="width:400px" value="{%?AdminTemplate}" /> 
						<div class="clear"></div>
					</div>
				</p>
			</div>
		</div>
		<br><br>
		<div id="Notice" class="button-box">
			<input type="submit" name="button" id="SettingSubmit" value="Submit" class="st-button"/>
		</div>
	</form>
</div>