<?php
$file=plugin_dir_path(__FILE__);
$file .="stripe/Stripe_payment.php";

require_once($file);
$ob=new \CFPay_Stripe_payment();

$data= $this->loadSetting( $id );


if( is_member_loggedin( $this->funnel_id ) )
{
    $members =  get_current_member( $this->funnel_id);

    if( $members )
    {

        $email = $members['email'];
        $mid    = $members['id'];
        $sales  =  $ob->get_sales($email,$this->funnel_id);
        if($sales)
        {
            if( isset( $_GET['cfstripe_get_session'] ) )
            {    
                $rsession_id=$sales[$_GET['cfstripe_get_session']]['payment_id'];
                $ob->customer_portal($rsession_id,$id,$this->method);
    
            }else{
                if( $data )
                {

                    
                    $set = $data->card_html;
                }else{
                    $set='<div class="col-md-3">
                            <div class="card"  style="background-color:#ffffff">
                                <div class="card-header"  style="text-align: center; background-color: #007BFF; padding: 10px 12px 10px 12px">
                                    <div class=" py-1" style=" color: #ffffff; font-size: 20px; font-weight:600;">{product_title}</div>
                                </div>
                                <div class="card-body" >
                                    <div class="py-1"   style="color: #000000; font-size: 16px;font-weight: 600;">Status: <span  style="color: #28a745" class="text-success"> <i class="fas fa-check-circle "></i> {status}</span> </div>
                                    <div class="py-1"   style="color: #000000; font-size: 16px;font-weight: 600;">Expire In: <span  style="color: #007bff"> {days}</span> day(s)  </div>
                                    <div class="pt-3">
                                       <form action="" method="get">
                                       <input type="hidden" name="cfstripe_get_session" value="{sales_id}">
                                       <button type="submit" class="btn btn-primary" style="color:#ffffff;font-size:16px;
                                       background-color:#007bff;font-weight:500;border-color:#007bff
                                       border-width:1px;border-style:solid;margin:1px 1px 1px 1px;padding:7px 10px 10px 10px
                                       ">Manage Billing &nbsp;<i class="fas fa-file-invoice-dollar"></i></button>
                                       </form>
                                    </div>
                                </div>
                            </div>
                        </div>';
                }
                echo '<div class="row">';
                foreach($sales as $key => $sale)
                {
                    $title = $ob->get_product_title($sale['productid'],'title');
                    $diff   = (((int) ( $sale['expires_on'] ) )-(time()));
                    $diff   = floor( $diff/( 24*60*60 ) );
                ?>   
                <?php
                   $card_html = str_ireplace( "{product_title}", $title, $set );
                   $card_html = str_ireplace( "{form_start}", '<form action="" method="get">', $card_html );
                   $card_html = str_ireplace( "{form_end}", '</form>', $card_html );
                   $card_html = str_ireplace( "{sales_id}", $key, $card_html );
                   $card_html = str_ireplace( "{status}", ucfirst( $sale['status'] ), $card_html );
                   $card_html = str_ireplace( "{days}", $diff, $card_html);
                   echo stripslashes($card_html);
                }
                echo '</div>';
            }
        }
    }
}

