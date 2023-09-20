<?php
 global $mysqli;
  global $dbpref;
 $table= $dbpref.'spinwheel_setting';
  $id = trim( $mysqli->real_escape_string( $id ) );

 $query ="select * from `".$table."` where id =".$id;
$result = $mysqli->query($query);
$res = $result->fetch_assoc();
$cfspinnerwheelname = $res['cfspinnerwheel'];
$cfspinnernum = $res['cfspinnernum'];
$cfspinfont = $res['cfspinfont'];
$cfspinfontstyle = $res['cfspinfontstyle'];
$cfspinslicefontsize = $res['cfspinslicefontsize'];
$cfspinwheeltype = $res['cfspinwheeltype'];
$cfspinmainheader = $res['cfspinmainheader'];
$cfspinmaifooter = $res['cfspinmaifooter'];
$cfspinner_theme = $res['cfspinner_theme'];
$cfspinnerbgimgurl = $res['cfspinnerbgimgurl'];
$words= $res['cfslicepricenames'];
   $str_array = json_decode($words,true);
$lengtharr =sizeof($str_array);
if($cfspinwheeltype =="Popup")
{
?> 






<div id="myModal" class="modal fade" style="margin-top: 10px;">
  <div class="container-fluid">
  <div class="row justify-content-center col-sm-9 offset-md-2" style=" background-color: aliceblue;
    border: 1px solid #888;">

  <div class="col-sm-6">

  <canvas id="canvas" width="440" height="430"
    data-responsiveMinWidth="180"
    data-responsiveScaleHeight="true"   
    data-responsiveMargin="50"
    >


</canvas>
  </div>
  <div class="col-sm-6">  

  <div id="cfspinner_model" class="cfspinner_model cfspinner_model" style=" max-width: 100%;
   width: 100%;  ">
  <!-- cfspinner_model content -->
    <div class="cfspinner_model-content" style=" margin: 25px;">
      <div class="cfspinner-form">
          <div class="cfspinner_model-header" style="color:#000000!important;text-align:center;">
            <div class="header_text" >
              <?=$cfspinmainheader; ?>
            </div>
            <p>Enter Details and Spin the wheel to win</p>

          </div>
          <div class="cfspinner_model-body" style="max-height:320px;overflow:auto;width:100%;">   
          <form  id="formSubmit">
    <input type="hidden" id="cfspinajax" value="<?php echo get_option("install_url") ?>/index.php?page=ajax">
    <input type="hidden" name="cfspinner_nonce" value="<?=cf_create_nonce('cfspinner_nonce_'.$id .''); ?>"  >
    <input type="hidden" name="cfspinner_wheelid" value="<?php echo $id;?>">
    
    <?php
                $fetch_data= $this->cfspinnerGetFormInput( $id );
                $header_count=0;
                while ( $data  = $fetch_data->fetch_assoc() ) {
                  if(in_array($data['type'], array('text', 'number', 'password', 'email')))
                  {
                    ?>
                
                    <input 
                      type="<?= $data['type'] ?>" 
                      name="<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"  
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                  else if(in_array(trim($data['type']), array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p')))
                  {
                    ++$header_count;
                    echo "<".trim($data['type'])." class='cfspinner-header cfspinner-header-".$header_count."'>".$data['title']."</".trim($data['type']).">";
                  }

                  else if( $data['type'] == "textarea" )
                  {
                      ?>
                
                    <textarea 
                    name="<?=$data['name'];  ?>" 
                    placeholder="<?=$data['placeholder']; ?>"
                    title="<?=$data['title'] ?>"
                    <?php  if(  $data['required']  == 1 ){ echo "required"; } ?>
                    ></textarea> 
                    <?php
                  }
                  else if( $data['type'] == "radio" )
                  {
                      ?>
                
                    <label class='lbl-radio'>
                    <input 
                      type="<?= $data['type'] ?>" 
                      name="<?=$data['name']  ?>"
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                    <?=$data['placeholder']; ?></label>
                    
                    <?php
                  }
                  else if( $data['type'] == "checkbox" )
                  {
                      ?>
                    <label class='lbl-checkbox'>
                    <input 
                      type="<?= $data['type'] ?>" 
                      name="<?=$data['name']  ?>" 
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                        <?=$data['placeholder']; ?></label>
                    
                    <?php
                  }
                  else {
                      ?>
                    <input 
                      type="<?= $data['type'] ?>" 
                      name="<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"
                      title="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                }
               ?>
     

        <button type="submit" class="btn btn-primary btn-lg btn-block"    >Submit</button>
    </form>  

</div>
<div class="cfspinner_model-footer">
              <?= $cfspinmaifooter;  ?>
          </div>
          </div>
</div>
</div>
</div>
</div>
</div>
</div>
<?php }else{?>
 

  <div class="container-fluid">
  <div class="row justify-content-center col-sm-9 offset-md-2" style=" background-color: aliceblue;
    border: 1px solid #888;">
  <div class="col-sm-6">
  <canvas id="canvas" width="440" height="430"
    data-responsiveMinWidth="180"
    data-responsiveScaleHeight="true"   
    data-responsiveMargin="50"
    >


</canvas>
  </div>
  <div class="col-sm-6" style="margin-top: 25px;">
 
  <div id="cfspinner_model" class="cfspinner_model cfspinner_model" style=" max-width: 100%;
   width: 100%;  ">
  <!-- cfspinner_model content -->
    <div class="cfspinner_model-content">
      <div class="cfspinner-form">
          <div class="cfspinner_model-header">
            <div class="header_text" style="color:#000000!important;" >
              <?=$cfspinmainheader; ?>
            </div>
          </div>
          <div class="cfspinner_model-body" style="max-height:320px;overflow:auto;width:100%;">   
          <p>Enter Details and Spin the wheel to win</p>
          <form  id="formSubmit">
    <input type="hidden" id="cfspinajax" value="<?php echo get_option("install_url") ?>/index.php?page=ajax">
    <input type="hidden" name="cfspinner_nonce" value="<?=cf_create_nonce('cfspinner_nonce_'.$id .''); ?>"  >
    <input type="hidden" name="cfspinner_wheelid" value="<?php echo $id;?>">

    <?php
                $fetch_data= $this->cfspinnerGetFormInput( $id );
                $header_count=0;
                while ( $data  = $fetch_data->fetch_assoc() ) {
                  if(in_array($data['type'], array('text', 'number', 'password', 'email')))
                  {
                    ?>
                
                    <input 
                      type="<?= $data['type'] ?>" 
                      name="<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"  
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                  else if(in_array(trim($data['type']), array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p')))
                  {
                    ++$header_count;
                    echo "<".trim($data['type'])." class='cfspinner-header cfspinner-header-".$header_count."'>".$data['title']."</".trim($data['type']).">";
                  }

                  else if( $data['type'] == "textarea" )
                  {
                      ?>
                
                    <textarea 
                    name="<?=$data['name'];  ?>" 
                    placeholder="<?=$data['placeholder']; ?>"
                    title="<?=$data['title'] ?>"
                    <?php  if(  $data['required']  == 1 ){ echo "required"; } ?>
                    ></textarea> 
                    <?php
                  }
                  else if( $data['type'] == "radio" )
                  {
                      ?>
                
                    <label class='lbl-radio'>
                    <input 
                      type="<?= $data['type'] ?>" 
                      name="<?=$data['name']  ?>"
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                    <?=$data['placeholder']; ?></label>
                    
                    <?php
                  }
                  else if( $data['type'] == "checkbox" )
                  {
                      ?>
                    <label class='lbl-checkbox'>
                    <input 
                      type="<?= $data['type'] ?>" 
                      name="<?=$data['name']  ?>" 
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                        <?=$data['placeholder']; ?></label>
                    
                    <?php
                  }
                  else {
                      ?>
                    <input 
                      type="<?= $data['type'] ?>" 
                      name="<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"
                      title="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                }
               ?>
     

        <button type="submit" class="btn btn-primary btn-lg btn-block"    >Submit</button>
    </form>  

</div>
<div class="cfspinner_model-footer">
              <?= $cfspinmaifooter;  ?>
          </div>
         
</div>
</div>
</div>
</div>
</div>
</div>
<?php }?>

<?php  
if($cfspinner_theme == "cfwheelcolbg")
{

?>
  <script>
     
      let theWheel = new Winwheel({
          'numSegments'  : <?php echo $lengtharr;?>,     
          'outerRadius'  : 180,  
          'textFontSize' : <?php echo $cfspinslicefontsize;?>,  
          'segments'     :        
          [
          <?php

             foreach ($str_array as $value) {
?>
             {'fillStyle' : '<?php echo $value['cfslicelabelcolor']; ?>','textFillStyle':'<?php echo $value['cfslicefontcolor']; ?>', 'textFontFamily' : '<?php echo $res['cfspinfont'];  ?>','textFontWeight':'<?php echo $res['cfspinfontstyle'];  ?>', 'text' : '<?php echo $value['cfslicelabel'];?>'},

             <?php }?>
           
          ],
          'animation' :            
          {
              'type'     : 'spinToStop',
                 'pins'         :'true',
                     'duration' : 5,   
              'spins'    : <?php echo $cfspinnernum;?>,    
'callbackFinished' : alertPrize                }
      });
      
      function startSpin()
    {
        theWheel.stopAnimation(false);
 
        theWheel.rotationAngle = theWheel.rotationAngle % 360;
 
        theWheel.startAnimation();
    }
    function alertPrize(indicatedSegment)
            {

              var postData= "action=spinnerwheelprice&"+"&cfspinerwinprize="+indicatedSegment.text;
            $.post( $("#cfspinajax").val() , postData, function( response ){
    
            }); 

                swal("Congratulations", "You have won " + indicatedSegment.text, "success");

            }


  </script>

<?php
}elseif ($cfspinner_theme == "cfwheelimgbg") {
  ?>
 <script>
            let theWheel = new Winwheel({
              'numSegments'  : <?php echo $lengtharr;?>,     
              'outerRadius'       : 150,       // Set outer radius so wheel fits inside the background.
          'textFontSize' : <?php echo $cfspinslicefontsize;?>,       // Set outer radius so wheel fits inside the background.
                'drawMode'          : 'image',   // drawMode must be set to image.
                'drawText'          : true,      // Need to set this true if want code-drawn text on image wheels

              'segments'     :        
          [
          <?php

             foreach ($str_array as $value) {
?>
             {'textFillStyle':'<?php echo $value['cfsliceimgfontcolor']; ?>', 'textFontFamily' : '<?php echo $res['cfspinfont'];  ?>','textFontWeight':'<?php echo $res['cfspinfontstyle'];  ?>', 'text' : '<?php echo $value['cfsliceimglabel'];?>'},

             <?php }?>
           
          ],
                'animation' :                  
                {
                    'type'     : 'spinToStop',
                    'duration' : 5,    
                    'spins'    : <?php echo $cfspinnernum;?>,    
                    'callbackFinished' : alertPrize
                }
            });

    
            let loadedImg = new Image();


            loadedImg.onload = function()
            {
                theWheel.wheelImage = loadedImg;    
                theWheel.draw();                  
            }

          
            loadedImg.src = "<?php echo $cfspinnerbgimgurl;?>";

            function startSpin()
    {
        theWheel.stopAnimation(false);
 
        theWheel.rotationAngle = theWheel.rotationAngle % 360;
 
        theWheel.startAnimation();
    }
    function alertPrize(indicatedSegment)
            {

              var postData= "action=spinnerwheelprice&"+"&cfspinerwinprize="+indicatedSegment.text;
            $.post( $("#cfspinajax").val() , postData, function( response ){
    
            }); 

                swal("Congratulations", "You have won " + indicatedSegment.text, "success");

            }
        </script><?php  }
  ?>

<script type="text/javascript">
var modal = document.getElementById('myModal');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

$(document).ready(function(){


  $("#formSubmit").on("submit", function(event){
            event.preventDefault();
            var postData= "action=spinnerwheelform&"+$(this).serialize();
            $.post( $("#cfspinajax").val() , postData, function( response ){
            //console.log(response);
             var response = JSON.parse(response);

					if(response.statusCode==1){
            startSpin();
                   }
          else{
            swal ( 'Oops' ,  'You already used this email!' ,  'error');
                   }
              
            });
        })

   
      });

</script>

<script>
    $(document).ready(function(){
        $("#myModal").modal('show');
    });

</script>