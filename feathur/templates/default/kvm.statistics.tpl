{%if isset|Statistics == true}
	{%foreach info in Statistics}
		{%if isset|info[iso_sync] == true}
			{%if isempty|info[iso_sync] == false}
				{%if isset|info[sync_error] == true}
					{%if isempty|info[sync_error] == false}
						<div align="center"><div class="alert warningbox" style="width:80%">Warning: Template syncing error. If this message persists for more than 5 minutes contact technical support.</div></div>
					{%/if}
					{%if isempty|info[sync_error] == true}
						<div align="center">
							<div class="alert warningbox" style="width:80%">
								<font color="black">Template Sync Progress:</font>
								<div class="progress progress-success" style="margin-bottom:0;">
									<div class="bar" style="width: {%?info[percent_sync]}%">{%if info[percent_sync] > 25}{%?info[percent_sync]}%{%/if}</div>
								</div>
							</div>
						</div>
					{%/if}
				{%/if}
			{%/if}
		{%/if}
	{%/foreach}
{%/if}