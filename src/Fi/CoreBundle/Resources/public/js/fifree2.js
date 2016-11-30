//Funzione che viene richiamata dalle form per consentire la validazione
function presubmit(formid) {
  $("#" + formid).children('input[type="submit"]').click();
}

/**
 * Function that will redirect to a new page & pass data using submit
 * @param {type} path -> new url
 * @param {type} params -> JSON data to be posted
 * @param {type} method -> GET or POST
 * @returns {undefined} -> NA
 */
function gotoUrl(path, params, method) {
  //Null check
  method = method || "post"; // Set method to post by default if not specified.

  // The rest of this code assumes you are not using a library.
  // It can be made less wordy if you use one.
  var form = document.createElement("form");
  form.setAttribute("method", method);
  form.setAttribute("action", path);

  //Fill the hidden form
  if (typeof params === 'string') {
    var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", 'data');
    hiddenField.setAttribute("value", params);
    form.appendChild(hiddenField);
  } else {
    for (var key in params) {
      if (params.hasOwnProperty(key)) {
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", key);
        if (typeof params[key] === 'object') {
          hiddenField.setAttribute("value", JSON.stringify(params[key]));
        } else {
          hiddenField.setAttribute("value", params[key]);
        }
        form.appendChild(hiddenField);
      }
    }
  }

  document.body.appendChild(form);
  form.submit();
}

var delay = (function () {
  var timer = 0;
  return function (callback, ms) {
    clearTimeout(timer);
    timer = setTimeout(callback, ms);
  };
})();


function pad(str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}

function db2data(giorno, senzalinea) {

  senzalinea = senzalinea || false;


  var iniziogiorno = senzalinea ? 6 : 7;
  var finegiorno = senzalinea ? 8 : 9;

  var iniziomese = senzalinea ? 4 : 5;
  var finemese = senzalinea ? 6 : 7;

  var gg = giorno.substring(iniziogiorno, finegiorno);
  var mm = giorno.substring(iniziomese, finemese);
  var anno = giorno.substring(0, 4);

  return gg + "/" + mm + "/" + anno;

}

function data2db(giorno, senzalinea) {

  senzalinea = senzalinea || false;

  var gg = pad(giorno.substring(0, 2), 2);
  var mm = pad(giorno.substring(3, 5), 2);
  var anno = pad(giorno.substring(6, 10), 4);

  return anno + (senzalinea ? "" : "-") + mm + (senzalinea ? "" : "-") + gg;

}

function mostrastorico(nomecampo) {
    
    
    
}