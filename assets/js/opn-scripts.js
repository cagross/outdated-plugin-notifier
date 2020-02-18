'use strict';

//Save the array of plugin slugs passed from the server to a new array.
const opnSlugs = opn_ajax_object.slugs;
const opnSelectors = opn_ajax_object.selectors;
const opnLocale = opn_ajax_object.locale;

// const opnLocale = opn_ajax_object.locale.replace('_', '-');

//For each plugin slug in the array, connect to its page in the wordpress.org plugins API, fetch its 'last updated date,' and display it on the admin plugins screen.
for ( let j = 0; j < opnSlugs.length; j++ ) {
	const opnPluginURL = 'https://api.wordpress.org/plugins/info/1.0/' + opnSlugs[ j ] + '.json';

	fetch( opnPluginURL )//Fetch all plugin data from the wordpress.org plugins API.
		.then( ( response ) => {
			return response.json();
		} )
		.then( ( info ) => {//From plugin data, extract 'last updated date' and insert it into the corresponding element on the page.
		//Define the plugin's element on the admin plugins screen.

			const opnSelector = "#the-list tr[data-plugin='" + opnSelectors[ j ] + "'] td.last_updated";
			const opnPluginEl = document.querySelectorAll( opnSelector );

			//For every matching element on the page, insert the 'last updated date' from the data fetched from the API.
			for ( let i = 0; i < opnPluginEl.length; i++ ) {
				if ( info.last_updated ) {






					// const event = new Date(Date.UTC(2012, 11, 20, 3, 0, 0));
					const event = new Date(info.last_updated.slice(0,10));

					const options = { year: 'numeric', month: 'long', day: 'numeric' };

				
					const testy = event.toLocaleDateString(opnLocale, options);






					// opnPluginEl[ i ].innerHTML = info.last_updated;
					// opnPluginEl[ i ].innerHTML = info.last_updated.slice(0,10);
					// opnPluginEl[ i ].innerHTML = new Date(info.last_updated.slice(0,10));
					opnPluginEl[ i ].innerHTML = testy;








				} else {
					opnPluginEl[ i ].innerHTML = 'Plugin name not found.';
				}
			}
		} );
}
