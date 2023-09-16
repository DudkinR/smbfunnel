"use strict";
cfProRevFroElement();
// get one element
function __cfPRev_EL(ele,par=false){
    if( par ){
        return par.querySelector(`${ele}`);
    }else{
        return document.querySelector(`${ele}`);
    }
}
// get multiple element 
function __cfPRev_mEl(ele,par=false){
    if(par){
        return par.querySelectorAll(`${ele}`);
    }else{
        return document.querySelectorAll(`${ele}`);
    }
}

function cfPreRevCapitilize( str = "" )
{
    const arr = str.split(" ");
    //loop through each element of the array and capitalize the first letter.
    for (var i = 0; i < arr.length; i++) {
        arr[i] = arr[i].charAt(0).toUpperCase() + arr[i].slice(1);
    }
    const str2 = arr.join(" ");
    return str2;
}
// When the user clicks the button, open the cfpro-rev-modal-login 
var cfpro_rev_modal_login_para = document.createElement("DIV");
document.addEventListener("click", function(eve){
    if( eve.target.classList.contains("cfpro-rev-modal-login-myBtn") )
    {
        let dataId=eve.target.getAttribute("data-openId");
        var cfpro_rev_modal_login =  __cfPRev_EL("#mycfpro-rev-modal-login-"+dataId);
        cfpro_rev_modal_login_para.classList.add("modal-backdrop", "fade", "show");
        document.getElementsByTagName("body")[0].appendChild(cfpro_rev_modal_login_para);
        cfpro_rev_modal_login.style.display = "block";
        cfpro_rev_modal_login.classList.add('show');
    }else if( eve.target.classList.contains("cfpro-rev-modal-login-myBtn") )
    {
        let dataId=eve.target.getAttribute("data-openId");
        var cfpro_rev_modal_login =  __cfPRev_EL("#mycfpro-rev-modal-login-"+dataId);
        cfpro_rev_modal_login_para.classList.add("modal-backdrop", "fade", "show");
        document.getElementsByTagName("body")[0].appendChild(cfpro_rev_modal_login_para);
        cfpro_rev_modal_login.style.display = "block";
        cfpro_rev_modal_login.classList.add('show');
    }
    else if( eve.target.classList.contains("cfpro-rev-edit-reivew") )
    {
        let revstatus = __cfPRev_EL("#cfpro-members-login").value;
        if( revstatus=='true' )
        {
            let dataId=eve.target.getAttribute("data-openId");
            var cfpro_rev_modal_login =  __cfPRev_EL("#cfpro-rev-modal-edit-"+dataId);
            cfpro_rev_modal_login_para.classList.add("modal-backdrop", "fade", "show");
            document.getElementsByTagName("body")[0].appendChild(cfpro_rev_modal_login_para);
            cfpro_rev_modal_login.style.display = "block";
            cfpro_rev_modal_login.classList.add('show');

        }else{
            let rurl = eve.target.getAttribute("data-url");
            window.location=rurl;
        }

    }
    // When the user clicks on <span> (x), cfpro-rev-modal-login-close the cfpro-rev-modal-login
    else if( eve.target.classList.contains("cfpro-rev-modal-login-close") )
    {
        let dataId=eve.target.getAttribute("data-openId");
        var md =  __cfPRev_EL("#mycfpro-rev-modal-login-"+dataId);
        var md1 =  __cfPRev_EL("#cfpro-rev-media-container-"+dataId);
        cfpro_rev_modal_login_para.remove();
        md1.style.display = "none";
        md.style.display = "none";
        md1.classList.remove('show');
        md.classList.remove('show');
    }
    // When the user clicks on <span> (x), cfpro-rev-modal-login-close the cfpro-rev-modal-login
    else if( eve.target.classList.contains("cfpro-rev-modal-review-close") )
    {
        let dataId=eve.target.getAttribute("data-openId");
        var md2 =  __cfPRev_EL("#cfpro-rev-modal-edit-"+dataId);
        cfpro_rev_modal_login_para.remove();
        md2.style.display = "none";
        md2.classList.remove('show');
    }
    // If user not logged in then send him to login page
    else if( eve.target.classList.contains("cfpro-rev-send-to-login") )
    {
        let loginURL=eve.target.getAttribute("data-url");
        window.location=loginURL;
    }
    // Delete review
     else if( eve.target.classList.contains("cfpre-rev-delete-rbtn") )
     {

        let reid = eve.target.getAttribute("data-id");
        cfProRevRemoveReview(reid);

     }
    //Add like dislike
    else if( eve.target.classList.contains("cfpro-rev-like") )
    {
        let targ = eve.target;
        let par;
        if( targ.tagName == "SPAN" )
        {
            par  = targ.closest(".cfpro-rev-l-la");
        }
        else if( targ.tagName == "I" )
        {
            par  = targ.closest(".cfpro-rev-l-la");
        }
        else if( targ.tagName == "A" )
        {
            par  = targ;
        }
        let pid = par.getAttribute("data-pid");
        let rid = par.getAttribute("data-rid");
        let status = par.getAttribute("data-status");
        if( status == false || status == "false" )
        {
            let hurl = par.getAttribute("data-url");
            window.location=`${hurl}`;
        }else{
            cfProRevAddLikeDislike( par, pid,'like',rid );
        }
    }
    //Add like dislike
    else if( eve.target.classList.contains("cfpro-rev-dlike")  )
    {
        let targ = eve.target;
        let par;
        if( targ.tagName == "SPAN" )
        {
            par  = targ.closest(".cfpro-rev-d-da");
        }
        else if( targ.tagName == "I" )
        {
            par  = targ.closest(".cfpro-rev-d-da");
        }
        else if( targ.tagName == "A" )
        {
            par  = targ;
        }
        let pid = par.getAttribute("data-pid");
        let rid = par.getAttribute("data-rid");
        let status = par.getAttribute("data-status");
        if (status==false || status=="false" )
        {
            let hurl = par.getAttribute("data-url");
            window.location=`${hurl}`;
        }else{

            cfProRevAddLikeDislike( par, pid,'dislike', rid );
        }
    }
    // Open Media box
    else if( eve.target.classList.contains("cfPrRevMediaoOModal") )
    {
        let dataId = eve.target.getAttribute("data-openId");
        let unid   = eve.target.getAttribute("data-unid");
        let curid  = eve.target.getAttribute("data-medid");
        var modal  = __cfPRev_EL( "#cfpro-rev-media-container-"+dataId );
        var media  = __cfPRev_EL( "#cfprorev-media-container-"+unid ).value;
        let medias = $.parseJSON( media );
        let img    = ['jpg', 'jpeg' , 'png', 'gif', 'svg'];
        let aud    = ['mp3', 'wma', 'aac', 'wav', 'flac','ogv'];
        let vid    = ['flv', 'mp4', 'm3u8', 'ts', '3gp', 'mov', 'avi', 'wmv'];
        let ht     = ``;
        let len    = medias.length;
        
        if( len > 0 )
        {
            var j=0;
            for ( var i=0; i < len; i++ )
            {
                j++;
                if( img.includes(medias[i].ext.toLowerCase() ) )
                {
                    ht +=`<div class="cfpro-rev-mySlides" style="background-image:url('${medias[i].name}')">
                        </div>`;
                    }
                else if( aud.includes(medias[i].ext.toLowerCase()) )
                {
                    ht +=`<div class="cfpro-rev-mySlides">
                        <audio  src="${medias[i].name}" type="audio/${medias[i].ext}" width="100%" height="auto" controls class="img-thumbnail img-fluid">
                        </audio></div>`;
                }
                else if( vid.includes(medias[i].ext.toLowerCase()) )
                {
                    ht +=`<div class="cfpro-rev-mySlides">
                        <video src="${medias[i].name}" type="video/${medias[i].ext}" class="cfpro-rev-icursor" width="100%" height="auto" controls class="img-thumbnail img-fluid">
                        </video>
                    </div>`;
                }
            }
        }else{
            ht+=`<div class="col-md-12">No file(s) available</div>`;
        }
        __cfPRev_EL(".cfpro-rev-image-box").innerHTML = ht;
        cfpro_rev_modal_login_para.classList.add( "modal-backdrop", "fade", "show" );
        document.getElementsByTagName("body")[0].appendChild( cfpro_rev_modal_login_para );
        cfProRevcurrentSlide(parseInt(curid));
        modal.style.display = "block";
        modal.classList.add('show');
    }
    //pagination
    else if( eve.target.classList.contains("cfpro-rev-pagination-link") )
    {
        let currEl = eve.target;
        let page = currEl.getAttribute("data-page");
        const url = new URL(window.location);
        url.searchParams.delete('cfpro_review_page');
        url.searchParams.set('cfpro_review_page',  page );
        window.history.pushState({}, '', url);
        window.location=location.href;
    }
    //read more
    if( eve.target.classList.contains('cfpro-rev-read-more-review-f') )
    {
        eve.preventDefault();
        let targ     = eve.target;
        let readmore = targ.getAttribute("data-readmore");
        let readless = targ.getAttribute("data-readless");
        let nreadmore     = readmore.trim().toLowerCase();
        let nreadless     = readless.trim().toLowerCase();
        let parEl    = targ.closest(".cfpro-rev-comment-text-f");
        let hideEl   = parEl.querySelector(".cfpro-rev-comment-dot-f");
        let showEl   = parEl.querySelector(".cfpro-rev-comment-second-text-f");
        let showText = showEl.innerHTML;

        let text     = targ.innerText.toLowerCase();
        if( text == nreadmore)
        {
            hideEl.innerHTML = showText;
            targ.innerText   = readless;
        }
        else if( text == nreadless )
        {
            hideEl.innerText = "...";
            targ.innerText   = readmore;
        } 
    }
});
// When the user clicks anywhere outside of the cfpro-rev-modal-login, cfpro-rev-modal-login-close it
window.onclick = function(event) {
    var cfprorevm =  __cfPRev_mEl(".cfpro-rev-rating-container");
    let len = cfprorevm.length;
    for( var i=0; i<len; i++ )
    {
        if (event.target == cfprorevm[i]) {
            cfpro_rev_modal_login_para.remove();
            cfprorevm[i].style.display = "none";
            cfprorevm[i].classList.remove('show');
        }
    }
}
window.addEventListener("load", function(eve){

    const urll = new URL(window.location);
    let cururl = urll.searchParams.get('cfpro_review_page');
    let cururl1 = urll.searchParams.get('cfpro_rev_verify_email');
    let cururl2 = urll.searchParams.get('cfprorev_open_revmodal');
    let cururl3 = urll.searchParams.get('cfprorev_open_revlike');

    // add focus on load
    if( cururl || cururl1 )
    {
        let rhead = __cfPRev_EL(".cfpro-rev-rating-head-f");
        if(rhead)
        {
          rhead.focus();
        }
    }
    // open login container after login
    if( cururl2 )
    {
        var cfpro_rev_modal_login =  __cfPRev_EL(".cfpro-rev-rating-container ");
        cfpro_rev_modal_login_para.classList.add("modal-backdrop", "fade", "show");
        document.getElementsByTagName("body")[0].appendChild(cfpro_rev_modal_login_para);
        cfpro_rev_modal_login.style.display = "block";
        cfpro_rev_modal_login.classList.add('show');
    }

    // add focus if some try to like without login
    // after login add focus
    if( cururl3 )
    {
        let rid = urll.searchParams.get('rid');
        let pid = urll.searchParams.get('pid');
        let par = __cfPRev_EL(`[data-rid=${rid}]`);
        if( urll.searchParams.has('dislike') )
        {
            cfProRevAddLikeDislike( par, pid,'dislike',rid );
            urll.searchParams.delete("dislike");
            urll.searchParams.delete("pid");
            urll.searchParams.delete("rid");
            window.history.pushState({}, '', urll);
        }
        if( urll.searchParams.has('like') )
        {
            cfProRevAddLikeDislike( par, pid,'like',rid );
            urll.searchParams.delete("like");
            urll.searchParams.delete("pid");
            urll.searchParams.delete("rid");
            window.history.pushState({}, '', urll);
        }
        let rhead = __cfPRev_EL(".cfpro-rev-rating-head-f");
        if(rhead)
        {
          rhead.focus();
        }
    }
    let rev = this.sessionStorage.getItem("reviewadded");
    if( rev )
    {
        let rhead = __cfPRev_EL(".cfpro-rev-rating-head-f");
        if(rhead)
        {
          rhead.focus();
        }
        this.sessionStorage.removeItem('reviewadded');
    }
    let nameurl = __cfPRev_mEl("input[name='review_url']");
    for ( var i = 0; i < nameurl.length; i++) {
        nameurl[i].value=urll;
    }
});
 // check uploaded file extension and size
 function cfProRevFroElement()
 {
     let el1 =__cfPRev_EL('.cfpro-rev-multiple-file');
     let el2 =__cfPRev_EL('.cfpro-review-form');
     if(el1)
     {
         el1.addEventListener('change', function(eve){
             var fi = this;
             var pare = this.closest('.cfpro-rev-multiple-file-parent');
             var pareEle = pare.closest('.cfpro-rev-rating-container');
             let nextEl =  pare.nextElementSibling;
             let neil = this.nextElementSibling.value;
             let filesize = this.previousElementSibling.value;
             let maxfile = this.previousElementSibling.previousElementSibling.value;
             let subbtn = __cfPRev_EL(".cfpre-rev-submit-rbtn",pareEle);
             let tex = document.createElement("UL");
             // VALIDATE OR CHECK IF ANY FILE IS SELECTED.
             let success=``;
             if (fi.files.length > 0) {
                 if( fi.files.length > maxfile )
                 {
                     success+=`<li class="text-danger">Sorry! You can upload only ${maxfile}.</li>`;
                     subbtn.setAttribute("disabled",true);
                     fi.value="";
                 }else{
                    let checke=true;
                     for (var i = 0; i <= fi.files.length - 1; i++) {
                         var fname = fi.files.item(i).name;      // THE NAME OF THE FILE.
                         var fsize = fi.files.item(i).size;      // THE SIZE OF THE FILE.
                         var ftype = fi.files.item(i).type;      // THE type OF THE FILE.
                         var filesize_kb = filesize*1048576;//1MB = 1048576 Bytes
                         if( neil.includes(ftype) )
                         {
                             success+=`<li class="text-danger">Only ${neil} allowed</li>`;
                             subbtn.setAttribute("disabled",true);
                             checke=false;
                             fi.value="";
                         }
                         else if( fsize > filesize_kb)
                         {
                             success+=`<li class="text-danger">File size should be less then ${filesize}MB</li>`;
                             subbtn.setAttribute("disabled",true);
                             checke=false;
                             fi.value="";
                         }
                     }
                     if(checke)
                     {
                        subbtn.removeAttribute("disabled");
                         success+=`<li class="text-success">${fi.files.length} File(s)</li>`;
                     }
                 } 
                 tex.innerHTML=success;
                 nextEl.innerHTML="";
                 nextEl.appendChild(tex);
             }
         });
     }
     if(el2)
     {
         el2.addEventListener('submit', function(eve){
            eve.preventDefault();
            let frm=this;
            let sucCon = __cfPRev_EL(".cfpro-rev-multiple-file-con", frm );
            let reBtn  = __cfPRev_EL(".cfpre-rev-submit-rbtn", frm );
            let textsum  = __cfPRev_EL(".cfpro-rev-summary-box", frm );
            let model  =  frm.closest(".cfpro-rev-rating-container");
            var xhttp  = new XMLHttpRequest();
            let sub_text   = reBtn.innerText;
            let formdata = new FormData(this); 
            reBtn.innerHTML=`<span class="cfpro-rev-loader"></span> Adding...`;
            xhttp.onreadystatechange = function() {
                if ( this.readyState == 4 && this.status == 200 ) {
                    if( this.responseText !="" )
                    {
                        try
                        {
                        const urll = new URL(window.location);
                        urll.searchParams.delete('cfpro_review_page');
                        urll.searchParams.delete('cfpro_rev_verify_email');
                        urll.searchParams.delete('cfprorev_open_revmodal');
                        window.history.pushState({}, '', urll);
                        // frm.reset();
                        cfpro_rev_modal_login_para.remove();
                        model.style.display = "none";
                        model.classList.remove('show');
                        if(sucCon)
                        {
                            sucCon.innerHTML="";
                        }
                        reBtn.innerHTML=sub_text;
                        let res = JSON.parse( this.responseText );
                        sessionStorage.setItem("reviewadded", "add");
                        if( res.status == 1 && res.action =='add' )
                        { 
                            if( res.email != "no" )
                            {
                                alert(res.email);
                            }
                            window.location=window.location.href;
                            
                        }else if( res.status == 1 && res.action =='update' ){
                            window.location=window.location.href;
                        }
                        else if( res.status == 0 )
                        { 
                             __cfPRev_EL("#cfpro-rev-media-progress", frm ).style.visibility='hidden';
                            frm.reset();
                            alert(res.message);
                        }
                        }catch(err){
                           
                            alert(err);
                        }
                    }else{
                        alert('Sorry! There is a error. Please refresh the page');   
                    }
                }
            }
              // (C) UPLOAD PROGRESS
            var percent = 0, width = 0;
            var bar =  __cfPRev_EL("#cfpro-rev-media-bar", frm );
            var progress =  __cfPRev_EL("#cfpro-rev-media-progress", frm );
            if(bar)
            {
                xhttp.upload.onloadstart = function(evt){
                    bar.style.width = "0";
                    progress.style.visibility="visible";
                };
                
                xhttp.upload.onprogress = function(evt){
                    percent = evt.loaded / evt.total;
                    width = Math.ceil(percent * 100);
                    bar.style.width = width + "%";
                    progress.style.visibility="visible";
                };
    
                xhttp.upload.onloadend = function(evt){
                    bar.style.width = "100%";
                    progress.style.visibility="visible";
                };
    
                // (D) ON UPLOAD COMPLETE
                xhttp.onload = function(){
                };
            }
            xhttp.open( "POST", __cfPRev_EL("#cfpro-rev-modal-ajax").value, true );
            xhttp.send(formdata);
         });
     }

 }


