( function( api ) {

	// Extends our custom "getfit-lite" section.
	api.sectionConstructor['getfit-lite'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );