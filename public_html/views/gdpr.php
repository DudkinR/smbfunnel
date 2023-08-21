<?php
$mysqli=$info['mysqli'];
$loadd=$info['load'];
$gdpr_ob=$loadd->loadGdpr();
if(isset($_POST['delrec']))
{
  $gdpr_ob->deleteRecord($_POST['delrec']);
}
//processDataRequests($do,$id)
if(isset($_POST['data_access_process']))
{
$gdpr_ob->processDataRequests(1,$_POST['data_access_process']);
}
else if(isset($_POST['data_access_dontprocess']))
{
  $gdpr_ob->processDataRequests(0,$_POST['data_access_dontprocess']);
}

if($_SERVER['REQUEST_METHOD']=="POST")
{
  $gdpr_ob->saveDefaultTexts();
}
?>

<?php 
  register_tiny_editor(array("#cookie_message", "#gdpr_defalt_dataacsmsg", "#gdpr_defalt_datrectsmsg")); 
?>

<div class="container-fluid">
<script>
  jQuery(document).ready(function($){
    $(".container-table").hide();
    $(".settingbtn").click(function(){
      $(".container-setting").show();
      $(".container-table").hide();
    });
    $(".tablebtn").click(function(){
      $(".container-setting").hide();
      $(".container-table").show();
});
  });
</script>
<ul class="nav nav-tabs md-tabs nav-justified theme-nav rounded-top  d-flex flex-column flex-sm-row settingtabdesign" role="tablist">
<li class="nav-item  settingbtn settingtabdesign">
    <a class="nav-link active settingtabdesign" data-bs-toggle="tab" href="#home" role="tab">
      <i class="fas fa-cog pr-2"></i><?php w("GDPR Settings"); ?></a>
  </li>
  <li class="nav-item tablebtn settingtabdesign">
    <a class="nav-link settingtabdesign" data-bs-toggle="tab" href="#menu1" role="tab">
      <i class="fas fa-table pr-2"></i><?php w("Cookie Consents & Data Requests"); ?></a>
  </li>

</ul>
</div>
<div class="container-fluid container-setting">
<div class="row">
<div class="col-sm-12 crdcontainer">

<form action="" method="post">
<div class="card card-primary exclude-pnl">
<div class="card-header theme-text bg-white"><?php w("Cookie Consent Setting"); ?></div>
<div class="card-body">
    <div class="row">
<div class="col">
<label class="theme-text"><?php w("Edit Cookie Notice"); ?></label>
   <textarea name="cookie_message" id="cookie_message"><?php echo str_replace("\&quot;","",str_replace("\\r\\n","",htmlentities(get_option('cookie_message')))); ?> </textarea>
   <button type="submit" name="usedefaultcookietext" class="btn theme-button use-default mt-3"><?php w("Use Default Text"); ?></button>
</div>
<div class="col">
<label class="theme-text"><?php w("Cookie Consent Settings"); ?></label>
  <br>
 <div class="row mb-3">
    
      <label  class="col-sm-4 col-form-label align-self-center"><?php w("Text Color"); ?></label>
     
     
      <div class="col text-right">
                    <input class="jscolor form-control form-control-sm" name="cookie_textcolr" value="<?php echo get_option('cookie_textcolr'); ?>">
                  </div>
    </div>
   
    <div class="row mb-3">
    
      <label  class="col-sm-4 col-form-label align-self-center"><?php w("Background Color"); ?></label>
     
     
      <div class="col-sm-8 text-right">

                    <input class="jscolor form-control form-control-sm" name="cookie_backgroundcolor" value="<?php echo get_option('cookie_backgroundcolor'); ?>">
                  </div>
    </div>
   
    
    
    <div class="mb-3 row">
    <label for="boxposition" class="col-sm-4 col-form-label"> <?php w("Box Postion"); ?></label>
    <div class="col-sm-8">
    <select class="form-select form-select-sm"  id="boxposition" name="cookie_position">
    <option <?php if(get_option('cookie_position')=="top:0px;left:0px;"){echo "selected";} ?> value="top:0px;left:0px;"><?php w("Top Left"); ?></option>
    <option <?php if(get_option('cookie_position')=="top:0px;right:0px;"){echo "selected";} ?> value="top:0px;right:0px;"><?php w("Top Right"); ?></option>
    <option <?php if(get_option('cookie_position')=="bottom:0px;left:0px;"){echo "selected";} ?> value="bottom:0px;left:0px;"><?php w("Bottom Left"); ?></option>
    <option <?php if(get_option('cookie_position')=="bottom:0px;right:0px;"){echo "selected";} ?> value="bottom:0px;right:0px;"><?php w("Bottom Right"); ?></option>
