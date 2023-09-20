<!doctype html>
<html>
<?php
$amount = $_SESSION['total_paid'];
$currency = $_SESSION['payment_currency'];
$productArray = $_SESSION['productArray'];
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Square Checkout</title>

    <link rel="stylesheet" href="<?php echo plugins_url('/css/checkout.css', __FILE__) ?>">


</head>
<!DOCTYPE html>
<html>

<head>
    <link href="<?php echo plugins_url('/css/app.css', __FILE__) ?>" rel="stylesheet" />
    <?php if ($_SESSION['environment'] == 0) { ?>
        <script type="text/javascript" src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
    <?php } else { ?>
        <script type="text/javascript" src="https://web.squarecdn.com/v1/square.js"></script>
    <?php } ?>
</head>

<body>
    <div class="container">
        <div class="text-center padding-vertical-medium border-header">
            <img height="40px" src="<?php echo $_SESSION['storeURL']; ?>" />
            <label style="  vertical-align: super; font-size: 30px; font-weight: 300;">
                <?php echo $_SESSION['storeName']; ?>
            </label>
        </div>
        <div class="row checkout-row">
            <div class="col-60 checkout-column">
                <div class="cart-before-form">

                    <h2 class="checkout-header padding-vertical-medium">Order Details</h2>
                    <div class="order-details-section">
                        <?php
                        foreach ($productArray as $val) {

                        ?>
                            <div class="order-details-section__item">
                                <div class="order-details-section__item__desc">

                                    <?php echo  $val['title']; ?>

                                </div>
                                <div class="order-details-section__item__price font-weight-500">
                                    <?php
                                    if (isset($val['quantity'])) {
                                    ?>
                                        $<?php echo $val['quantity'] * $val['price']; ?>.00
                                    <?php } else { ?>
                                        $<?php echo $val['price']; ?>.00
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="order-details-section__item">
                            <div class="order-details-section__item__desc">
                                Shipping Charge
                            </div>
                            <div class="order-details-section__item__price font-weight-500">
                                $<?php echo $_SESSION['sheepingcharge'] ?>.00
                            </div>
                        </div>
                        <div class="order-details-section__item">
                            <div class="order-details-section__item__desc">
                                Tax
                            </div>
                            <div class="order-details-section__item__price font-weight-500">
                                $<?php echo  $_SESSION['tax']; ?>.00
                            </div>
                        </div>
                    </div>

                    <div class="order-details-section">
                        <div class="font-weight-500">
                            Total
                            <span class="float-right">$<?php echo $_SESSION['total_paid']; ?>.00</span>
                        </div>
                    </div>

                </div>

                <form id="payment-form" style="margin: 0;">
                    <div id="google-pay-button"></div>
                    <div id="payment-divider" class="divider-text" style="display: block; transform-origin: 0px 0px; opacity: 1; transform: scale(1, 1);">
                        <span>OR</span>
                    </div>



                    <h2 class="checkout-header padding-vertical-medium">Personal Information</h2>

                    <div class="checkout-form">
                        <div class="input-row">
                            <div class="input-row__label">Email</div>
                            <div class="input-container">
                                <input type="email" class="input-row__input" name="email" placeholder="jane.doe@example.com" required value="<?php echo $_SESSION['email']; ?>">
                            </div>
                        </div>
                    </div>



                    <h2 class="checkout-header padding-vertical-medium">Payment Information</h2>
                    <div class="checkout-form">
                        <div class="input-row">
                            <div class="input-row__label">Name on Card</div>
                            <div class="input-container">
                                <input class="input-row__input" placeholder="Jane Doe" value="<?php echo $_SESSION['userName']; ?>" name="card_fullname" maxlength="26" required>
                            </div>
                        </div>


                    </div>
                    <h2 class="checkout-header padding-vertical-medium">Card Details</h2>
                    <div>

                        <div data-card-brand="unknown">
                            <!-- <div class="input-row__label">Card Number</div> -->

                            <div class="input-container" style="width: 100% ;">
                                <div id="card-container" style="margin-top:10px ;"></div>
                                <!-- <button id="card-button" type="button">Pay</button> -->
                                <!-- <div id="sq-card-number" class="input-row__input"></div> -->
                                <div class="input-row__card-image"></div>
                            </div>
                        </div>

                    </div>
                    <input type="hidden" value="<?php echo get_option('install_url') . '/index.php/?page=do_payment&execute=1'; ?>" id="returnURL" />
                    <input type="hidden" value="<?php echo get_option('install_url') . '/index.php/?page=do_payment&execute=0'; ?>" id="cancelURL" />
                    <input type="hidden" value="<?php echo $_SESSION['app_id']; ?>" id="app_id" />
                    <input type="hidden" value="<?php echo $_SESSION['location_id']; ?>" id="location_id" />
                    <input type="hidden" id="amount" value="<?php echo $amount; ?>" />
                    <input type="hidden" id="currency" value="<?php echo $currency; ?>" />

                </form>
            </div>

            <div class="col-40 checkout-column">
                <div class="cart-after-form">

                    <h2 class="checkout-header padding-vertical-medium">Order Details</h2>
                    <div class="order-details-section">
                        <?php
                        foreach ($productArray as $val) {

                        ?>
                            <div class="order-details-section__item">
                                <div class="order-details-section__item__desc">

                                    <?php echo  $val['title']; ?>

                                </div>
                                <div class="order-details-section__item__price font-weight-500">
                                    <?php
                                    if (isset($val['quantity'])) {
                                    ?>
                                        $<?php echo $val['quantity'] * $val['price']; ?>.00
                                    <?php } else { ?>
                                        $<?php echo $val['price']; ?>.00
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="order-details-section__item">
                            <div class="order-details-section__item__desc">
                                Shipping Charge
                            </div>
                            <div class="order-details-section__item__price font-weight-500">
                                $<?php echo $_SESSION['sheepingcharge']; ?>.00
                            </div>
                        </div>
                        <div class="order-details-section__item">
                            <div class="order-details-section__item__desc">
                                Tax
                            </div>
                            <div class="order-details-section__item__price font-weight-500">
                                $<?php echo  $_SESSION['tax']; ?>.00
                            </div>
                        </div>
                    </div>
                    <div class="order-details-section">
                        <div class="font-weight-500">
                            Total
                            <span class="float-right">$<?php echo $_SESSION['total_paid']; ?>.00</span>
                        </div>
                    </div>

                </div>
                <div class="button-container padding-vertical-small">
                    <button id="card-button" type="button" value="Place Order" class="button button--primary button--block">Place Order</button>
                </div>
                <div class="padding-horizontal-large padding-vertical-small text-center text-muted">
                    <small>
                        By continuing, you agree to the <a href="
  
    https://squareup.com/legal/privacy-no-account
  
" target=_blank>Square Privacy Policy</a>.
                    </small>
                </div>
            </div>
        </div>
        <div id="payment-status-container"></div>
        <div class="text-muted text-center">
            <div class="logo-container">
                <svg class="square-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44 44" width="44" height="44">
                    <path fill="#abaeb0" d="M36.65 0h-29.296c-4.061 0-7.354 3.292-7.354 7.354v29.296c0 4.062 3.293 7.354 7.354 7.354h29.296c4.062 0 7.354-3.292 7.354-7.354v-29.296c.001-4.062-3.291-7.354-7.354-7.354zm-.646 33.685c0 1.282-1.039 2.32-2.32 2.32h-23.359c-1.282 0-2.321-1.038-2.321-2.32v-23.36c0-1.282 1.039-2.321 2.321-2.321h23.359c1.281 0 2.32 1.039 2.32 2.321v23.36z"></path>
                    <path fill="#abaeb0" d="M17.333 28.003c-.736 0-1.332-.6-1.332-1.339v-9.324c0-.739.596-1.339 1.332-1.339h9.338c.738 0 1.332.6 1.332 1.339v9.324c0 .739-.594 1.339-1.332 1.339h-9.338z"></path>
                </svg>
            </div>
            <small>Powered by Square</small>
        </div>
        <div class="text-muted text-center">
            <small>
                <a href="
  
    https://squareup.com/legal/privacy-no-account
  
" target=_blank>Privacy Policy</a>
            </small>
        </div>
    </div>

</body>

</html>
<script>
    const appId = document.getElementById('app_id').value;
    const locationId = document.getElementById('location_id').value;
    const returnURL = document.getElementById('returnURL').value;
    const cancelURL = document.getElementById('cancelURL').value;


    async function initializeCard(payments) {
        const card = await payments.card();
        await card.attach('#card-container');

        return card;
    }

    function buildPaymentRequest(payments) {
        const amount = document.getElementById('amount').value;
        const currency = document.getElementById('currency').value;
        return payments.paymentRequest({
            countryCode: 'US',
            currencyCode: currency,
            total: {
                amount: amount,
                label: 'Total',
            },
        });
    }

    async function initializeGooglePay(payments) {
        const paymentRequest = buildPaymentRequest(payments);
        const googlePay = await payments.googlePay(paymentRequest);
        await googlePay.attach('#google-pay-button');

        return googlePay;
    }

    async function createPayment(token) {
        const body = JSON.stringify({
            locationId,
            sourceId: token,
        });

        const paymentResponse = await fetch('plugins/cf_plugin_for_square/app/square/payments.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body,
        });

        if (paymentResponse.ok) {
            return paymentResponse.json();
        }

        const errorBody = await paymentResponse.text();
        throw new Error(errorBody);
    }

    async function tokenize(paymentMethod) {
        const tokenResult = await paymentMethod.tokenize();
        if (tokenResult.status === 'OK') {
            return tokenResult.token;
        } else {
            let errorMessage = `Tokenization failed with status: ${tokenResult.status}`;
            if (tokenResult.errors) {
                errorMessage += ` and errors: ${JSON.stringify(
              tokenResult.errors
            )}`;
            }

            throw new Error(errorMessage);
        }
    }

    // status is either SUCCESS or FAILURE;
    function displayPaymentResults(status) {
        const statusContainer = document.getElementById(
            'payment-status-container'
        );
        if (status === 'SUCCESS') {
            statusContainer.classList.remove('is-failure');
            statusContainer.classList.add('is-success');
        } else {
            statusContainer.classList.remove('is-success');
            statusContainer.classList.add('is-failure');
        }

        statusContainer.style.visibility = 'visible';
    }

    document.addEventListener('DOMContentLoaded', async function() {
        if (!window.Square) {
            throw new Error('Square.js failed to load properly');
        }

        let payments;
        try {
            payments = window.Square.payments(appId, locationId);
        } catch {
            const statusContainer = document.getElementById(
                'payment-status-container'
            );
            statusContainer.className = 'missing-credentials';
            statusContainer.style.visibility = 'visible';
            return;
        }

        let card;
        try {
            card = await initializeCard(payments);
        } catch (e) {
            console.error('Initializing Card failed', e);
            return;
        }

        let googlePay;
        try {
            googlePay = await initializeGooglePay(payments);
        } catch (e) {
            console.error('Initializing Google Pay failed', e);
            // There are a number of reason why Google Pay may not be supported
            // (e.g. Browser Support, Device Support, Account). Therefore you should handle
            // initialization failures, while still loading other applicable payment methods.
        }

        async function handlePaymentMethodSubmission(event, paymentMethod) {
            event.preventDefault();

            try {
                // disable the submit button as we await tokenization and make a payment request.
                cardButton.disabled = true;
                const token = await tokenize(paymentMethod);
                const paymentResults = await createPayment(token);
                displayPaymentResults('SUCCESS');

                console.debug('Payment Success', paymentResults);
                const payment_id = paymentResults.payment.id;
                window.location = returnURL + '&payment_id=' + payment_id;
            } catch (e) {
                cardButton.disabled = false;
                displayPaymentResults('FAILURE');
                window.location = cancelURL;
                console.error(e.message);


            }
        }

        const cardButton = document.getElementById('card-button');
        cardButton.addEventListener('click', async function(event) {
            await handlePaymentMethodSubmission(event, card);
        });

        // Checkpoint 2.
        if (googlePay) {
            const googlePayButton = document.getElementById('google-pay-button');
            googlePayButton.addEventListener('click', async function(event) {
                await handlePaymentMethodSubmission(event, googlePay);
            });
        }
    });
</script>