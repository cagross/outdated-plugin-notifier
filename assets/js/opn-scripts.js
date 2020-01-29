'use strict';

//Save the array of plugin slugs passed from the server to a new array.
const opnSlugs = opn_ajax_object.slugs;
const opnSelectors = opn_ajax_object.selectors;

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
					opnPluginEl[ i ].innerHTML = info.last_updated;
					// const event = new Date(Date.UTC(2012, 11, 20, 3, 0, 0));
					// console.log(Date.UTC(2012, 11, 20, 3, 0, 0));
					// console.log('Plugins date = ' + info.last_updated);
					// // console.log(Date.UTC(info.last_updated));
					// console.log('Plugins date object = ' + new Date(info.last_updated));
					// // console.log('Plugins date object = ' + new Date('25 Mar 2015'));
					// console.log(Date.UTC(info.last_updated));
					// console.log(event);
					// console.log(event.toLocaleString('ko-KR', { timeZone: 'UTC' }));

				} else {
					opnPluginEl[ i ].innerHTML = 'Plugin name not found.';
				}
			}
		} );
}