</select>
    </div>
</div>
<div class="mb-3 row">
    <label for="acceptbutton" class="col-sm-4 col-form-label"><?php w("Accept Button Text"); ?></label>
    <div class="col-sm-8">
      <input  type="text" class="form-control form-control-sm" id="acceptbutton" name="cookie_buttontext" value="<?php echo get_option('cookie_buttontext');?>">
    </div>
  </div>
  <div class="mb-3 row">
    <label for="donotaccept" class="col-sm-4 col-form-label"><?php w("Do Not  Accept Button text"); ?></label>
    <div class="col-sm-8">
      <input type="text" class="form-control form-control-sm" id="donotaccept" name="dontaccept_cookie_text" value="<?php echo stripslashes(get_option('dontaccept_cookie_text'));?>">
    </div>
  </div>
  <div class="mb-3 row">
    <label for="redirection" class="col-sm-4 col-form-label"><?php w("Redirection Url"); ?></label>
    <div class="col-sm-8">
      <input type="text" class="form-control form-control-sm" id="redirection" name="cookie_redirectoionurl" value="<?php echo get_option('cookie_redirectoionurl');?>">
    </div>
  </div>
  <div class="mb-3 row">
    <label for="fontsize" class="col-sm-4 col-form-label"><?php w("Font Size"); ?></label>
    <div class="col-sm-8">
      <input type="text" class="form-control form-control-sm" id="fontsize" name="cookie_font_size" value="<?php echo get_option('cookie_font_size');?>">
    </div>
  </div>
  <div class="mb-3 row">
    <label for="distance" class="col-sm-4 col-form-label"><?php w("Distance From Border"); ?></label>
    <div class="col-sm-8">
      <input type="text" class="form-control form-control-sm" id="distance" name="cookie_border" value="<?php echo get_option('cookie_border');?>">
    </div>
  </div>
 
  <div class="mb-3 row">
  <label for="customcss" class="col-sm-4 col-form-label align-self-center"><?php w("Custom CSS") ?></label>
    <div class="col-sm-8">
    <textarea class="form-control form-control-sm" id="customcss" rows="4"  name="cookie_style"><?php echo htmlentities(get_option('cookie_style')); ?></textarea>
    </div>
  </div>
   
   
</div>
  </div>
</div></div>

  <div class="row">
  <div class="col-sm-12">
  <div class="card card-primary exclude-pnl">
  <div class="card-header theme-text bg-white border-bottom-0"><?php w("Data Requests Setup"); ?></div>
  <div class="card-body">
  <div class="col-sm-12">
  <div class="input-group">
  <div class="input-group-prepend">
  <div class="input-group-text">
  <input type="checkbox" name="getgdprdatanotice" <?php if(get_option('gdpr_data_notice')=='1'){echo "checked";} ?>>
  </div>
  </div>
  <p class="form-control"><?php w("Get Notification For Data Access/Rectification Requests"); ?></p>
  </div>
  <div class="input-group" style="margin-top:10px;"><div class="input-group-prepend"><span class="input-group-text"><input type="checkbox" name="send_email_with_gdpr_confirmation" <?php if(get_option('send_email_with_gdpr_confirmation')=='1'){echo "checked";} ?>></span></div>
  <p class="form-control"><?php w("Send Email During Confirmation"); ?></p>
 </div>
  <p style="margin-top:10px;margin-bottom:2px;"><?php w("Enter Notification Email Id"); ?></p>
  <input type="email" class="form-control" name="gdprdataacccesrequestemail" placeholder="<?php w("Enter Notification Email") ?>" value="<?php echo get_option('gdpr_notice_email'); ?>">
  <p style="margin-top:10px;margin-bottom:2px;"><?php w("Data Access Page URL"); ?></p>
  <input type="url" class="form-control" placeholder="<?php w("Data Access Page URL"); ?>" value="<?php echo get_option('install_url')."/?page=data_requests"; ?>" onclick=copyText(this.value) data-bs-toggle="tooltip" title="<?php w("Click To Copy"); ?>">
 

  </div>
  <div class="col-sm-12">
  <br>
  <label class="theme-text"><?php w("Setup Confirmation Messages"); ?></label>
  <div class="row">
  <div class="col">
    <label><?php w("Data Access Email Title"); ?></label>
    <input type="text" class="form-control" name="gdpr_dataacess_Sub" placeholder="<?php w("Ex"); ?>: <?php w("Data Access Notice"); ?>" style="margin-bottom: 20px;" value="<?php echo htmlentities(get_option('gdpr_dataacess_Sub')); ?>">
    <label><?php w("Data Access Email Body"); ?></label>
   <textarea id="gdpr_defalt_dataacsmsg" name="gdpr_defalt_dataacsmsg"><?php echo str_replace("\&quot;","",str_replace("\\r\\n","",htmlentities(get_option('gdpr_defalt_dataacsmsg')))); ?> </textarea>

