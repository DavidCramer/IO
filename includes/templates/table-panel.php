

	<table class="widefat fixed striped">
		<thead>
			<tr class="cf-io-sort">
			{{#each @root/fields}}
				<span class="cf-io-autocomplete-out-entry-mustache" data-slug="{{id}}" data-label="{{name}}"></span>
				{{#if name}}
					{{#unless hide}}
					<th class="sorted desc">
						
						<span style="display:inline-flex;">{{name}}{{#if sort}}<span class="sorting-indicator"></span>{{/if}}</span>
						<input type="hidden" name="fields[{{@key}}][id]" value="{{@key}}">
					</th>
					{{/unless}}
				{{/if}}
			{{/each}}
			</tr>
		</thead>
		<tbody>

			<tr>
			{{#each @root/fields}}
				{{#if name}}
					{{#unless hide}}
					<td>
						<p class="description">{{name}} sample</p>
					</td>
					{{/unless}}
				{{/if}}
			{{/each}}
			</tr>
			<tr>
			{{#each @root/fields}}
				{{#if name}}
					{{#unless hide}}
					<td>
						<p class="description">{{name}} sample</p>
					</td>
					{{/unless}}
				{{/if}}
			{{/each}}
			</tr>
			<tr>
			{{#each @root/fields}}
				{{#if name}}
					{{#unless hide}}
					<td>
						<p class="description">{{name}} sample</p>
					</td>
					{{/unless}}
				{{/if}}
			{{/each}}
			</tr>
			<tr>
			{{#each @root/fields}}
				{{#if name}}
					{{#unless hide}}
					<td>
						<p class="description">{{name}} sample</p>
					</td>
					{{/unless}}
				{{/if}}
			{{/each}}
			</tr>


		</tbody>				
	</table>



<table>
<thead>
<tr id="cf-io-sort-holder"></tr>
</thead>
</table>
