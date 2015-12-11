	var cf_io_autopopulate_fields = {},io_get_autopopulation_object;
	jQuery( function( $ ){

		Handlebars.registerPartial('filter_query', $('#filters-partial-tmpl').html() );

		io_autopopulate_rebuild_ui = function( filters ){
			cf_io_autopopulate_fields = io_get_autopopulation_object();
			
			var fields = $('.auto-populate-options:checked'),
				current_fields = {};

			if( cf_io_autopopulate_fields.config.io_auto_populate.fields ){
				current_fields 	= cf_io_autopopulate_fields.config.io_auto_populate.fields;
			}
			cf_io_autopopulate_fields.config.io_auto_populate.fields = {};
			cf_io_autopopulate_fields.config.io_auto_populate.forms = io_cf_forms;


			fields.each( function(){
				var field_wrap = $( this ).closest( '.caldera-editor-field-config-wrapper' ),
					field_id = field_wrap.prop('id'),
					field_label = field_wrap.find('input.field-label').val(),
					field_type = field_wrap.find('select.caldera-select-field-type').val(),
					field_poptype = field_wrap.find('select.auto-populate-type').val();

				if( field_poptype === 'form_entries'){

					if( !current_fields[ field_id ] ){
						cf_io_autopopulate_fields.config.io_auto_populate.fields[ field_id ] = {
							id		:	field_id,
							name	:	field_label,
							type	:	field_type
						};
					}else{
						cf_io_autopopulate_fields.config.io_auto_populate.fields[ field_id ] = current_fields[ field_id ];
					}

				}
				
			});
			if( filters ){
				$.extend( true, cf_io_autopopulate_fields.config.io_auto_populate, filters );
			}


			//console.log( cf_io_autopopulate_fields );
			$('#cf-io-autopopulation').val( JSON.stringify( cf_io_autopopulate_fields.config.io_auto_populate ) ).trigger( 'rebuild-autopopulate' );
		}
		
		io_add_node = function(node, node_default){
			var id = 'nd' + Math.round(Math.random() * 99866) + Math.round(Math.random() * 99866),
				newnode = { "_id" : id },
				nodes = node.split('.'),
				node_point_record = nodes.join('.') + '.' + id,
				node_defaults = JSON.parse( '{ "_id" : "' + id + '", "_node_point" : "' + node_point_record + '" }' );

			if( node_default && typeof node_default === 'object' ){				
				$.extend( true, node_defaults, node_default );
			}			
			var node_string = '{ "' + nodes.join( '": { "') + '" : { "' + id + '" : ' + JSON.stringify( node_defaults );
			for( var cls = 0; cls <= nodes.length; cls++){
				node_string += '}';
			}
			var new_nodes = JSON.parse( node_string );
			
			return new_nodes;
		};

		io_get_autopopulation_object = function(){
			data = $('#cf-io-autopopulate-app').formJSON();
			if( !data.config ){
				data = {
					config : {
						io_auto_populate : io_cf_config
					}
				};
			}

			return data;
		}


		$( document ).on('click', '[data-add-query]', function(){
			var clicked = $( this ),
				node = clicked.data('addQuery'),
				defaults = clicked.data('nodeDefault');

				io_autopopulate_rebuild_ui( io_add_node( node, defaults ) );

				
		});


		$(document).on('change', '[data-sync-fields]', function(){
			io_autopopulate_rebuild_ui();
		});

		$(document).on( 'field.added', function(){});
		$(document).on( 'click', '.caldera-editor-header-nav a', function( e ){
			io_autopopulate_rebuild_ui();
		});
		$(document).on('bound.fields', function(){
			$('#cf-io-autopopulation').trigger('rebuild-autopopulate');
		})
		

	});