// add like dislike in fast way
function cfProRevAddLikeDislike(targ, pid,type, rid )
{
    let parEl = targ.closest(".cfpro-rev-ld-c");
    let addornot = targ.getAttribute("data-status");
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {

        if ( this.readyState == 4 && this.status == 200 ) {
            if( this.responseText !="" )
            {
                try{
                    let res = JSON.parse( this.responseText );
                    if( res.status == 1 )
                    {
                        cfProRevReplaceLikeDislike(parEl,type);
                    }
                    else if( res.status == 0 )
                    { 
                        alert( res.message );
                    }
                }
                catch( err ){
                }
            }else{
                console.info("You are the Admin. Please logout first.");
            }
        }
    };
    let postData = "action=cfproreviews_like&pid="+pid+"&status="+addornot+"&rid="+rid+"&type="+type;
    xhttp.open( "POST", __cfPRev_EL("#cfpro-rev-modal-ajax").value, true );
    xhttp.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
    xhttp.send( postData );
}
function cfProRevRemoveReview( rid )
{
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {

        if ( this.readyState == 4 && this.status == 200 ) {
            console.log( this.responseText );
            if( this.responseText !="" )
            {
                try{
                    let res = JSON.parse( this.responseText );
                    if( res.status == 1 )
                    {
                        __cfPRev_EL(`#cfpro-rev-r-container-${rid}`).remove();
                        cfpro_rev_modal_login_para.remove();
                    }
                    else if( res.status == 0 )
                    { 
                        alert( res.message );
                    }
                }
                catch( err ){
                }
            }else{
                console.info("You are the Admin. Please logout first.");
            }
        }
    };
    let postData = "action=cfproreviews_delete&rid="+rid;;
    xhttp.open( "POST", __cfPRev_EL("#cfpro-rev-modal-ajax").value, true );
    xhttp.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
    xhttp.send( postData );
}
// add like dislike in fast way
function cfProRevReplaceLikeDislike(parEl,type )
{
    let lcEl  = __cfPRev_EL(".cfpro-rev-l-cs",parEl);
    let lcic  = __cfPRev_EL(".cfpro-rev-l-li",parEl);
    let dcEl  = __cfPRev_EL(".cfpro-rev-d-cs",parEl);
    let dcic  = __cfPRev_EL(".cfpro-rev-d-di",parEl);
    let tlke  = parseInt( lcEl.innerText );
    let tdlke = parseInt( dcEl.innerText );
    let total_l = ( isNaN( tlke ) ) ? 0 : tlke;
    let total_dl = ( isNaN( tdlke ) ) ? 0 : tdlke;
    if( type == 'like' )
    {
        if( lcic.classList.contains('text-primary') ){
            total_l  = total_l-1;
            lcic.classList.remove('text-primary');
        }
        else if( dcic.classList.contains('text-primary') ){
            total_l  = total_l+1;
            total_dl  = total_dl-1;
            dcic.classList.remove('text-primary');
            lcic.classList.add('text-primary');
        }
        else{
            total_l  = total_l+1;
            lcic.classList.add('text-primary')
            lcEl.innerText=total_l;
        }
    }else if( type == 'dislike' )
    {
        if( dcic.classList.contains('text-primary') ){
            total_dl  = total_dl-1;
            dcic.classList.remove('text-primary');
        }
        else if( lcic.classList.contains('text-primary') ){
            total_l   = total_l-1;
            total_dl  = total_dl+1;
            lcic.classList.remove('text-primary');
            dcic.classList.add('text-primary');
        }
        else{
            total_dl  = total_dl+1;
            dcic.classList.add('text-primary');
        }
    }
    dcEl.innerText = ( total_dl == 0 ) ? "" : total_dl;
    lcEl.innerText = ( total_l == 0  )  ? "" : total_l;
}
// check email validation
function cfProRevcheckEmail(email) {
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return filter.test(email);
}
var cfProRevslideIndex = 1;
function cfProRevplusSlides( n,s ) {
  cfProRevShowSlides(cfProRevslideIndex += n, n);
}
function cfProRevcurrentSlide( n ) {
  cfProRevShowSlides(cfProRevslideIndex = n,false);
}
function cfProRevShowSlides( n,s=false ) {
    var i;
    var slides = document.getElementsByClassName("cfpro-rev-mySlides");
    var dots = document.getElementsByClassName("cfpro-rev-idemo");
    var column = document.getElementsByClassName("cfpro-rev-icolumn");
    let len = slides.length;
    if ( n > len ) { cfProRevslideIndex = 1 }
    if (n < 1) { cfProRevslideIndex = len }
    for ( i = 0; i < len; i++ ) {
        slides[i].style.display = "none";
    }
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
        if( n == 0 || n < 1 ){
            column[i].style.display = "none";
        }
        else if( cfProRevslideIndex == len  ){
            column[i].style.display = "none";
        }
    }
    slides[cfProRevslideIndex-1].style.display = "block";
}