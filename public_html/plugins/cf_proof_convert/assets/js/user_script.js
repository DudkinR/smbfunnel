"use strict";
function cfProofConvertShowNotification(c=0,rotate=null,count=1,scount=0,new_nex=0,url=null,config_version=0)
{
  let delay_time=6;
  let showing_time=3;
  let i=c;
  let count_all=count;
  let setup_count=scount;
  let new_next=new_nex;
  let rotative=rotate;
  let cfproof_convert_chips=document.getElementById("cfproof_convert_chips");
  let ajax=document.getElementById("cfproof_convert_user_ajax").value;
  let xhttp=new XMLHttpRequest();
  xhttp.onreadystatechange=function(){
    if(this.readyState==4 && this.status==200){
      let data = JSON.parse(this.responseText);
      console.log(data);
      if(data.count>0){
        delay_time=data.delay_time*1000;
        showing_time=data.showing_time*1000;
        let css_file = document.createElement("link");
        css_file.setAttribute("rel", "stylesheet");
        css_file.setAttribute("type", "text/css");
        css_file.setAttribute("id", "cfproof_convert_css_file");
        css_file.setAttribute("href", data.css_file);
        document.getElementsByTagName("head")[0].appendChild(css_file);
        let custom_css = document.createElement("style");
        custom_css.setAttribute("id", "cfproof_convert_custom_css");
        custom_css.innerHTML=data.custom_css;
        document.getElementsByTagName("head")[0].appendChild(custom_css);
        if( data.output != undefined )
        {
          cfproof_convert_chips.innerHTML=data.output;
          cfproof_convert_chips.classList.remove("cfproof_convert_chips_none_bl"); 
          cfproof_convert_chips.classList.remove("cfproof_convert_chips_none_br"); 
          cfproof_convert_chips.classList.remove("cfproof_convert_chips_none_tl"); 
          cfproof_convert_chips.classList.remove("cfproof_convert_chips_none_tr");
          cfproof_convert_chips.classList.remove("cfproof_convert_chips_bl"); 
          cfproof_convert_chips.classList.remove("cfproof_convert_chips_br"); 
          cfproof_convert_chips.classList.remove("cfproof_convert_chips_tl"); 
          cfproof_convert_chips.classList.remove("cfproof_convert_chips_tr"); 
          if (data.position=="bl") {
            cfproof_convert_chips.classList.add("cfproof_convert_chips_bl"); 
          }
          else if(data.position=="br") {
            
          cfproof_convert_chips.classList.add("cfproof_convert_chips_br"); 
          }
          else if(data.position=="tl") { 
          cfproof_convert_chips.classList.add("cfproof_convert_chips_tl"); 
          }
          else if(data.position=="tr") { 
          cfproof_convert_chips.classList.add("cfproof_convert_chips_tr"); 
          }
          else {
            cfproof_convert_chips.classList.add("cfproof_convert_chips_bl"); 
          }
          if(i<=data.count){
            i=data.s;
            count_all=data.count;
            setup_count=data.setup_count;
            new_next=data.new_next;
            let timeout=setTimeout(function(){
              cfproof_convert_chips.classList.remove("cfproof_convert_chips_bl;"); 
              cfproof_convert_chips.classList.remove("cfproof_convert_chips_br"); 
              cfproof_convert_chips.classList.remove("cfproof_convert_chips_tr"); 
              cfproof_convert_chips.classList.remove("cfproof_convert_chips_tl");
              cfproof_convert_chips.classList.remove("cfproof_convert_chips_none_bl;"); 
              cfproof_convert_chips.classList.remove("cfproof_convert_chips_none_br"); 
              cfproof_convert_chips.classList.remove("cfproof_convert_chips_none_tr"); 
              cfproof_convert_chips.classList.remove("cfproof_convert_chips_none_tl");
              if (data.position=="bl") {
                cfproof_convert_chips.classList.add("cfproof_convert_chips_none_bl"); 
              }
              else if(data.position=="br") {
              cfproof_convert_chips.classList.add("cfproof_convert_chips_none_br");
              }
              else if(data.position=="tl") {
                cfproof_convert_chips.classList.add("cfproof_convert_chips_none_tl"); 
              }
              else if(data.position=="tr") {
                cfproof_convert_chips.classList.add("cfproof_convert_chips_none_tr"); 
              }
              else {
                cfproof_convert_chips.classList.add("cfproof_convert_chips_none_bl"); 
              }
              cfproof_convert_chips.innerHTML="";
            },showing_time);


            let next_timeout = setTimeout(function(){
              document.getElementById("cfproof_convert_custom_css").remove();
              document.getElementById("cfproof_convert_css_file").remove();
              cfProofConvertShowNotification(i,rotative,count_all,setup_count,new_next,url);
              },delay_time+showing_time);

            if( sessionStorage.getItem("dont_display")==1 ){
              clearTimeout(next_timeout);
              clearTimeout(timeout);
              cfproof_convert_chips.style.display='none';
            }
            
          }else{
            rotative=data.rotative;
            if(rotative=="yes"){
              i=0;
              count=1;
              setup_count=0;
              new_next=data.new_next
              setTimeout( function(){
              document.getElementById("cfproof_convert_custom_css").remove();
              document.getElementById("cfproof_convert_css_file").remove();
                cfProofConvertShowNotification(i,rotative,count_all,setup_count,new_next,url);
              },delay_time+showing_time)
            }
          }
        }else{
          rotative='yes';
          if(rotative=="yes"){
            i=0;
            count_all=data.count;
            setup_count=data.setup_count;
            new_next=0;
            setTimeout( function(){
            document.getElementById("cfproof_convert_custom_css").remove();
            document.getElementById("cfproof_convert_css_file").remove();
              cfProofConvertShowNotification(i,rotative,count_all,setup_count,new_next,url);
            },50)
          }
        }
        
      }
    }
  }
  xhttp.open("POST",ajax,true);
  xhttp.setRequestHeader("Access-Control-Allow-Origin", "*");
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.setRequestHeader('cache-control', 'no-cache, must-revalidate, post-check=0, pre-check=0');
  xhttp.setRequestHeader('cache-control', 'max-age=0');
  xhttp.setRequestHeader('expires', '0');
  xhttp.setRequestHeader('expires', 'Tue, 01 Jan 1980 1:00:00 GMT');
  xhttp.setRequestHeader('pragma', 'no-cache');
  xhttp.send("action=cfproof_convert_user_ajax&config_version="+config_version+"&url="+url+"&count="+count_all+"&next="+i+"&setup_count="+setup_count+"&new_next="+new_next);
}

