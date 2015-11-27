
	<textarea id="template-code-editor" class="cf-io-code-editor" data-mode="mustache" name="template[code]">{{template/code}}</textarea>
	
	{{#is _current_tab value="#cf-io-panel-template"}}
		{{#script}}
		cfio_init_editor('template-code-editor');
		{{/script}}
	{{/is}}
