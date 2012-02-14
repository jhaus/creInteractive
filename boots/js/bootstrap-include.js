/* ==========================================================
 * bootstrap-include.js v0.1.0
 * ==========================================================
 * Copyright 2011 CRE Interactive, LLC
 * ========================================================== */

// http://stackoverflow.com/questions/950087/include-javascript-file-inside-javascript-file
// http://api.jquery.com/jQuery.getScript/
// http://www.cryer.co.uk/resources/javascript/script17_include_js_from_js.htm

function jsincl(jsFile)
{
  document.write('<script src="js/'
    + jsFile + '"></scr' + 'ipt>'); 
}

//jsincl('bootstrap-modal.js');
//jsincl('bootstrap-dropdown.js');
//jsincl('bootstrap-scrollspy.js');
jsincl('bootstrap-buttons.js');
//jsincl('bootstrap-tabs.js');
//jsincl('bootstrap-twipsy.js');
//jsincl('bootstrap-popover.js');
//jsincl('bootstrap-alerts.js');

/*
var boot_scripts = {

  //"bootstrap-modal.js",
  //"bootstrap-dropdown.js",
  //"bootstrap-scrollspy.js",
  //"bootstrap-buttons.js"
  //"bootstrap-tabs.js"
  //"bootstrap-twipsy.js",
  //"bootstrap-popover.js",
  //"bootstrap-alerts.js"

};

for each( ( boot_scripts as boot_script ) {

  $.getScript( 'js/' . boot_script );

)};
*/