let cfproof_convert_chips=document.getElementById("cfproof_convert_chips");
cfproof_convert_chips.addEventListener("click",function(event){
   let ajax=document.getElementById("cfproof_convert_user_ajax").value;
  let cfproof_convert_chips=document.getElementById("cfproof_convert_chips");
  var product_title=document.getElementById("cfproof_convert_product_title");
  var setup_id=product_title.getAttribute("data-setup-id");
  var redirect_url=product_title.getAttribute("data-redirect_url");
  let xhttp=new XMLHttpRequest();
  xhttp.onreadystatechange=function(){
    if(this.readyState==4 && this.status==200){
          console.log(this.responseText);
      var data=JSON.parse(this.responseText);
      if(event.target.classList.contains("cfproof_convert_closebtn")){
        if(data.status==1){
          if(data.dont_display==1){
            sessionStorage.setItem("dont_display", 1);
          }
          cfproof_convert_chips.style.display='none';
        }
      }
      if(event.target.getAttribute("id")=="cfproof_convert_product_title" || event.target.getAttribute("id")=="cfproof_convert_link"){
        if(data.status==1){
          if(data.dont_display==1){
            sessionStorage.setItem("dont_display", 1);
          }
          location.href=redirect_url;
        }
      }
    }
  }
  xhttp.open("POST",ajax,true);
  xhttp.setRequestHeader("Access-Control-Allow-Origin", "*");
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("action=cfproof_convert_impression&cs=a&setup_id="+setup_id);
});
