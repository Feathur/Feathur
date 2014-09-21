<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2 nofluid">
    <div class="alert warningbox static-alert">
        <p>First run the feathur installer, then fill out the form below.</p>
    </div>
</div>
<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2">
	{%if isset|Errors == true}
        <div class="pure-u-1">
            {%foreach error in Errors}
                <div class="alert {%?error[type]}box nofluid">
                    <div id="Status" style="padding:4px;padding-left:5px;width:95%;">{%?error[content]}</div>
                </div>
                <br><br>
            {%/foreach}
        </div>
	{%/if}
    <form name="input" action="admin.php?view=addserver&action=submitserver" method="post" class="pure-u-1 pure-form pure-form-aligned">
        <h3 class="title">Add Server</h3>
        <div class="pure-control-group">
            <label for="name">Name:</label>
            <input id="name" type="text" name="name" required>
        </div>
        <div class="pure-control-group">
            <label for="Hostname">Hostname:</label>
            <input id="Hostname" type="text" name="hostname" required>
        </div>
        <div class="pure-control-group">
            <label for="Username">Super User (Usually root):</label>
            <input id="Username" type="text" name="username" value="root" required>
        </div>
        <div class="pure-control-group">
            <label for="key">SSH Key:</label>
            <textarea name="key" class="st-forminput" id="key" rows="3" cols="47" required></textarea>
        </div>
        <div class="pure-control-group">
            <label for="ServerType">Server Type:</label>
            <select name="type" id="ServerType" required>
                <option value="openvz">OpenVZ</option>
                <option value="kvm">KVM</option>
            </select>
        </div>
        <div class="pure-control-group">
            <label for="location">Location:</label>
            <input id="location" type="text" name="location" required>
        </div>
        <div class="pure-control-group">
            <label for="volume_group">Volume Group (KVM Only, Ex: vg_1232324):</label>
            <input id="volume_group" type="text" name="volume_group">
        </div>
        <div class="pure-control-group">
            <label for="gemu">QEMU Path (KVM Only, leave blank for default):</label>
            <input id="qemu" type="text" name="qemu">
        </div>
        <hr>
        <p class="formnote">Please double-check everything entered before submitting.</p>
        <button type="submit" class="centered pure-button pure-button-primary button-green" id="addserver">Create Server</button>
    </form>
</div>