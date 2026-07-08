<!DOCTYPE>
<html>
<head>
</head>
<body>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '1714750472105245', // FB APP ID Varcreative
      xfbml      : false,
      version    : 'v2.6'
    });
  };
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
<button onclick="compartir()">Compartir</button>
<script type="text/javascript">
function compartir() {
  FB.ui({
    method: "feed",
    link: "<?php echo $link ?>",
    caption: "<?php echo $titulo ?>",
    description: "<?php echo $descripcion ?>",
  });  
}
</script>
</body>
</html>