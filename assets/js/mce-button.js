(function() {
	tinymce.PluginManager.add('testimonials_mce_button', function( editor, url ) {
		editor.addButton('testimonials_mce_button', {
			icon: 'client-testimonials',
			tooltip: 'Client Testimonials',
			onclick: function() {
				editor.windowManager.open( {
					title: 'Client Testimonials',
					body: 
					[
						{
							type: 'textbox',
							name: 'fullhd',
							label: 'Items on large Desktop',
							value: '5'
						},
						{
							type: 'textbox',
							name: 'widescreen',
							label: 'Items on Desktop',
							value: '4'
						},
						{
							type: 'textbox',
							name: 'desktop',
							label: 'Items on small Desktop',
							value: '3'
						},
						{
							type: 'textbox',
							name: 'tablet',
							label: 'Items on Tablet',
							value: '2'
						},
						{
							type: 'textbox',
							name: 'mobile',
							label: 'Items on Mobile',
							value: '1'
						},
						{
							type: 'textbox',
							name: 'posts_per_page',
							label: 'Total numbers of Items to show',
							value: '20'
						},
						{
							type: 'listbox',
							name: 'loop',
							label: 'Slider Loop',
								'values': 
								[
									{text: 'On', value: 'true'},
									{text: 'Off', value: 'false'}
								]
						},
						{
							type: 'listbox',
							name: 'autoplay',
							label: 'Slider Autoplay',
								'values': 
								[
									{text: 'On', value: 'true'},
									{text: 'Off', value: 'false'}
								]
						},
						{
							type: 'listbox',
							name: 'nav',
							label: 'Show Slider Nav',
								'values': 
								[
									{text: 'Off', value: 'false'},
									{text: 'On', value: 'true'}
								]
						},
						{
							type: 'listbox',
							name: 'orderby',
							label: 'Order By',
								'values': 
								[
									{text: 'None', value: 'none'},
									{text: 'ID', value: 'ID'},
									{text: 'Date', value: 'date'},
									{text: 'Modified', value: 'modified'},
									{text: 'Rand', value: 'rand'}
								]
						}
					],
					onsubmit: function( e ) {
						editor.insertContent( '[client-testimonials fullhd="' + e.data.fullhd + '" widescreen="' + e.data.widescreen + '" desktop="' + e.data.desktop + '" tablet="' + e.data.tablet + '" mobile="' + e.data.mobile + '" loop="' + e.data.loop + '" autoplay="' + e.data.autoplay + '" nav="' + e.data.nav + '" posts_per_page="' + e.data.posts_per_page + '" orderby="' + e.data.orderby + '"]');
						}
					}
				);
			}
		});
	});
})();