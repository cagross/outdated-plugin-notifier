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

  //For each plugin slug in the array, connect to its page in the wordpress.org plugins API, fetch its 'last updated date,' and display it on the admin plugins screen.
  for (let j = 0; j < opnSlugs.length; j++) {
    const opnPluginURL =
      "https://api.wordpress.org/plugins/info/1.0/" + opnSlugs[j] + ".json";

    fetch(opnPluginURL) //Fetch all plugin data from the wordpress.org plugins API.
      .then((response) => {
        return response.json();
      })
      .then((info) => {
        //From plugin data, extract 'last updated date' and insert it into the corresponding element on the page.
        //Define the plugin's element on the admin plugins screen.

        const opnSelector =
          "#the-list tr[data-plugin='" + opnSelectors[j] + "'] td.last_updated";
        const opnPluginEl = document.querySelectorAll(opnSelector);

        //For every matching element on the page, insert the 'last updated date' from the data fetched from the API.
        for (let i = 0; i < opnPluginEl.length; i++) {
          if (info.last_updated) {
            const opnEvent = new Date(info.last_updated.slice(0, 10)); // From the date-time string fetched from the API, extract only the 11-digit date.  The integer '10' here is used to extract the first 10 digits of the string, which contain the date.
            const opnDate = opnEvent.toLocaleDateString(opnLocale, options); // From the 11-digit date string, format it according to the user's WordPress locale settings.
            opnPluginEl[i].innerHTML = opnDate;
          } else {
            opnPluginEl[i].innerHTML = "Plugin not found on wordpress.org.";
          }
        }
      });
  }
}
myFunc();
