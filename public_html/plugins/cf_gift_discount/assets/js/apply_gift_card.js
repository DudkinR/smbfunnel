
$(function(){
    $(window).on("load", function(){
        const cfdiscount_plugin_url = new URL(window.location);
        let cfdiscount_1 = cfdiscount_plugin_url.searchParams.get('cfdisc_redeem_giftcard');
        let cfdiscount_2 = cfdiscount_plugin_url.searchParams.get('cfdisc_redeem_revert');
        let cfdiscount_3 = cfdiscount_plugin_url.searchParams.get('cfdisc_redeem_discount');
        let cfdiscount_4 = cfdiscount_plugin_url.searchParams.get('cfdisc_revert_discount');
        if( cfdiscount_1 || cfdiscount_4 || cfdiscount_2 || cfdiscount_3  )
        {
            $(".cfgift-gift-card-container").focus();
        }
    });
    setTimeout(function(){ $(".cfgift-error").text('').hide(); }, 3000);

    $("#cfgift-giftcard-input").on("input",function(){
        let _val = $(this).val().trim();
        if(_val.length>0){
            $("#cfgift-sub-btn-for-gift").prop("disabled",false).removeClass('cfgift-not-allowed').addClass('cfgift-btn-active');
        }else{
            $("#cfgift-sub-btn-for-gift").prop("disabled",true).addClass('cfgift-not-allowed').removeClass('cfgift-btn-active');
        }
    });

    $("#cfdis-discount-input").on("input",function(){
        let _val = $(this).val().trim();
        if(_val.length>0){
            $("#cfdis-sub-btn-discount").prop("disabled",false).removeClass('cfgift-not-allowed').addClass('cfgift-btn-active');
        }else{
            $("#cfdis-sub-btn-discount").prop("disabled",true).addClass('cfgift-not-allowed').removeClass('cfgift-btn-active');
        }
    });

    $("#cfgift-sub-btn-for-gift").on("click", function(eve){

        let _codeval, btn, response, postData,currentdate;
        _codeval =  $("#cfgift-giftcard-input").val().trim();
        btn = $(this);
        currentdate = cfDiscGetFormattedDate();
        $(btn).html(`<i class="fa fa-spinner fa-spin"></i>`);
        postData= "action=discp_aval_giftcard&param=save&gift_code="+_codeval+"&currentdate="+currentdate;
        
        $.ajax( {
            url : $("#cfgift-ajax").val(), 
            data : postData,
            type: "POST",
            success: function( resp ){
                try{
                    $(btn).text('Apply');
                    response  = $.parseJSON( resp );
                    if( response.status==1 ){
                        window.history.replaceState(null, null, ' ');
                        const cfdiscount_plugin_url = new URL(window.location);
                        let currentQuer = cfdiscount_plugin_url.searchParams.get('cfdisc_redeem_giftcard');
                        cfdiscount_plugin_url.searchParams.delete('cfdisc_redeem_revert');
                        cfdiscount_plugin_url.searchParams.delete('oto_removed');
                        if( currentQuer != null || currentQuer != "null" )
                        {   cfdiscount_plugin_url.searchParams.delete('cfdisc_redeem_giftcard' );
                            cfdiscount_plugin_url.searchParams.set('cfdisc_redeem_giftcard',  _codeval );
                        }else{
                            cfdiscount_plugin_url.searchParams.set('cfdisc_redeem_giftcard',  _codeval );
                        }
                        window.history.pushState({}, '', cfdiscount_plugin_url);
                        window.location = location.href;

                    }else if( response.status==0 ){
                        $(".cfgift-gift-error").show().text(response.message) 
                        setTimeout(() => {
                            $(".cfgift-gift-error").hide().text("");
                        }, 5000);
                    }
                    else{
                        window.location=location.href;
                    }

                }catch(err)
                {
                    window.location=location.href;
                }
            }
        });
    });
    $("#cfdis-sub-btn-discount").on("click", function(eve){

        let _codeval, btn, response, postData,currentdate;
        _codeval =  $("#cfdis-discount-input").val().trim();
        btn = $(this);
        currentdate = cfDiscGetFormattedDate();
        $(btn).html(`<i class="fa fa-spinner fa-spin"></i>`);
        postData= "action=cfdiscp_aval_discount&param=save&discount_code="+_codeval+"&currentdate="+currentdate;
        $.ajax( {
            url : $("#cfgift-ajax").val(), 
            data : postData,
            type: "POST",
            success: function( resp ){
                try{
                    $(btn).text('Apply');
                    response  = $.parseJSON( resp );
                    if( response.status==1 ){
                        window.history.replaceState(null, null, ' ');
                        const cfdiscount_plugin_url = new URL(window.location);
                        let currentQuer = cfdiscount_plugin_url.searchParams.get('cfdisc_redeem_discount');
                        cfdiscount_plugin_url.searchParams.delete('cfdisc_revert_discount');
                        cfdiscount_plugin_url.searchParams.delete('oto_removed');
                        if( currentQuer != null || currentQuer != "null" )
                        {   cfdiscount_plugin_url.searchParams.delete('cfdisc_redeem_discount' );
                            cfdiscount_plugin_url.searchParams.set('cfdisc_redeem_discount',  _codeval );
                        }else{
                            cfdiscount_plugin_url.searchParams.set('cfdisc_redeem_discount',  _codeval );
                        }
                        window.history.pushState({}, '', cfdiscount_plugin_url);
                        window.location = location.href;

                    }else if( response.status==0 ){
                        $(".cfgift-gift-error").show().text(response.message) 
                        setTimeout(() => {
                            $(".cfgift-gift-error").hide().text("");
                        }, 5000);
                    }
                    else{
                        window.location=location.href;
                    }

                }catch(err)
                {
                    window.location=location.href;
                }
            }
        });
    });
    $(document).on("click",'.cfgift-remove-gift-card', function(eve){
        window.history.replaceState(null, null, ' ');
        const cfdiscount_plugin_url = new URL(window.location);
        if(confirm('Are you sure'))
        {
            let currentQuery = cfdiscount_plugin_url.searchParams.get('cfdisc_redeem_giftcard');
            if( currentQuery != null )
            {
                cfdiscount_plugin_url.searchParams.delete('cfdisc_redeem_giftcard');
                cfdiscount_plugin_url.searchParams.delete('oto_removed');
                cfdiscount_plugin_url.searchParams.set('cfdisc_redeem_revert',  'revert' );
            }
            cfdiscount_plugin_url.searchParams.set('cfdisc_redeem_revert',  'revert' );
            window.history.pushState({}, '', cfdiscount_plugin_url);
            window.location = location.href;
        }
    });
    $(document).on("click",'.cfdisp-remove-discount', function(eve){
        window.history.replaceState(null, null, ' ');
        const cfdiscount_plugin_url = new URL(window.location);
        if(confirm('Are you sure'))
        {
            let currentQuery = cfdiscount_plugin_url.searchParams.get('cfdisc_redeem_discount');
            if( currentQuery != null )
            {
                cfdiscount_plugin_url.searchParams.delete('cfdisc_redeem_discount');
                cfdiscount_plugin_url.searchParams.delete('oto_removed');
                cfdiscount_plugin_url.searchParams.set('cfdisc_revert_discount',  'revert' );
            }
            cfdiscount_plugin_url.searchParams.set('cfdisc_revert_discount',  'revert' );
            window.history.pushState({}, '', cfdiscount_plugin_url);
            window.location = location.href;
        }
    });
});

function cfDiscGetFormattedDate()
{
    let newD  = new Date();
    let D = newD.getDate();
    let M = newD.getMonth();
    let Y  = newD.getFullYear();
    let H  = newD.getHours();
    let Mn  = newD.getMinutes();
    M=M+1;
    if(D<10)
    {
        D="0"+D;
    }
    if(M<10)
    {
        M="0"+M;
    }else{
        M=M;
    }
    if(Y<10)
    {
        Y="0"+Y;
    }

    if(H<10)
    {
        H="0"+H;
    }
    
    if(Mn<10)
    {
        Mn="0"+Mn;
    }
    return Y+"-"+M+"-"+D;
}