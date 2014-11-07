<script type="text/javascript">
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
    
    $("#SettingsForm").submit(function(e) {
        e.preventDefault();
        loading(1);
        var values = $(this).serialize();
        $.ajax({
            url: "admin.php?view=settings&submit=1",
            type: "post",
            data: values,
            success: function(data){
                loading(0);
                var result = $.parseJSON(data);
                $('.ajax-alert').html('<div class="alert ' + result.type + 'box"><p>' + result.result + '</p></div>');
                $('.ajax-alert').css("display","block");
            }
        });
    });
});

</script>

      <div class="tabs primarytabs">
         <div class="tab nth btn1 cur" onclick="showCon(1)"><span>General</span><i class="fa fa-cogs"></i></div>
         <div class="tab nth btn2" onclick="showCon(2)"><span>Mail</span><i class="fa fa-envelope"></i></div>
         <div class="tab nth btn3" onclick="showCon(3)"><span>Bandwidth</span><i class="fa fa-tasks"></i></div>
         <div class="tab nth btn4" onclick="showCon(4)"><span>Templates</span><i class="fa fa-list-alt"></i></div>
      </div>
      
<div id="tabConWrap" class="pure-u-sm-1 pure-u-md-1 pure-u-lg-l pure-u-xl-1-2">
      <form id="SettingsForm" class="whitebox pure-form pure-form-aligned pure-u-1" name="settings" method="post" action="" autocomplete="off">
      
        <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
        <input style="display:none" type="text" name="noautofillusernameremembered"/>
        <input style="display:none" type="password" name="noautofillpasswordremembered"/>
      
        <div class="ajax-alert pure-u-1" style="display: none;"></div>
        <div id="tabCon" class="con1">
            <div id="tabConTxt">
                <div class="pure-control-group">
                    <label for="title">Title:</label>
                    <input name="title" type="text" id="title" value="{%?Title}" /> 
                </div>
                <div class="pure-control-group">
                    <label for="description">Description:</label>
                    <input name="description" type="text" class="st-forminput" id="description" value="{%?Description}" />
                </div>
                <div class="pure-control-group">
                    <label for="panel_url">Panel URL (without http://):</label>
                    <input name="panel_url" type="text" class="st-forminput" id="panel_url" value="{%?PanelURL}" />
                </div>
                <div class="pure-control-group">
                    <label>Matinenance Mode:</label>
                    <label for="maintenance" class="pure-checkbox" style="text-align: left;">
                        <input type="checkbox" name="maintenance" value="1" {%if isset|Maintanance == true}{%if isempty|Maintanance == false}checked{%/if}{%/if} id="maintenance"/> Enabled
                    </label>
                </div>
                <div class="pure-control-group">
                    <label for="update_type">Update Branch:</label>
                    <select name="update_type" id="update_type">
                        <option value="develop" {%if isset|UpdateType == true}{%if UpdateType == develop}selected="selected"{%/if}{%/if}>Development</option>
                        <option value="Testing" {%if isset|UpdateType == true}{%if UpdateType == Testing}selected="selected"{%/if}{%/if}>Testing (not recommended)</option>
                    </select>
                </div>
                <div class="pure-control-group">
                    <label for="template_redone_setting">Template Warning Message (Dashboard):</label>
                    <select name="template_redone_setting" id="template_redone_setting">
                        <option value="0" {%if isset|TemplatesRedone == true}{%if isempty|TemplatesRedone == true}selected="selected"{%/if}{%/if}>Enabled</option>
                        <option value="1" {%if isset|TemplatesRedone == true}{%if isempty|TemplatesRedone == false}selected="selected"{%/if}{%/if}>Disabled</option>
                    </select>
                </div>
            </div>
         </div>
         
         <div id="tabCon" class="con2" style="display: none">
            <div id="tabConTxt">
                <div class="pure-control-group">
                    <label for="mail">Mail Sender Type:</label>
                    <select name="mail" id="mail">
                        <option value="0" {%if isset|Mail == false}selected="selected"{%/if}>Sendmail</option>
                        <option value="1" {%if isset|Mail == true}{%if Mail == 1}selected="selected"{%/if}{%/if}>Send Grid</option>
                        <option value="2" {%if isset|Mail == true}{%if Mail == 2}selected="selected"{%/if}{%/if}>Mandrill</option>
                    </select>
                </div>
                <div id="mail-info">
                    <div class="pure-control-group">
                        <label for="mail_username">SMTP Username:</label>
                        <input name="mail_username" autocomplete="off" type="text" id="mail_username" value="{%if isset|MailUsername == true}{%?MailUsername}{%/if}"><br>
                    </div>
                    <div class="pure-control-group">
                        <label for="mail_password">SMTP Password:</label>
                        <input name="mail_password" autocomplete="off" type="password" {%if isset|MailPassword == true}value="password"{%/if} id="mail_password">
                    </div>
                </div>
            </div>
         </div>
         
         <div id="tabCon" class="con3" style="display: none">
            <div id="tabConTxt">
                <div class="pure-control-group">
                    <label for="bandwidth_accounting">Bandwidth Accounting:</label>
                    <select name="bandwidth_accounting" id="bandwidth_accounting">
                        <option value="upload" {%if isset|BandwidthAccounting == true}{%if BandwidthAccounting == upload}selected="selected"{%/if}{%/if}>Upload Only</option>
                        <option value="download" {%if isset|BandwidthAccounting == true}{%if BandwidthAccounting == download}selected="selected"{%/if}{%/if}>Download Only</option>
                        <option value="both" {%if isset|BandwidthAccounting == true}{%if BandwidthAccounting == both}selected="selected"{%/if}{%/if}>Both (Upload and Download)</option>
                    </select>
                </div>
            </div>
         </div>
         
         <div id="tabCon" class="con4" style="display: none">
            <div id="tabConTxt">
                <p class="formnote" style="margin-top: 0;">Templates can be found in the <b>var/feathur/feathur/templates</b> directory.</p>
                <div class="pure-control-group">
                    <label for="template">User Template:</label>
                    <input name="template" type="text" id="template" value="{%?Template}">
                </div>
                <div class="pure-control-group">
                    <label for="admin_template">Admin Template:</label>
                    <input name="admin_template" type="text" id="admin_template" value="{%?AdminTemplate}" /> 
                </div>
            </div>
         </div>
        <br>
        <div align="center">
            <button type="submit" name="button" id="SettingSubmit" class="pure-button pure-button-primary button-large">Submit Settings</button>
        </div>
	</form>
</div>
<!-- End tabbedConWrap -->
