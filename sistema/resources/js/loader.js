function loadScript(url, callback){
  var script = document.createElement("script")
  script.type = "text/javascript";
  if (script.readyState){  //IE
    script.onreadystatechange = function(){
      if (script.readyState == "loaded" ||
          script.readyState == "complete"){
        script.onreadystatechange = null;
        if (typeof callback == "function") callback();
      }
    };
  } else {
    script.onload = function(){
      if (typeof callback == "function") callback();
    };
  }
  script.src = url;
  document.getElementsByTagName("head")[0].appendChild(script);
}