<button type="submit" class="btn theme-button use-default" name="defdatxt"><?php w("Use Default Text"); ?></button>
 </div>
   <div class="col">
   <label><?php w("Enter Email Title For Data Rectification Confirmation"); ?></label>
   <input type="text" class="form-control" name="gdpr_data_rectifi_sub" placeholder="<?php w("Ex:"); ?> <?php w("Data Rectification Notice") ?>" style="margin-bottom: 20px;" value="<?php echo htmlentities(get_option('gdpr_data_rectifi_sub')); ?>">
   <label><?php w("Enter Email Body For Data Rectification Confirmation") ?></label>
   <textarea id="gdpr_defalt_datrectsmsg" name="gdpr_defalt_datrectsmsg"><?php echo str_replace("\&quot;","",str_replace("\\r\\n","",htmlentities(get_option('gdpr_defalt_datrectsmsg')))); ?> </textarea>
<button type="submit" class="btn theme-button use-default" name="defdrrtxt"><?php w("Use Default Text"); ?></button>

 </div></div>
  </div>
  </div></div></div>
</div>

  <br>
  <div class="card card-primary exclude-pnl">
  <div class="card-header theme-text border-bottom-0 bg-white"><?php w("Terms and Conditions and Privacy Policy"); ?></div>
  <div class="card-body">
  <div class="alert alert-info">
  <?php w('For "Terms & Conditions" and "Privacy Policy" you can create pages by clicking on add new page and can categorize as "No Category" for each funnel. Also you can create a single project for those two pages. You can use our templates for "Terms & Conditions" and "Privacy Policy".'); ?>
  </div>
  </div>
  </div>

    <div class="mb-3">
  <button type="submit" class="btn theme-button float-right" style="margin-bottom:20px;" name="save_settings"><?php w("Save Settings"); ?></button>
</div>
  </form>
