

var firstClick=false;
var dtstart_str;

function markStart(d){
  document.getElementById(d).classList.add("selected");
}
function unMarkStart(d){
  document.getElementById(d).classList.remove("selected");
}

function handleClick(d){
  if(!firstClick){
    dtstart_str = d;
    markStart(dtstart_str);
    firstClick = true;
  } else {
    firstClick = false;
    unMarkStart(dtstart_str);
    occupy(dtstart_str, d);
    dtstart_str = undefined;
  }
}

function occupy(st, end){
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
  };
 //  window.location.href = window.location.href + "?st=" + st + "&end=" + end;
  xhttp.open("GET", window.location.href + "?st=" + st + "&end=" + end, true);
  xhttp.send();

}
