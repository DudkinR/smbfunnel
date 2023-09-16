$(function(){

  $(".cfseo-select-container").each(function () {
    var $this = $(this), numberOfOptions = $(this).children("option").length;
    $this.addClass("cfseo-select-hidden");
    $this.wrap('<div class="cfseo-select"></div>');
    $this.after('<div class="cfseo-select-styled"></div>');
  
    var $styledSelect = $this.next("div.cfseo-select-styled");
    var s_opt=$(".cfseo-selected").data("sel");
    if(s_opt=="selected"){
      $styledSelect.text($("#cfseo-selected").text());
    }
    else{
      $styledSelect.text($this.children("option").eq(0).text());

    }
    var $list = $("<ul />", {
      class: "cfseo-select-options"
    }).insertAfter($styledSelect);
  
    for (var i = 0; i < numberOfOptions; i++) {
      $("<li />", {
        text: $this.children("option").eq(i).text(),
        rel: $this.children("option").eq(i).val()
      }).appendTo($list);
    }
  
    var $listItems = $list.children("li");
  
    $styledSelect.click(function (e) {
      e.stopPropagation();
      $("div.cfseo-select-styled.cfseo-active")
        .not(this)
        .each(function () {
          $(this).removeClass("cfseo-active").next("ul.cfseo-select-options").hide();
        });
          $(this).toggleClass("cfseo-active").next("ul.cfseo-select-options").toggle();
        
      });
  
    $listItems.click(function (e) {
      e.stopPropagation();
      $styledSelect.text($(this).text()).removeClass("cfseo-active");
      $this.val($(this).attr("rel"));
      $list.hide();
      //console.log($this.val());
    });
  
    $(document).click(function () {
      $styledSelect.removeClass("cfseo-active");
      $list.hide();
    });
  });

  $("#cfseo-main-title").on("input",function(){
      var mainTitle=$("#cfseo-main-title").val();
      if(mainTitle.length > 60){
        $("#cfseo-main-title").css({"border": "2px solid #d62424"});
      }else{
        $("#cfseo-main-title").css({"border": "1px solid #ced4da"});
      }
  });
  $("#cfseo-main-description").on("input",function(){
      var mainTitle=$("#cfseo-main-description").val();
      if(mainTitle.length > 160){
        $("#cfseo-main-description").css({"border": "2px solid #d62424"});
      }else{
        $("#cfseo-main-description").css({"border": "1px solid #ced4da"});
      }
  });

  $("#cfseo-add-setting").on("submit",function(eve){
    eve.preventDefault();
    let btn=eve.target;
    btn.disabled=true;  
    $("#cfseo_save_setting").html('Saving.. <span class=" spinner-border spinner-border-sm"></span></button>');
    var postData= "action=cfseo_save_ajax&"+$("#cfseo-add-setting").serialize();    
    $.post( $("#cfseo_ajax").val() , postData, function( response ){
      btn.disabled=false;
      console.log(response);
      var response  = $.parseJSON(response);
      if(response.status==1){
      $("#cfseo_id").val(response.cfseo_id);
      $("#cfseo_param").val("update_cfseo");
      $("#cfseo_save_setting").html("Saved");
      setTimeout(function(){
       $("#cfseo_save_setting").html("save changes") 
      }, 1000);     
      }else if(response.status==0){
      alert("Error: There was an error! Setting not saved");
      }
    });
  });

  $("#cfseo-delete-setting").on("submit",function(eve){
    if(confirm("Are you sure.")){
        eve.preventDefault();
        var postData= "action=cfseo_delete_ajax&"+$("#cfseo-delete-setting").serialize();    
        $.post( $("#cfseo_ajax").val() , postData, function( response ){
          console.log(response);
          var response  = $.parseJSON(response);
          if(response.status==1){
          setTimeout(function(){
          location.href="index.php?page=cfseo_dashboard";
          }, 500);     
          }else if(response.status==0){
          alert("Error: There was an error! Setting not saved");
          }
        });
    }else{
        return false;
    }
  })
  $("#cfseo-add-webmaster-tools").on("submit",function(eve){
    eve.preventDefault();
    let btn=eve.target;
    btn.disabled=true;  
    $("#cfseo_save_setting").html('Saving.. <span class=" spinner-border spinner-border-sm"></span></button>');
    var postData= "action=cfseo_save_webmaster_ajax&"+$("#cfseo-add-webmaster-tools").serialize();    
    $.post( $("#cfseo_ajax").val() , postData, function( response ){
      btn.disabled=false;
      console.log(response);
      var response  = $.parseJSON(response);
      if(response.status==1){
         $("#cfseo_webmaster_param").val("update_cfseo_webmaster");
      $("#cfseo_save_setting").html("Saved");
      setTimeout(function(){
        $("#cfseo_save_setting").html("save changes")
      }, 1000);     
      }else if(response.status==0){
      alert("Error: There was an error! Setting not saved");
      }
    });
  })

  $("#cfseo-add-social-accounts").on("submit",function(eve){
    eve.preventDefault();
    let btn=eve.target;
    btn.disabled=true;  
    $("#cfseo_save_setting").html('Saving.. <span class=" spinner-border spinner-border-sm"></span></button>');
    var postData= "action=cfseo_save_social_ajax&"+$("#cfseo-add-social-accounts").serialize();    
    $.post( $("#cfseo_ajax").val() , postData, function( response ){
      btn.disabled=false;
      console.log(response);
      var response  = $.parseJSON(response);
      if(response.status==1){
      $("#cfseo_social_param").val("update_cfseo_social");
      $("#cfseo_save_setting").html("Saved");
      setTimeout(function(){
        $("#cfseo_save_setting").html("save changes")
      }, 1000);     
      }else if(response.status==0){
      alert("Error: There was an error! Setting not saved");
      }
    });
  });
})


