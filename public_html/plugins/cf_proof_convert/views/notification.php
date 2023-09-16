<style type="text/css">
.cfproof_convert_chips_bl
{
  -webkit-animation: cfproof_convert_animate_bl 0.2s ;
  animation: cfproof_convert_animate_bl 0.2s ;
  bottom: 72px;
  left: 50px;
}
.cfproof_convert_chips_none_bl{
  -webkit-animation: cfproof_convert_animate_none_bl 0.4s ;
  animation: cfproof_convert_animate_none_bl 0.4s ;
  bottom: -200px;
  left: 50px;
}
@-webkit-keyframes cfproof_convert_animate_bl {
  from {bottom: -200px; opacity: 0} 
  to {bottom: 72px; opacity: 1}
}
@keyframes cfproof_convert_animate_bl {
  from {bottom: -200px; opacity: 0} 
  to {bottom: 72px; opacity: 1}
}
@-webkit-keyframes cfproof_convert_animate_none_bl {
  from {bottom: 72px; opacity: 1} 
  to {bottom: -200px; opacity: 0;display: none;}
}
@keyframes cfproof_convert_animate_none_bl {
  from {bottom: 72px; opacity: 1} 
  to {bottom: -200px; opacity: 0;display: none;}
}
.cfproof_convert_chips_br{
  -webkit-animation: cfproof_convert_animate_br 0.2s ;
  animation: cfproof_convert_animate_br 0.2s ;
  bottom: 72px;
  right: 50px;
}
.cfproof_convert_chips_none_br{
  -webkit-animation: cfproof_convert_animate_none_br 0.4s ;
  animation: cfproof_convert_animate_none_br 0.4s ;
  bottom: -200px;
  right: 50px;
}
@-webkit-keyframes cfproof_convert_animate_br {
  from {bottom: -200px; opacity: 0} 
  to {bottom: 72px; opacity: 1}
}
@keyframes cfproof_convert_animate_br {
  from {bottom: -200px; opacity: 0} 
  to {bottom: 72px; opacity: 1}
}
@-webkit-keyframes cfproof_convert_animate_none_br {
  from {bottom: 72px; opacity: 1} 
  to {bottom: -200px; opacity: 0;display: none;}
}
@keyframes cfproof_convert_animate_none_br {
  from {bottom: 72px; opacity: 1} 
  to {bottom: -200px; opacity: 0;display: none;}
}
.cfproof_convert_chips_tl{
  -webkit-animation: cfproof_convert_animate_tl 0.2s ;
  animation: cfproof_convert_animate_tl 0.2s ;
  top: 40px;
  left: 40px;
}
.cfproof_convert_chips_none_tl{
  -webkit-animation: cfproof_convert_animate_none_tl 0.4s ;
  animation: cfproof_convert_animate_none_tl 0.4s ;
  top: -200px;
  left: 40px;
}
@-webkit-keyframes cfproof_convert_animate_tl {
  from {top:  -200px; opacity: 0} 
  to {top: 40px; opacity: 1}
}
@keyframes cfproof_convert_animate_tl {
  from {top: -200px; opacity: 0} 
  to {top: 40px; opacity: 1}
}
@-webkit-keyframes cfproof_convert_animate_none_tl {
  from {top: 40px; opacity: 1} 
  to {top: -200px; opacity: 0;display: none;}
}
@keyframes cfproof_convert_animate_none_tl {
  from {top: 40px; opacity: 1} 
  to {top: -200px; opacity: 0;display: none;}
}
.cfproof_convert_chips_tr{
  -webkit-animation: cfproof_convert_animate_tl 0.2s ;
  animation: cfproof_convert_animate_tl 0.2s ;
  top: 40px;
  right: 40px;
}
.cfproof_convert_chips_none_tr{
  -webkit-animation: cfproof_convert_animate_none_tr 0.4s ;
  animation: cfproof_convert_animate_none_tr 0.4s ;
  top: -200px;
  right: 40px;
}
@-webkit-keyframes cfproof_convert_animate_tr {
  from {top: -200px; opacity: 0} 
  to {top: 40px; opacity: 1}
}
@keyframes cfproof_convert_animate_tr {
  from {top: -200px; opacity: 0} 
  to {top: 40px; opacity: 1}
}
@-webkit-keyframes cfproof_convert_animate_none_tr {
  from {top: 40px; opacity: 1} 
  to {top: -0px; opacity: 0;display: none;}
}
@keyframes cfproof_convert_animate_none_tr {
  from {top: 72px; opacity: 1} 
  to {top: -200px; opacity: 0;display: none;}
}
.cfproof_convert_chips_d{
  -webkit-animation: cfproof_convert_animate_d 0.2s ;
  animation: cfproof_convert_animate_d 0.2s ;
  bottom: 72px;
  right: 50px;
}
.cfproof_convert_chips_none_d{
  -webkit-animation: cfproof_convert_animate_none_d 0.4s ;
  animation: cfproof_convert_animate_none_d 0.4s ;
  bottom: -200px;
  left: 50px;
}
@-webkit-keyframes cfproof_convert_animate_d {
  from {bottom:  -200px; opacity: 0} 
  to {bottom: -72px; opacity: 1}
}
@keyframes cfproof_convert_animate_d {
  from {bottom:  -200px; opacity: 0} 
  to {bottom:   -72px; opacity: 1}
}
@-webkit-keyframes cfproof_convert_animate_none_d {
  from {bottom: 72px; opacity: 1} 
  to {bottom: -200px; opacity: 0;display: none;}
}
@keyframes cfproof_convert_animate_none_d {
  from {bottom: 72px; opacity: 1} 
  to {bottom: -200px; opacity: 0;display: none;}
}
</style>

<div class="cfproof_convert_chips" id="cfproof_convert_chips"></div>
<input type="hidden" id="cfproof_convert_user_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>" />
<?php
$cfproof_convert_url =(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')?"https://":"http://"; 
$cfproof_convert_url.= $_SERVER['HTTP_HOST'];      
$cfproof_convert_url.= $_SERVER['REQUEST_URI'];
?>
<script src="<?php echo plugin_dir_url(dirname(__FILE__,1)); ?>assets/js/user_script.js?v=<?= $config_version;?>"></script>

<script type="text/javascript">
  setTimeout(function(){
    var cfproof_convert_count=0;
    cfProofConvertShowNotification(cfproof_convert_count,"yes",1,0,0,'<?=$cfproof_convert_url; ?>','<?=$config_version; ?>');
  },5000)
</script>

