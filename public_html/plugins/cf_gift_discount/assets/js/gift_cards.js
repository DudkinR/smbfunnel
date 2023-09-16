"use strict"
$(document).ready(function(){

    let giftdiscountajaxUrl =  $("#giftdiscountajaxUrl").val();
    if($("#savegiftcardsproduct").val()=="save")
    {
        $("#productid").val(`${randomStr(5)}_${Date.now()}_${randomStr(6)}`);
    }

    $(".cfdisp_setting-form").on("submit", function(event){
        event.preventDefault();    
        let response;
        let btn =$(this).find(".cfdisp-save-setting");
        $(btn).html(`<i class="fa fa-spinner fa-spin" ></i> ${t('Saving')}`);
        $.ajax({
            method:"post",
            url:giftdiscountajaxUrl,
            data:$(this).serialize(),
            success:function(resp){  
                response=JSON.parse(resp);
                $(btn).html(`Save changes`);
                if(response.status == 1)
                {
                    $("#cfdisp-sfsnackbar").text(response.message).addClass('show');
                    setTimeout(function(){ $("#cfdisp-sfsnackbar").text('').removeClass("show"); }, 500);

                }else{
                    $("#cfdisp-sfsnackbar").text(response.message).addClass("show text-danger");
                    setTimeout(function(){ $("#cfdisp-sfsnackbar").text('').removeClass("show text-danger");  }, 3000);
                }
            },
            error:function(err){
                console.log(err)
            }
        });
    });

    // Show the first tab by default
    $('.cfgift_tabs-stage div.cfgift_tabs_navvar').hide();
    $('.cfgift_tabs-stage > form > div#cfgift_tab-1').show();
    $('.cfgift_tabs-nav li:first').addClass('tab-active');

    // Change tab class and display content
    $('.cfgift_tabs-nav a').on('click', function(event){
        event.preventDefault();
        $('.cfgift_tabs-nav li').removeClass('cfgift_tab-active');
        $(this).parent().addClass('cfgift_tab-active');
        $('.cfgift_tabs-stage div.cfgift_tabs_navvar').hide();
        $($(this).attr('href')).show();
    });

    $(".cfdisp-expiration_type").change(function(){ 
        if( $(this).is(":checked") ){
            var val = $(this).val(); 
            if(val=="set_expiration"){
                $(".cfdisp-set_expiration_date").show().focus();
            }else{

                $(".cfdisp-set_expiration_date").hide();
            }
        }
    });

    $("#cfgemail_teplates").change(function(){ 
        if( $(this).val()=="custom" ){ 
            $("#cfgmail-custom").show().focus();
        }else{
            $("#cfgmail-custom").hide();
        }
    });

    $("#cfdemail_teplates").change(function(){ 
        if( $(this).val()=="custom" ){ 
            $("#cfdmail-custom").show().focus();
        }else{
            $("#cfdmail-custom").hide();
        }
    });
    $(".cfdisc_apply_on_product").change(function(){ 
        if( $(this).is(":checked") ){ 
            var val = $(this).val(); 
            if(val=="custom"){
                $(".cfdis_set_apply_product").show().focus();
            }else{

                $(".cfdis_set_apply_product").hide();
            }
        }
    });

    //add/update gift cards
    $("#cfdis_Giftform").on("submit",function(event){

        event.preventDefault();
        let response, giftcode, initial_value, expiration_date,apply_on_value, set_date, apply_on, success, error, frmvalid = true;
        giftcode = $("#cardcodde").val();
        initial_value = $("#initial_value").val();
        set_date = $("input[type='radio'][name='expiration_type']:checked");
        apply_on = $("input[type='radio'][name='apply_type']:checked");
        if( giftcode.length < 4 )
        {
            $(".gift-card-err").text(t('Code must be at least 4 characters long.'));
            frmvalid=false;
            return false;
        }

        if( initial_value <= 0 )
        {
            $(".initial-value-err").text(t('Initial value must be greater than 0'));
            frmvalid=false;
            return false;
        }
        //required collection type
        if( $(set_date).attr("value")=="set_expiration" ){

            expiration_date = $("#cfdisp-expiration_date").val();
            if( expiration_date.length < 5 )
            {
                $(".expiration_date_err").text(t('Please enter a valid date'));
                frmvalid=false;
                return false;
            }
        }else if( $(set_date).attr("value")=="no_expiration"  ){
            frmvalid=true
        }
         //required collection type
        if( $(apply_on).attr("value")=="custom" ){

            apply_on_value = $("#cfdisp-expiration_date").val();
            if( apply_on_value.length < 5 )
            {
                $(".cfdis_applypro_err").text(t('Please choose at least one product'));
                frmvalid=false;
                return false;
            }
        }else if( $(apply_on).attr("value")=="all"  ){
            frmvalid=true
        }
        
        if(frmvalid)
        {

            $("#save-gift-prd-btn").html(`<i class="fa fa-spinner fa-spin" ></i> ${t('Saving')}`);
            $(".cfdis-sf-error").html(``);
            $.ajax({
                method:"post",
                url: giftdiscountajaxUrl,
                data:$(this).serialize(),
                success:function(resp){  
                    response=JSON.parse(resp);
                    $("#cfdisp-success-class").hide();
                    $("#cfdisp-error-class").hide();
                    $("#save-gift-prd-btn").html(`<i class="fas fa-check-circle"></i>&nbsp;${t('Save setup')}`);
                    if(response.status == 1)
                    {
                        let pUrl = $("#giftdiscountinstall_url").val();
                        $("#giftcard_id").val(response.last_id);
                        $("#savegiftcards").val('update');
                        success=`<div class="alert alert-success alert-dismissible">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>${t('Success')}!</strong> ${response.message}. </li>
                            </div>`;
                        window.location = pUrl+`index.php?page=cfdiscount_giftcard_timeline&giftcard_id=${response.last_id}`;
                            $("#cfdisp-success-class").show().html(success).focus();
                    }else{
                        error=`<div class="alert alert-danger alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong>${t('Error')}!</strong> ${response.message}.</li>
                            </div>`;
                            $("#cfdisp-error-class").show().html(error).focus();
                    }
                },
                error:function(err){
                    console.log(err)
                }
            })
        }
    });

    //add discount
    $("#cfdis_discountform").on("submit",function(event){

        event.preventDefault();
        let response, giftcode, expiration_date,apply_on_value, set_date, apply_on, gift_type,percentage_val, success, error, frmvalid = true;
        giftcode = $("#cardcodde").val();
        set_date = $("input[type='radio'][name='expiration_type']:checked");
        apply_on = $("input[type='radio'][name='apply_type']:checked");
        gift_type= $("#cfdis_discount_type").val();
        if( giftcode.length < 4 )
        {
            $(".gift-card-err").text(t('Code must be at least 4 characters long.'));
            frmvalid=false;
            return false;
        }
        //required collection type
        if( $(set_date).attr("value")=="set_expiration" ){

            expiration_date = $("#cfdisp-expiration_date").val();
            if( expiration_date.length < 5 )
            {
                $(".expiration_date_err").text(t('Please enter a valid date'));
                frmvalid=false;
                return false;
            }
        }else if( $(set_date).attr("value")=="no_expiration"  ){
            frmvalid=true
        }
            //required collection type
        if( $(apply_on).attr("value")=="custom" ){

            apply_on_value = $("#cfdisp-expiration_date").val();
            if( apply_on_value.length < 5 )
            {
                $(".cfdis_applypro_err").text(t('Please choose atleast one product'));
                frmvalid=false;
                return false;
            }
        }else if( $(apply_on).attr("value")=="all"  ){
            frmvalid=true
        }
        percentage_val = $("#cfdis_percentage").val();
        if( percentage_val.length < 1 )
        {
            $(".cfdis_percentage_ad").text(t('Please enter discount percentage'));
            frmvalid=false;
            return false;
        }
        if( percentage_val.length > 6 )
        {
            $(".cfdis_percentage_ad").text(t('Please enter valid percetange! Ex. 10.15'));
            frmvalid=false;
            return false;
        }
        if( parseInt( percentage_val ) > 100 )
        {
            $(".cfdis_percentage_ad").text(t('Please enter valid percetange! Ex. 10.15'));
            frmvalid=false;
            return false;
        }
        if(frmvalid)
        {

            $("#save-gift-prd-btn").html(`<i class="fa fa-spinner fa-spin" ></i> ${t('Saving')}`);
            $(".cfdis-sf-error").html(``);
            $.ajax({
                method:"post",
                url: giftdiscountajaxUrl,
                data:$(this).serialize(),
                success:function(resp){  
                    response=JSON.parse(resp);
                    let pUrl = $("#giftdiscountinstall_url").val();
                    $("#cfdisp-success-class").hide();
                    $("#cfdisp-error-class").hide();
                    $("#save-gift-prd-btn").html(`<i class="fas fa-check-circle"></i>&nbsp;${t('Save setup')}`);
                    if(response.status == 1)
                    {
                        $("#giftcard_id").val(response.last_id);
                        $("#savegiftcards").val('update');
                        success=`<div class="alert alert-success alert-dismissible">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>${t('Success')}!</strong> ${response.message}. </li>
                            </div>`;
                        window.location = pUrl+`index.php?page=cfdiscount_discount_timeline&discount_id=${response.last_id}`;
                            $("#cfdisp-success-class").show().html(success).focus();
                    }else{
                        error=`<div class="alert alert-danger alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong>${t('Error')}!</strong> ${response.message}.</li>
                            </div>`;
                            $("#cfdisp-error-class").show().html(error).focus();
                    }
                },
                error:function(err){
                    console.log(err)
                }
            })
        }
    });

    //add/update gift cards
    $(".updategiftcardform").on("submit",function(event){
        event.preventDefault();
        
        let response;
        let btn =$(this).find(".save-changes");
        $(btn).html(`<i class="fa fa-spinner fa-spin" ></i> ${t('Saving')}`);
        $.ajax({
            method:"post",
            url:giftdiscountajaxUrl,
            data:$(this).serialize(),
            success:function(resp){  
                response=JSON.parse(resp);
                $(btn).html(`Save changes`);
                let pUrl = $("#giftdiscountinstall_url").val();
                if(response.status == 1)
                {
                    $("#btn-close-model2").trigger('click');
                    $("#cfdisp-sfsnackbar").text(response.message).addClass('show');
                    setTimeout(function(){ $("#cfdisp-sfsnackbar").text('').removeClass("show");                         
                    window.location = pUrl+`index.php?page=cfdiscount_giftcard_timeline&giftcard_id=${response.last_id}`; }, 500);

                }else{
                    $("#cfdisp-sfsnackbar").text(response.message).addClass("show text-danger");
                    setTimeout(function(){ $("#cfdisp-sfsnackbar").text('').removeClass("show text-danger");  }, 3000);
                }
            },
            error:function(err){
                console.log(err)
            }
        })
    });
    //add/update gift cards
    $(".updatediscountform").on("submit",function(event){
        event.preventDefault();
        
        let response;
        let btn =$(this).find(".save-changes");
        $(btn).html(`<i class="fa fa-spinner fa-spin" ></i> ${t('Saving')}`);
        $.ajax({
            method:"post",
            url:giftdiscountajaxUrl,
            data:$(this).serialize(),
            success:function(resp){  
                response=JSON.parse(resp);
                $(btn).html(`Save changes`);
                let pUrl = $("#giftdiscountinstall_url").val();
                if(response.status == 1)
                {
                    $("#btn-close-model2").trigger('click');
                    $("#cfdisp-sfsnackbar").text(t('Discount updated successfully')).addClass('show');
                    setTimeout(function(){ $("#cfdisp-sfsnackbar").text('').removeClass("show"); 
                    window.location = pUrl+`index.php?page=cfdiscount_discount_timeline&discount_id=${response.last_id}`; }, 500);

                }else{
                    $("#cfdisp-sfsnackbar").text(t('There is something wrong please refresh the page')).addClass("show text-danger");
                    setTimeout(function(){ $("#cfdisp-sfsnackbar").text('').removeClass("show text-danger");  }, 3000);
                }
            },
            error:function(err){
                console.log(err)
            }
        })
    });

    $("#initial_value").on("change", function(eve){
        let _thisval=parseFloat($(this).val());
        $(this).val(_thisval.toFixed(2));

    })
    $("#cfdis_percentage").on("change", function(eve){
        let _thisval=parseFloat($(this).val());
        $(this).val(_thisval.toFixed(2));

    })
    $("#resend_gift-code").on("submit",function(eve){
        eve.preventDefault();
        let response;
        let btn =$(this).find(".cfdisp-resend-code");
        $(btn).html(`<i class="fa fa-spinner fa-spin" ></i> ${t('sending')}`);
        $.ajax({
            method:"post",
            url: giftdiscountajaxUrl,
            data:$(this).serialize(),
            success:function(resp){
                response=JSON.parse(resp);
                $(btn).html(`resent`);
                if(response.status == 1)
                {
                    $("#btn-close-model2").trigger('click');
                    $("#cfdisp-sfsnackbar").text(response.message).addClass('show');
                    setTimeout(function(){ $("#cfdisp-sfsnackbar").text('').removeClass("show");  }, 3000);

                }else{
                    $("#cfdisp-sfsnackbar").text(response.message).addClass("show text-danger");
                    setTimeout(function(){ $("#cfdisp-sfsnackbar").text('').removeClass("show text-danger");  }, 3000);
                }
            },
            error:function(err){
                console.log(err)
            }
        })
    });
    //add a new customer
    $("#addnewcustomerform").on("submit",function(event){
        event.preventDefault();
        let reg_name,response,valid_email,valid_phoneno,name,email,phone,err,frmvalid=true;
        reg_name  = /^[a-zA-Z\s]*$/;
        valid_email = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        valid_phoneno = /^[0-9]*$/; 
        name = $("#customer_name").val();
        email = $("#customer_email").val();
        phone = $("#customer_phoneno").val();
        if( name.length <= 0 )
        {
            $(".customer-name-err").text(t('Please enter the name.'));
            frmvalid=false;
        }
        if( !reg_name.test(name) )
        {
            $(".customer-name-err").text(t('Please enter a valid name.'));
            frmvalid=false;
        }
        if (!valid_email.test(email)) {
            $(".customer-email-err").text(t('Please enter a valid email.'));
            frmvalid=false;
        }

        if(frmvalid)
        {
            $("#update-member").html(`<i class="fa fa-spinner fa-spin" ></i> ${t('Saving')}`);
            $.ajax({
                method:"post",
                url:giftdiscountajaxUrl,
                data:$(this).serialize(),
                success:function(resp){
                    response=JSON.parse(resp);
                    $("#update-member").html(t('Save changes'));
                    if(response.status == 1)
                    {
                        let pUrl = $("#giftdiscountinstall_url").val();
                        window.location = pUrl+`index.php?page=cfdiscount_giftcard_timeline&giftcard_id=${response.last_id}`;
                    }
                    else if(response.status == 0)
                    {
                        $(".cus-error").text( response.message );
                        setTimeout(function(){ $(".cus-error").text(` `) }, 3000);
                    }else{
                        window.location=location.href;
                    }
                },
                error:function(err){
                    console.log(err)
                }
            })
        }
        
    });
    
    $("#open-giftcard-form").on("click", function(){
        $("#collectionhidecard1").hide();
        $("#cfdisp-collectionformdiv").show();
        $("#giftcarddetails").focus();
    });
    
    $('[data-toggle="tooltip"]').tooltip();
    
    $(document).on("click",".change-media-ve", function(){
        letsOpenMedia(this, false);
    });
    $(document).on("click",".upload-gift-image", function(){
        let parEl = $(this).parents('.item-selection');
        letsOpenMediaForImage($(parEl), false);
    });

    $(document).on("change",".delete-image-box", function(){
        
        if( $( this ).is(":checked") ){
            $("#cfdisp-delete-produc-images").show();
        }
    });
    $(document).on("click",".images-del-btn", function(){

        if( confirm(t("Are you sure")) )
        {
            let productImg = $(".delete-image-box");
            for( let i=0; i<productImg.length;i++ )
            {
                if( $(productImg[i]).is(":checked") ){
                    $(productImg[i]).parents(".item-selection").remove();
                }
            }
            let len=$(".item-selection");
            if(len.length==1)
            {
                $(".item-selection").replaceWith(replaceMediaHTML('old'));
            }
        }
    })
    
    $(document).on("click",".varient_delete", function(){
        if( confirm(t('Selected variants are going to be deleted temporarily, you have to save the product setup to delete them permanently')) )
        {
 
            let variantPar=$(this).parents('tr').parents('tr.del-variant-id');
            let variantId=$(variantPar).data("id");
            $("#varient_container").prepend(`<input type="hidden" class="form-control"  name="denominations[delete][]"  value="${variantId}"> `);

            $(this).parents('tr').parents('tr').remove();
            
        }
    });

    $("#add_media_url_f").on("click", function(eve){
        eve.preventDefault();
        let cont,type,murl,len,itm;
        
        type=$("#media_type").val();
        murl=$("#media_url").val();
        itm=$(".item-selection");
        if( type=="image" )
        {
            cont=`<img src="${murl}" class="img-responsive"></img>`;
        }
        else if(type=="vimeo" )
        {
            cont=`<iframe src="${murl}" width="100%" frameborder="0" allow="fullscreen; picture-in-picture" allowfullscreen></iframe>`;
        }
        else if(type=="youtube" )
        {
            cont=`<iframe width="100%" src="${murl}" frameborder="0" allow="accelerometer;clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
        }
        else if(type=="video" )
        {
            cont=`<video style="width: 100%;"><source src="${murl}"></video>`;
        }
        
        let conht=`<div class="col-sm-4 item-selection mt-2 mr-2 mb-4 h-180">
        <div class="row w-200-pc">
        <div class="col-sm-12">
        <div class="form-group">
        <input type="checkbox" data-toggle="tooltip" class="delete-image-box" title="Select" value="0">
        <input type="hidden" name='media[]' data-toggle="tooltip" title="Select" value="${murl}">
        <input type="hidden" name='type[]' data-toggle="tooltip" title="Select" value="${type}">
        </div>
        </div> 
        <div class="col-sm-12 media-container">
        <div class="media img">
        ${cont}
        </div>
         </div>
         </div>`;

         let inboxImge=$(".product-media-selector").find("#add-media-box");
         $(inboxImge).before(conht);
         $(inboxImge).replaceWith(replaceMediaHTML('new'));
    });

    //add gift card product in database
    $("#add-giftcard-form").on("submit", function(eve){

        let error,resp ,currency_symbol, productId, title='', success, frmvalid=true; 
        eve.preventDefault();
        title=$("#title").val();
        currency_symbol=current_currency_symbol ($("#currency").val());
        $("#currency_symbol").val(currency_symbol);
        console.log(currency_symbol);
        productId=$("#productid").val();
        error=`<div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <ul>`;
        //required title
        if( title.trim().length <= 0 ){ 

            frmvalid=false;
            error+=`<li><strong>Error!</strong> ${t('Please enter title.')}</li>`;
        }
        //required title
        if( productId.trim().length <= 0 ){ 

            frmvalid=false;
            error+=`<li><strong>Error!</strong> ${t('Please enter product id.')}</li>`;
        }
        error+=`</ul></div>`;

        if(frmvalid)
        {
            $("#save-gift-prd-btn").html(`<i class="fa fa-spinner fa-spin" ></i> ${t('Saving')}`);
            $.ajax({
                method:'post',
                url:giftdiscountajaxUrl,
                data:$(this).serialize(),
                success:function( res )
                {
                    try{
                        $("#save-gift-prd-btn").html(`<i class="fas fa-check-circle"></i>&nbsp;${t('Save setup')}`);
                        $("#cfdisp-success-class").hide();
                        $("#cfdisp-error-class").hide();
                        resp=JSON.parse(res);
                        if(resp.status == 1)
                        {
                            let pUrl = $("#giftdiscountinstall_url").val();
                            let store_id = $("#funnelid").val();
                            setTimeout(() => {
                                window.location = pUrl+`index.php?page=cfdiscount_add_giftproduct&store_id=${store_id}&product_id=${resp.last_id}`;
                            }, 500);
                        }else{
                            error=`<div class="alert alert-danger alert-dismissible">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <strong>${t('Error')}!</strong> ${resp.message}.</li>
                                </div>`;
                                $("#cfdisp-error-class").show().html(error).focus();
                        }

                    }catch(e)
                    {
                        console.log(e.message);
                    }
                },
                error:function(res){
                    console.log(res);
                }
            });

        }else{

            $("#cfdisp-error-class").show().html(error).focus();
        }
        
    });
    $("#product-review-icon").on("click", function(){
        let url = $("#product-review-page-select :selected").attr("data-url");
        if(url != "0" || url!='' )
        {
            let id = $("#productid").val();
            url=url+`?product=${id}`;
            window.open(url,"_blank");
        }
    });

    //add decimal after digit 
    $(document).on("change",".denominations_inp", function(){
        let _thisval=parseFloat($(this).val());
        $(this).val(_thisval.toFixed(2));
    });
    // multiselect collection
    $('#collection-select').multiselect({
        columns: 1,
        placeholder: t('Select Collection'),
        search: true,
        selectAll: true
    });
     // multiselect collection
     $('#cfdis_product-select').multiselect({
        columns: 1,
        placeholder: t('Select Products'),
        search: true,
        selectAll: true
    });
    
    // Custom select
    $('#member_id').customselect();;
    $('#currency').customselect();;

    $(document).on('click', '.product-media-selector .dropdown-menu .input-group-text', function (e) {
        e.stopPropagation();
      });

    // add denomination
    $("#add-denominations-m").on("click", function(){
        let varient_id=`${Date.now()}_${randomStr(6)}`;
        let ht=`<div class="form-group denominations-box">
            <div class="row">
                <div class="col-sm-10">
                    <input type="number" name="denominations[${varient_id}]" value="${varient_id}" min="0" class="form-control denominations_inp">
                </div>
                <div class="col-sm-2">
                    <button data-toggle="tooltip" title="${t('Delete')}''" class="btn btn-outline-danger btn-delete-denominations">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>`;
        $(".add-denominations").append(ht);
    });
    // delete denominations
    $(document).on("click",".btn-delete-denominations", function(){
        $(this).parents(".denominations-box").remove();
    })

    // select currency dynamically
    $( window ).on("load", function(){
        let currency = $("#set-currency").val();
        let optn = $("#currency > option");
        let ln = optn.length;
        for(let i=0; i<ln; i++)
        {
            if( $(optn[i]).attr("value") == currency  ){
                $(optn[i]).attr("selected",true);
            }
        } 
    }); 
});
function searchGiftCards(search)
{
    var ob=new OnPageSearch(search,"#keywordsearchresult");
    ob.url=window.location.href;
    ob.search();
}

function letsOpenMedia(selector, html)
{
    try
    {
        //here calling open media
        openMedia(function(content){
            try
            {
                let extension,extarray,extarray1, extarray2,img;
                extension = content.substring(content.lastIndexOf('.') + 1);
                extarray = ['jpg', 'jpeg' , 'png', 'gif', 'svg'],
                extarray1 = ['mp3', 'wma', 'aac', 'wav', 'flac','ogv'];
                extarray2 = ['flv', 'mp4', 'm3u8', 'ts', '3gp', 'mov', 'avi', 'wmv'];
                if(extarray.includes(extension) )
                {

                    img=` <img class="img-fluid img-thumbnail" src="${content}">
                    <input name="denominations[variant_media][]" type="hidden" value="${content}" />
                    <input name="denominations[variant_mtype][]" type="hidden" value="image" />
                    `;
                }
                else if(extarray2.includes(extension) )
                {
                    img=` <video style="width: 100%;"><source src="${content}"></video>
                    <input name="denominations[variant_media][]" type="hidden" value="${content}" />
                    <input name="denominations[variant_mtype][]" type="hidden" value="video" />`;
                }
                else if(extarray1.includes(extension) )
                {
                    img=`<audio style="width: 100%;"><source src="${content}"></audio>
                    <input name="denominations[variant_media][]" type="hidden" value="${content}" />
                    <input name="denominations[variant_mtype][]" type="hidden" value="audio" />`;
                }else{
                    img=` <img class="img-fluid img-thumbnail" src="${content}">
                    <input name="denominations[variant_media][]" type="hidden" value="${content}" />
                    <input name="denominations[variant_mtype][]" type="hidden" value="image" />`;
                }


                selector.innerHTML=img;

            }catch(err){console.log(err);}
        }, html);
    }catch(err){console.log(err)}
}
function letsOpenMediaForImage(selector, html)
{
    try
    {
        //here calling open media
        openMedia(function(content){
            try
            {
                let extension,extarray,extarray1, extarray2,cont,types;
                extension = content.substring(content.lastIndexOf('.') + 1);
                extarray = ['jpg', 'jpeg' , 'png', 'gif', 'svg'],
                extarray1 = ['mp3', 'wma', 'aac', 'wav', 'flac','ogv'];
                extarray2 = ['flv', 'mp4', 'm3u8', 'ts', '3gp', 'mov', 'avi', 'wmv'];
                if(extarray.includes(extension) )
                {

                    cont=`<img src="${content}" class="img-responsive"></img>`;
                    types="image";
                }
                else if(extarray2.includes(extension) )
                {
                    cont=`<video style="width: 100%;"><source src="${content}"></video>`;
                    types="video";
                }
                else if(extarray1.includes(extension) )
                {
                    cont=`<audio style="width: 100%;"><source src="${content}"></audio>`;
                    types="audio";
                }else{
                    cont=``;
                    types="image";
                }
                let par = selector.closest(".row");
                $(selector).replaceWith( replaceMediaHTML('new') );
                let newHt=`<div class="col-sm-4 item-selection mt-2 mr-2 mb-4 h-180">
                <div class="row w-200-pc">
                <div class="col-sm-12">
                <div class="form-group">
                <input type="checkbox" data-toggle="tooltip" class="delete-image-box" title="Select" value="0">
                <input type="hidden" name='media[]' data-toggle="tooltip" title="Select" value="${content}">
                <input type="hidden" name='type[]' data-toggle="tooltip" title="Select" value="${types}">
                </div>
                </div> 
                <div class="col-sm-12 media-container">
                <div class="media img">
                ${cont}
                </div>
                 <!----> <!----> <!----> <!---->
                 </div>
                 </div>`;
                 $(par).addClass('inline-overflow').prepend(newHt);
                 $(selector).insertBefore(newHt);


            }catch(err){console.log(err);}
        }, html);
    }catch(err){console.log(err)}
}

function replaceMediaHTML(type='new'){

    let ht=``;
    if(type=='new')
    {
        ht=`<div class="col-sm-4 item-selection flex-center-column cp mt-2 mb-4" id="add-media-box">
        <button type="button" class="uploader upload-gift-image"><i class="fas fa-angle-up"></i>
        </button> <button type="button" class="btn btn-outline-secondary upload-gift-image">${t('Add Media')}</button></div>`;
    }else{
        ht=`<div class="col-sm-12 item-selection h-220 flex-center-column cp" id="add-media-box"><button
                type="button" class="uploader upload-gift-image"><i class="fas fa-angle-up"></i></button>
            <h5 class="text-gray p5">${t('No items are available!')}</h5> <button type="button"
                class="btn btn-outline-secondary upload-gift-image">${t('Add Media')}</button>
        </div>`;
    }
    return ht;
}
function randomStr(length= 5) 
{
    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * 
 charactersLength));
   }
   return result;
} 
function current_currency_symbol (gift_currency) {
    let currency;
    try {
      currency = gift_currency.toUpperCase();
      let c = gift_currency.toUpperCase();
      if (
        cf_currencies !== undefined &&
        cf_currencies[c] !== undefined &&
        cf_currencies[c].symbol_native !== undefined
      ) {
        currency = cf_currencies[c].symbol_native;
      }
    } catch (err) {
      console.log(err);
    }
    return currency;
  }