</div></div></div>
<!--setup ends here-->
<div class="container-fluid container-table">
<div class="row">
<div class="col-sm-12">   
<div class="card card-primary exclude-pnl">
<div class="card-header theme-text"><?php w("Cookie Consent and Data Request Records"); ?></div>
<div class="card-body">
  <p class="theme-text"><?php w("Cookie Consent Records"); ?></p>
 <div class="table-responsive">
 <table class="table table-striped">
 <thead><th>#</th><th><?php w("IP"); ?></th><th><?php w("Status"); ?></th><th><?php w("URL"); ?></th><th><?php w("Date"); ?></th><th><?php w("Action"); ?></th></thead>
 <tbody>
 <?php
 $hashcount=0;
 $currentpage=0;
 if(isset($_GET['cookiehashcount']))
 {
   $hashcount=($_GET['cookiehashcount']*10)-10;
   $currentpage=$_GET['cookiehashcount'];
 }
 $cookiedata=$gdpr_ob->getData('cookie', $currentpage);
 $cookiedata_query=$cookiedata['query'];
 if($cookiedata_query)
 {
 $temptablepref=$info['dbpref'];
 while($r=$cookiedata_query->fetch_object())
 {
   ++$hashcount;
   $pageurl="";
   $pagequery=$mysqli->query("select `filename`,`".$temptablepref."quick_funnels`.baseurl as `baseurl` from `".$temptablepref."quick_pagefunnel` inner join `".$temptablepref."quick_funnels` on `".$temptablepref."quick_pagefunnel`.funnelid=`".$temptablepref."quick_funnels`.id  where `".$temptablepref."quick_pagefunnel`.funnelid='".$r->funnel."' and `".$temptablepref."quick_pagefunnel`.level='".$r->label."'");

 
    if(isset($pagequery->num_rows))
    {
      if($tempfetchurl=$pagequery->fetch_object())
      {
       $pageurl=$tempfetchurl->baseurl."/".$tempfetchurl->filename."/";
      }
    }
  
    $pageurl="<a href='".$pageurl."' target='_BLANK'>".$pageurl."</a>";

   $acceptedcookie="<font color='#ff0066'>".t("Did Not Accept")."</font>";
   if($r->action=='1')
   {
    $acceptedcookie="<font color='green'>".t("Accepted")."</font>";   
   }
   $actionbtn="<form action='' method='post' onsubmit='return confirmDeletion(this,event)'><input type='hidden' name='delrec' value='".$r->id."'><button type='submit' class='btn unstyled-button'><i class='fas fa-trash text-danger' data-bs-toggle='tooltip' title='".t("Delete Record")."'></i></button></form>";
   echo "<tr><td>".t($hashcount)."</td><td>".$r->ip."</td><td>".$acceptedcookie."</td><td>".$pageurl."</td><td>$r->date</td><td>".$actionbtn."</td></tr>";
 }
}
 ?>
 <tr><td colspan=10 class='total-data'><?php w("Total Number Of Entries"); ?>: <?php echo t($cookiedata['total']); ?></td></tr>
 </tbody>
 </table>
 <?php
 $paging_url="index.php?page=gdpr&cookiehashcount";
 echo createPager($cookiedata['total'],$paging_url,$currentpage);
 ?>
 </div>
 <br>
 <p class="theme-text"><?php w("Data Access and Rectification Requests"); ?></p> 
 <div class="table-responsive">
 <table class="table table-striped">
 <thead><th>#</th><th><?php w("Type"); ?></th><th><?php w("IP"); ?></th><th><?php w("Name"); ?></th><th><?php w("Email"); ?></th><th><?php w("Status"); ?></th><th><?php w("URL"); ?></th><th><?php w("Date"); ?></th><th><?php w("Action"); ?></th></thead>
 <tbody>
 <?php
 $hashcount=0;
 $currentpage=0;
 if(isset($_GET['datahashcount']))
 {
   $hashcount=($_GET['datahashcount']*10)-10;
   $currentpage=$_GET['datahashcount'];
 }
 $cookiedata=$gdpr_ob->getData('data', $currentpage);
 $cookiedata_query=$cookiedata['query'];
 if($cookiedata_query)
 {
 $temptablepref=$info['dbpref'];
 while($r=$cookiedata_query->fetch_object())
 {
   ++$hashcount;
   $pageurl="";
   $pagequery=$mysqli->query("select `filename`,`".$temptablepref."quick_funnels`.baseurl as `baseurl` from `".$temptablepref."quick_pagefunnel` inner join `".$temptablepref."quick_funnels` on `".$temptablepref."quick_pagefunnel`.funnelid=`".$temptablepref."quick_funnels`.id  where `".$temptablepref."quick_pagefunnel`.funnelid='".$r->funnel."' and `".$temptablepref."quick_pagefunnel`.level='".$r->label."'");

 
    if(isset($pagequery->num_rows))
    {
      if($tempfetchurl=$pagequery->fetch_object())
      {
       $pageurl=$tempfetchurl->baseurl."/".$tempfetchurl->filename."/";
      }
    }
  
    $pageurl="<a href='".$pageurl."' target='_BLANK'>".$pageurl."</a>";
    $temp_type=ucwords(str_replace("_"," ",$r->type));
    if($r->type=="data_others")
    {
      $temp_type="Others";
    }
    $userdata=json_decode($r->data);
    $status=t("N/A");
    if($userdata->login_status==1)
    {
      $status=t("Loggedin");
    }
    elseif($userdata->login_status==2)
    {
      $status=t("Logged in as \${1}",array($userdata->login_email));
    }


    $process_action="<input type='hidden' name='data_access_process' value='".$r->id."'><button class='btn unstyled-button' data-bs-toggle='tooltip' title='".t("Click To Mark As Processed")."'><i class='fas fa-check text-warning'></i></button>";

    if($r->action=='1')
    {
      $process_action="<input type='hidden' name='data_access_dontprocess' value='".$r->id."'><button class='btn unstyled-button' data-bs-toggle='tooltip' title='".t("Click To Mark As Pending")."'><i class='fas fa-check-double text-success'></i></button>";
    }

    $actionbtn="<table class='actionedittable'><tr><td><form action='' method='post'>".$process_action."</form></td>
    <td><button class='btn unstyled-button' data-bs-toggle='tooltip' title='".t("Download As JSON")."' onclick='downloadRequestedData(this,".$r->id.")'><i class='fas fa-download text-info'></i></button></td>
    <td><form action='' method='post' onsubmit='return confirmDeletion(this,event)'><input type='hidden' name='delrec' value='".$r->id."'><button type='submit' class='btn unstyled-button' data-bs-toggle='tooltip' title='".t("Delete The Request")."'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";

   echo "<tr><td>".t($hashcount)."</td><td>".$temp_type."</td><td>".$r->ip."</td><td>".$userdata->name."</td><td><i class='fas fa-eye text-primary' style='cursor:pointer;' onclick=viewUserData(\"viewuserdata".$hashcount."\") data-bs-toggle='tooltip' title='View Request Detail'></i><input type='hidden' value='".htmlentities($userdata->data)."' id='viewuserdata".$hashcount."'>".$userdata->email."</td><td>".$status."</td><td>".$pageurl."</td><td>$r->date</td><td>".$actionbtn."</td></tr>";
 }
}
 ?>
 <tr><td colspan=10 class="total-data"><center><?php w("Total Number Of Entries"); ?>: <?php echo t($cookiedata['total']); ?></center></td></tr>
 </tbody>
 </table>
