/*global opn_ajax_object*/
"use strict";

function myFunc() {
  if (opn_ajax_object === undefined) {
    console.warn("OPN: No data received from server--aborting.");
    return;
  }

  //Save the array of plugin slugs passed from the server to a new array.
  const opnSlugs = opn_ajax_object.slugs;
  const opnSelectors = opn_ajax_object.selectors;
  const opnLocale = opn_ajax_object.locale; // I should perhaps add a default locale somewhere in this file.

  const options = { year: "numeric", month: "long", day: "numeric" };

  //For each plugin slug in the array, search for it in the wordpress.org plugins API, fetch its 'last updated date,' and display it on the admin plugins screen.  Uses the query_plugins action (a search) rather than plugin_information (an exact lookup), because plugin_information returns an actual HTTP 404 for any plugin not in the repo, while query_plugins always returns 200--even when nothing matches.  Since it's a search rather than an exact lookup, results are filtered below for an exact slug match.
  for (let j = 0; j < opnSlugs.length; j++) {
    const opnQueryURL =
      "https://api.wordpress.org/plugins/info/1.2/?" +
      new URLSearchParams({
        action: "query_plugins",
        "request[search]": opnSlugs[j],
        "request[per_page]": "10",
      }).toString();

    //Define the plugin's element on the admin plugins screen.
    const opnSelector =
      "#the-list tr[data-plugin='" + opnSelectors[j] + "'] td.last_updated";
    const opnPluginEl = document.querySelectorAll(opnSelector);

    fetch(opnQueryURL) //Search the wordpress.org plugins API for this plugin's slug.
      .then((response) => {
        return response.json();
      })
      .then((data) => {
        //Since query_plugins is a text search, find the result (if any) whose slug exactly matches the plugin being looked up.
        const opnMatch = (data.plugins || []).find(
          (plugin) => plugin.slug === opnSlugs[j]
        );

        //For every matching element on the page, insert the 'last updated date' from the data fetched from the API.
        for (let i = 0; i < opnPluginEl.length; i++) {
          if (opnMatch && opnMatch.last_updated) {
            const opnEvent = new Date(opnMatch.last_updated.slice(0, 10)); // From the date-time string fetched from the API, extract only the 10-digit date.
            const opnDate = opnEvent.toLocaleDateString(opnLocale, options); // Format the date according to the user's WordPress locale settings.
            opnPluginEl[i].innerHTML = opnDate;
          } else {
            opnPluginEl[i].innerHTML = "Plugin not found on wordpress.org.";
          }
        }
      })
      .catch(() => {
        console.warn("OPN: Unable to reach the server for slug '" + opnSlugs[j] + "'.");
      });
  }
}
myFunc();
