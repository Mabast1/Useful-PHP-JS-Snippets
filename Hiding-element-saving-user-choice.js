/* Creating a button with tooltip inside an html widget on the front end

<div class="tooltip">
    <span class="tooltiptext">Hide permanently</span>
    <button onclick="myFunction()" id="close-btn"><i class="fa fa-times-circle"></i></button>
</div>

*/
//<script>
function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(";");
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == " ") {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function setCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
  let expires = "expires=" + d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function myFunction() {
  var element = document.getElementById("training-alert");
  element.style.display = "none";
  setCookie("training_banner_cookie_hide", true, 1);
}

function init() {
  var shouldHideBanner = getCookie("training_banner_cookie_hide");
  if (shouldHideBanner) {
    var element = document.getElementById("training-alert");
    element.style.display = "none";
  }
}

init();
//</script>