</div>
 <?php
  $paging_url="index.php?page=gdpr&datahashcount";
  echo createPager($cookiedata['total'],$paging_url,$currentpage);
 ?>
 </div></div></div></div></div>






<script>
var globdiv;
function viewUserData(id)
{
 var div=document.createElement("div");
 div.setAttribute('class','row');
 var container="<div class='overlay' ><div class='card pnl api-forms'><div class='card-header'>"+t("Detail")+" <i class='fas fa-times closethidiv' style='color:white;right:20px;top:20px;position:absolute;cursor:pointer;'></i></div><div class='card-body purchasedetailqmlr' style='max-height:400px;overflow-y:auto;'>"+t("Loading...")+"</div></div></div>";
 div.innerHTML=container;
 //alert(div);
 var maindiv=document.getElementsByClassName("container-table")[0];
 try{maindiv.removeChild(globdiv);}catch(err){console.log(err.message);}
 globdiv=div;
 maindiv.appendChild(div);
 document.getElementsByClassName("closethidiv")[0].onclick=function(){maindiv.removeChild(div);};
document.getElementsByClassName("purchasedetailqmlr")[0].innerHTML=document.getElementById(id).value;
}
function downloadRequestedData(el,id)
{
  el.disabled=true;
  var server=new XMLHttpRequest();
  server.onreadystatechange=function(){
  
    if(this.readyState==4 && this.status==200)
    {
      var type=this.getResponseHeader('content-type');
      var blob=new Blob([this.response],{type: type});
      console.log(blob);
      var url=URL.createObjectURL(blob);
      var doc=document.createElement("a");
      doc.download="data-access.html";
      doc.href=url;
      doc.click();
      el.disabled=false;
    }
  }
  server.open("POST","index.php?page=download&type=data_request",true);
  server.setRequestHeader('content-type','application/x-www-form-urlencoded');
  server.send("gdpr_req_id="+id);
}
</script>
<style>
  .use-default
  {
    margin-top:5px;
  }
</style>