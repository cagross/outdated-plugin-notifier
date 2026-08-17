/*global opn_ajax_object*/
"use strict";

function myFunc() {
  if (opn_ajax_object === undefined) {
    console.warn("OPN: No data received from server--aborting.");
    return;
  }

  //Save the data passed from the server to local variables.
  const opnSlugs = opn_ajax_object.slugs;
  const opnSelectors = opn_ajax_object.selectors;
  const opnLocale = opn_ajax_object.locale; // I should perhaps add a default locale somewhere in this file.
  const opnAjaxUrl = opn_ajax_object.ajax_url;
  const opnNonce = opn_ajax_object.nonce;

  const options = { year: "numeric", month: "long", day: "numeric" };

  //For each plugin slug in the array, ask this site's own admin-ajax.php (which in turn calls plugins_api() server-side) for that plugin's 'last updated date,' then display it on the admin plugins screen.
  for (let j = 0; j < opnSlugs.length; j++) {
    //Define the plugin's element on the admin plugins screen.
    const opnSelector =
      "#the-list tr[data-plugin='" + opnSelectors[j] + "'] td.last_updated";
    const opnPluginEl = document.querySelectorAll(opnSelector);

    fetch(opnAjaxUrl, {
      method: "POST",
      body: new URLSearchParams({
        action: "opn_get_plugin_info",
        nonce: opnNonce,
        slug: opnSlugs[j],
      }),
    })
      .then((response) => {
        return response.json();
      })
      .then((result) => {
        if (!result.success) {
          console.warn(
            "OPN: " +
              (result.data?.message ||
                "Request failed for slug '" + opnSlugs[j] + "'.")
          );
          return;
        }

        //For every matching element on the page, insert the 'last updated date' returned by the server.
        for (let i = 0; i < opnPluginEl.length; i++) {
          if (result.data.found) {
            const opnEvent = new Date(result.data.last_updated.slice(0, 10)); // From the date-time string returned by the server, extract only the 10-digit date.
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