document.addEventListener("DOMContentLoaded", function() {
  var tabs = document.querySelectorAll('.cfseo-tabbed .cfseo-tabbed-tab-links');
  var seperators = document.querySelectorAll('.cfseo-seperators');
  for (var i = 0, len = tabs.length; i < len; i++) {
    tabs[i].addEventListener("click", function(eve) {
      eve.preventDefault();
      if (this.classList.contains('cfseo-active'))
        return;

      var parent = this.parentNode;
      var currentTag=eve.target;
      innerTabs = parent.querySelectorAll('.cfseo-tabbed-tab-links');
      innerTabs2 = document.querySelectorAll(".cfseo-tabcontent");
      for (var index = 0, iLen = innerTabs.length; index < iLen; index++) {
        innerTabs[index].classList.remove('cfseo-active');
      }
      for (var index1 = 0, iLen1 = innerTabs2.length; index1 < iLen1; index1++) {
        innerTabs2[index1].classList.remove('cfseo-show');
      }

      this.classList.add('cfseo-active');
      var achr = this.getAttribute("href");
      // location.hash=achr;
      var achrId=achr.replace("#", "");
      var seoShowSetting=document.getElementById(achrId);
      if(seoShowSetting.classList.contains("cfseo-show"))
      {
        seoShowSetting.classList.remove("cfseo-show");
      }
      else{
       seoShowSetting.classList.add("cfseo-show");
      }
    });
  }

  for (var i = 0, len = seperators.length; i < len; i++) {
    seperators[i].addEventListener("click", function() {
      if (this.classList.contains('cfseo-seperators-active'))
        return;
      // console.log(this);
      var parent = this.parentNode,
          innerSwitchers = parent.querySelectorAll('.cfseo-seperators');

      for (var index = 0, iLen = innerSwitchers.length; index < iLen; index++) {
        innerSwitchers[index].classList.remove('cfseo-seperators-active');
      }

      this.classList.add('cfseo-seperators-active');
    });
  }
});
