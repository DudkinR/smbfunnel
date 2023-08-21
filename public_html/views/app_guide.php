<div class="container app-guide">
    <div class="row">
        <div class="col-md-12">

            <div class="accordion" id="accordionExample">
                <div class="card z-depth-0 bordered">
                    <div class="card-header card-header-copy" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <i class="fas fa-arrow-right"></i> <?php w("Product templating for email & membership area"); ?>
                            </button>
                        </h5>
                    </div>
                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-1"><i class="fas fa-info-circle fa-3x"></i>
                                    </div>
                                    <div class="col-md-11 pt-8">
                                        <span class="mt-2">
                                   <?php w("You can customize the product access email as well the membership area. The details written below."); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span class="total-data"><?php w("Display Single Product"); ?></span>
                                <pre>
                                <code class="language-js">
                                {product.id} -<?php w("For Product ID"); echo "\n"; ?>
                                {product.title} -<?php w("Product Title"); echo "\n"; ?>
                                {product.url} -<?php w("Product URL"); echo "\n"; ?>
                                {product.currency} -<?php w("Product Currency"); echo "\n"; ?>
                                {product.download_url} -<?php w("Product Download URL"); echo "\n"; ?>
                                {product.image} -<?php w("Product Image"); echo "\n"; ?>
                                {product.price} -<?php w("Product Price"); echo "\n"; ?>
                                {product.tax} -<?php w("Product Tax"); echo "\n"; ?>
                                </code>
                            </pre>

                            <span class="total-data"><?php w("Multiple Product Looping"); ?></span>
                            <pre>
                                <code class="language-js">
                                {products}
                                    {product.id}
                                    {product.title}
                                    {product.url} 
                                    {product.currency}
                                    {product.download_url}
                                {/products}
                                </code>
                            </pre>
    
                            <span class="total-data"><?php w("Example"); ?></span>
                            <pre>
                                <code class="language-html">
                                Hi, Your Purchased products are 
                                {products}
                                    (#{product.id}) {product.title} URL: {product.url}
                                {/products}
                                </code>
                            </pre>
                            <span class="total-data ms-5"><?php w("Expected Output (Let's assume we have two products)") ?></span>
                            <pre class="mt-0 ms-5">
                                <code class="language-css mt-0">
                                Hi, Your Purchased products are
                                (#12345) Product1 url: http://producturl.com
                                (#123456) Product2 url: http://producturl.com
                                </code>
                            </pre>
                            <span class="total-data ms-5"><?php w("Unsubscribe Shortcode") ?></span>
                            <pre class="mt-0 ms-5">
                                <code class="language-css mt-0">
                                {unsubscribe}link{/unsubscribe}
                                </code>
                            </pre>
                            <p></p>
                        </div>
                    </div>
                </div>
                <div class="card z-depth-0 bordered">
                    <div class="card-header card-header-copy" id="headingOneSub">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOneSub" aria-expanded="true" aria-controls="collapseOne">
                                <i class="fas fa-arrow-right"></i> <?php w("Product templating for checkout page"); ?>
                            </button>
                        </h5>
                    </div>
                    <div id="collapseOneSub" class="collapse" aria-labelledby="headingOneSub" data-parent="#accordionExample">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-1"><i class="fas fa-info-circle fa-3x"></i>
                                    </div>
                                    <div class="col-md-11 pt-8">
                                        <span class="mt-2">
                                        <?php w('The templating process for the checkout page is the same as "Product templating for email & membership area". Here in the checkout page, we have few more independent variable support'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span class="total-data"><?php w("Additional Variables"); ?></span>
                                <pre>
                                    <code class="language-js">
                                    {shipping_charge} -<?php w("To display total shipping charge"); echo "\n"; ?>
                                    {tax_amount} -<?php w("To display total tax amount"); echo "\n"; ?>
                                    {subtotal_price} -<?php w("Total product price"); echo "\n"; ?>
                                    {total_price} -<?php w("Total product price including tax and sheeping charges"); echo "\n"; ?>
                                    {payment_currency} -<?php w("To display currency that's going to be applied"); echo "\n"; ?> 
                                    </code>
                                </pre>

                            <span class="total-data"><?php w("Setting up checkout button"); ?></span>
                                <br>
                                <p>
                                    <?php echo w('You need an HTML form and at least a submit button with the "name" attribute and the attribute value will be "confirm_checkout". Take a look at the example given below for a better understanding'); ?>
                                </p>

                            <pre>
                                <code class="language-html">
                                    &lt;form action="" method="POST"&gt;
                                        &lt;button name="confirm_checkout" type="submit"&gt;Checkout&lt;/button&gt;
                                    &lt;/form&gt;
                                </code>
                            </pre>
    
                            <span class="total-data"><?php w("Example"); ?></span>
                            <p><?php w("Please watch our Checkout related templates for better understanding"); ?></p>
                            <p></p>
                        </div>
                    </div>
                </div>
                <div class="card z-depth-0 bordered">
                    <div class="card-header card-header-copy" id="headingTwo">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <i class="fas fa-arrow-right"></i><?php w("Templating with membership details"); ?>
                            </button>
                        </h5>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-1"><i class="fas fa-info-circle fa-3x"></i>
                                    </div>
                                    <div class="col-md-11 pt-8"><span class="">
                                            <?php w("Membership email & membership area customization."); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <span class="total-data"><?php w("Display Single Variables"); ?></span>
                            <pre>
                                <code class="language-js">
                                {name} -<?php w("Member name"); echo "\n"; ?>
                                {email} -<?php w("Member Email"); echo "\n"; ?>
                                //--<?php w("Example of dynamic variables"); ?>--<?php echo "\n"; ?>
                                    {Any_Stored_Entity} - /*--<?php w("You can display variable added for the relevant member 
                                    that came through Order Form, Registration Forms, IPN Requests"); ?>--*/ <?php echo "\n"; ?>
                                    //<?php w("Example"); echo "\n"; ?>
                                    {address} - <?php w("Address that stored through any method mentioned above"); ?> 
                                
                                //<?php w("Display payment data"); ?> 
                                    {payment_id} - <?php w("Payment id"); ?> 
                                    {total_paid} -<?php w("Total payment amount"); ?> 
                                    {payer_name} -<?php w("Payer name"); ?> 
                                    {payer_email} -<?php w("Payer email"); echo "\n"; ?>

                                // <?php w("No-conflict rule"); echo "\n"; ?>
                                    /*--<?php w("To avoid unwanted replacement append \${1} with variables",array('`member`')); ?>--*/
                                    //<?php w("Example"); echo "\n"; ?>
                                    {member.name} -<?php w("Member name"); ?> 
                                    {member.address} -<?php w("Member address"); echo "\n"; ?>
                                </code>
                            </pre>

                            <span class="total-data text-lg"><?php w("Multiple Membership Looping With Mail Content"); ?></span>
                            <pre>
                                <code class="language-js">
                                {members}
                                    {member.payment_id}
                                    {member.login_url} 
                                    {member.email} 
                                    {member.password} 
                                    {member.payer_name} 
                                    {member.payer_email}
                                    {member.total_paid}
                                    //...<?php w("For dynamic variables write"); ?> {member.your_variable_name}
                                {/members}
                                </code>
                            </pre>
                            
                            <span class="total-data text-lg"><?php w("Example"); ?>:</span>
                            <pre>
                                <code class="language-html">
                                Hi, Your membership credentials for {product.title} written below

                                {members}
                                    Login URL: {member.login_url} 
                                    Email: {member.email} 
                                    Password: {member.password}
                                {/members}
                                </code>
                            </pre>
                            <span class="total-data text-lg ms-5"><?php w("Expected output written below (Lets assume the buyer has one product `Test Product` and you are creating two membership for it)"); ?></span>
                            <pre class="ms-5">
                                <code class="language-html">
                                Hi, Your membership credentials for Test Product written below

                                Login URL: http://yoursite.com/path/login_page 
                                Email: user@host.com 
                                Password: @aedvhb545tf

                                Login URL: http://yoursite.com/path/login_page_2 
                                Email: user@host.com 
                                Password: @dfg2455545-
                                </code>
                            </pre>
                            <span class="total-data"><?php w("Logout Link On Membership Pages"); ?></span>
                            <pre>
                                <code class="language-js">
                                {logout_url} -<?php w("Log out URL"); echo "\n"; ?> 
                                {logout_link} -<?php w("Linked logout text."); echo "\n"; ?>
                                </code>
                            </pre>

                        </div>
                    </div>
                </div>
                <div class="card z-depth-0 bordered">
                    <div class="card-header card-header-copy" id="headingThree">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <i class="fas fa-arrow-right"></i> <?php w("Products"); ?>
                            </button>
                        </h5>
                    </div>
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-1"><i class="fas fa-info-circle fa-3x"></i>
                                    </div>
                                    <div class="col-md-11 my-auto"><span class="">
                                            <?php w("Add Optional Products In Order Form"); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
   
                            <p><?php w("You can add optional products on the order form that people can buy alongside the main product."); ?><br>
                            (** <?php w("You can do the same with our editor without writing HTML manually"); ?>)<br>
                            </p>
                            <span class="total-data"><?php w("Example"); ?></span>
                            <pre style="margin-bottom:2px">
                                <code class="language-html">
                                   <?php w("Single Product"); echo "\n"; ?>
                                   <?php w("Field name \${1} and value should be preferred product id",array("`optional_products`")); echo "\n"; ?>

                                   <?php w("Example"); ?>:
                                   <span><</span>input type="checkbox" name="optional_products" value="12345">


                                   <?php w("Multiple Products"); echo "\n"; ?>
                                   <?php w("Field name \${1} and value should be preferred product ids",array('`optional_products[]`')); echo "\n"; ?>

                                   <?php w("Example") ?>:
                                   <span><</span>input type="checkbox" name="optional_products[]" value="123445">
                                   <span><</span>input type="checkbox" name="optional_products[]" value="123445">
                                </code>
                            </pre>
                        </div>
                    </div>
                </div>

                <div class="card z-depth-0 bordered">
                    <div class="card-header card-header-copy" id="heading4">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapseThree">
                                <i class="fas fa-arrow-right"></i> <?php w("Validation Errors"); ?></button>
                        </h5>
                    </div>
                    <div id="collapse4" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-1"><i class="fas fa-info-circle fa-3x"></i>
                                    </div>
                                    <div class="col-md-11 my-auto"><span class="">
                                            <?php w("Display Validation Error For Login, Register and Forgot Password pages"); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <pre>
                                <code class="language-html">
                                    {validation_error} - <?php w("Use this to display validation errors"); echo "\n"; ?> 
                                    <?php w("Example Output"); ?>:
                                    `member already exists`, `Invalid email id` etc
                                </code>
                            </pre>
                        </div>
                    </div>
                </div>


                <div class="card z-depth-0 bordered">
                    <div class="card-header card-header-copy" id="heading5">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapseThree">
                                <i class="fas fa-arrow-right"></i> <?php w("Template Creation Guide For Forgot Password Page"); ?>
                            </button>
                        </h5>
                    </div>
                    <div id="collapse5" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-1"><i class="fas fa-info-circle fa-3x"></i>
                                    </div>
                                    <div class="col-md-11 my-auto"><span class="">
                                    <?php w("Template Creation Guide For Forgot Password Page"); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <pre>
                                <code class="language-html">
                                    <?php w("Email Input"); echo "\n"; ?> 
                                    {insert_email}<?php w("content"); ?>{/insert_email}

                                    <?php w("Confirmation Message and OTP Input"); echo "\n"; ?>
                                    {confirmation_message}<?php w("content"); ?>{/confirmation_message}
                                    
                                    <?php w("Update password fields"); echo "\n"; ?>
                                    {update_password}<?php w("content"); ?>{/update_password}

                                    <span><<span>--! <?php w("For detail see our inbuilt templates"); ?> -->
                                </code>
                            </pre>
                        </div>
                    </div>
                </div>




            </div>
            <!-- product detail-->




        </div>

    </div>
</div>