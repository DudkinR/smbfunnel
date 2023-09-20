<!DOCTYPE html>
<html lang="eng">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title>Purchase</title>
    <!-- jQuery is used only for this example; it isn't required to use Stripe -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- jQuery library -->
    <script src="<?php echo plugins_url('vendor/jquery/jquery-3.2.1.min.js',__FILE__); ?>"></script>
    <script src="<?php echo plugins_url('vendor/jquery-creditcardvalidator/jquery.creditCardValidator.js',__FILE__); ?>"></script>
    <script src="<?php echo plugins_url('vendor/assets/validation.js',__FILE__); ?>"></script>

    <!-- 2Checkout JavaScript library -->
    <script src="<?php echo plugins_url('vendor/js/2co.min.js',__FILE__); ?>"></script>
    <script>
            // A success callback of TCO token request
            var success = function (data) {
                // Set the token in the payment form
                $('#paymentForm #token').val(data.response.token.token);

               // $("#error-message").hide();
               // $("#error-message").html("");

                // Submit the form with TCO token
                $('#paymentForm').submit();
            };

            // A Error callback of TCO token request.
            var error = function (data) {
                var errorMsg = "";
                if (data.errorCode === 200) {
                    tokenRequest();
                } else {
                    errorMsg = data.errorMsg;
                    $("#error-message").show();
                    $("#error-message").html(errorMsg);
                    $("#submit-btn").show();
                    $("#loader").hide();
                }
            };

            function tokenRequest() {
                	var valid = validate();
                if (valid == true) {
                    $("#submit-btn").hide();
                    $("#loader").css("display", "inline-block");
                    var args = {
                        sellerId: $('#seller_id').val(),
                        publishableKey: $('#publishable_key').val(),
                        ccNo: $("#cardNumber").val(),
                        cvv: $("#cvv").val(),
                        expMonth: $("#expiryMonth").val(),
                        expYear: $("#expiryYear").val()
                    };

                    // Request 2Checkout token
                    TCO.requestToken(success, error, args);
                }
            }

            $(function () {
            	   TCO.loadPubKey('sandbox');

                $("#submit-btn").on('click', function (e) {
                   tokenRequest();
                   return false;
                });
            });
        </script>















</head>
<body>
    <div class="container-fluid">
    <div class="row">	
    <div class="col-sm-4 offset-sm-4" style="margin-top:50px;">
    <div class="card exclude-pnl">
    <div class="card-header" style="background:linear-gradient(#19334d,#19334d);">Card Payment</div>
    <div class="card-body">
    <div class="paymentErrors alert alert-danger" style="display:none;"></div>

    <div class="card card-default" style="margin-bottom:10px;">
        <div class="card-header bg-default" style="font-size:15px;color:rgb(0,0,0)">
        Total <strong><?php echo number_format(round($total),2)." (".$currency.")" ?></strong> going to be paid,  <a data-toggle="collapse" href="#collapse1" style="color:#004080;"><u>View Detail</u></a>
        </div>
        <div id="collapse1" class="panel-collapse collapse">
        <div class="card-body"><?php echo $allproductdetail; ?></div>
        <div class="card-footer">Total: <?php echo number_format(round($total),2)." (".$currency.")" ?></div>
        </div>
    </div>

    <form action="index.php?page=do_payment&execute=1" method="POST" id="paymentForm" onsubmit="return false">
    <div class="form-group">
    <label for="name">Name</label>
    <input type="text" name="cfpay_2chk_name" class="form-control" placeholder="Your Name">
    </div>
    <div class="form-group">
    <label for="email">Email</label>
    <input type="email" name="cfpay_2chk_email" class="form-control" placeholder="Your Email Id">
    </div>
    <div class="form-group">
    <label>Card Number</label>
    <input type="text" name="cfpay_2chk_card_num" size="20" autocomplete="off" id="card_num" class="form-control" placeholder="Card Number"/>
    </div>
    <div class="form-group">
    <div class="row">
    <div class="col">
    <label>CVV</label>
    <input type="text" name="cfpay_2chk_cvv" size="4" autocomplete="off" id="cvv" class="form-control"  placeholder="CVV"/>
    </div>
    <div class="col">
    <label>Expiration (MM/YYYY)</label>
    <div class="row">
    <div class="col">
    <input type="text" name="cfpay_2chk_exp_month" placeholder="MM" size="2" id="exp_month" class="form-control" />
    </div>
    <div class="col">
    <input type="text" name="cfpay_2chk_exp_year" placeholder="YYYY" size="4" id="exp_year" class="form-control" />
    </div>
    </div>
    </div>

    </div>
    </div>
    <br>
    <!-- hidden token input -->
    <input id="token" name="cfpay_2chk_token" type="hidden" value="">
    <div class="form-group">
    <input type="submit" id="makePayment" name="two_checkout_pay" class="btn form-control theme-button" value="Make Payment">
    </div>
    </form>
    </div>
    </div>
    </div>
    </div>
    </div>
</body>
<style>
    .panel
    {
        -webkit-box-shadow: 2px 4px 9px -2px rgba(0,0,0,0.75);
        -moz-box-shadow: 2px 4px 9px -2px rgba(0,0,0,0.75);
        box-shadow: 2px 4px 9px -2px rgba(0,0,0,0.75);
    }
</style>	
</html>