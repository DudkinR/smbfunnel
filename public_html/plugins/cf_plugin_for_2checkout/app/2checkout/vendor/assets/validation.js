function validate() {
	var valid = true;
	$(".demoInputBox").css('background-color', '');
	var message = "";

	var cardHolderNameRegex = /^[a-z ,.'-]+$/i;
	var cvvRegex = /^[0-9]{3,3}$/;

	var cardHolderName = $("#cardHolderName").val();
	var cardHolderEmail = $("#cardHolderEmail").val();
	var cardNumber = $("#cardNumber").val();
	var cvv = $("#cvv").val();
	
	$(".required").each(function() {
		if($(this).val()=='') {
			$(this).css('background-color', '#FFFFDF');
			valid = false;
		}
	});
	
	if(!valid) {
		message += "<div> Highlighted fields are required.</div>";
	}
	
	if (cardHolderName != "" && !cardHolderNameRegex.test(cardHolderName)) {
		message += "<div>Card Holder Name is invalid</div>";
		$("#cardHolderName").css('background-color', '#FFFFDF');
		valid = false;
	}

	if (!cardHolderEmail.match(
			/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)) {
		message += "<div>Email is invalid</div>";
		$("#cardHolderEmail").css('background-color', '#FFFFDF');
		valid = false;
	}

	if (cardNumber != "") {
		$('#cardNumber').validateCreditCard(function(result) {
			if (!(result.valid)) {
				message += "<div>Card Number is Invalid</div>";
				$("#card-number").css('background-color', '#FFFFDF');
				valid = false;
			}
		});
	}

	if (cvv != "" && !cvvRegex.test(cvv)) {
		message += "<div>CVV is Invalid</div>";
		$("#cvv").css('background-color', '#FFFFDF');
		valid = false;
	}

	if (message != "") {
		$("#error-message").show();
		$("#error-message").html(message);
        $("#submit-btn").show();
        $("#loader").hide();
	}
	return valid;
}