<?php
    if( !isset($_COOKIE["device_pixel_ratio"]) ){
    
?>
<script language="javascript">
(function(){
  if( document.cookie.indexOf('device_pixel_ratio') == -1
      && 'devicePixelRatio' in window
      && window.devicePixelRatio == 2 ){

    var date = new Date();
    date.setTime( date.getTime() + 3600000 );

    document.cookie = 'device_pixel_ratio=' + window.devicePixelRatio + ';' +  ' expires=' + date.toGMTString() +'; path=/';
    window.location.reload();
  }
})();
</script>
<?php } ?>