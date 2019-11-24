<?php
use yii\helpers\Html;
?>
<!--start header -->

<div class="header">
    <div class="h-overlay"></div>


    <div class="upper-cont">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="eac-contact"><i class="far fa-clock"></i> 10:00 AM To 5:00 PM</p>
                    <p class="eac-contact"><a href="mailto:info@turbovanesltd.com"><i class="far fa-envelope"></i> info@turbovanesltd.com</a></p>
                </div>

                <div class="col-md-6 text-right">
                    <p class="eac-contact"><a href="tel:0121 558 6868"><i class="fas fa-phone-alt"></i> 0121 558 6868</a></p>
                    <p class="eac-contact"><a href="https://www.facebook.com/turbovanesltd/"><i class="fab fa-facebook-f"></i></a> </p>
                    <p class="eac-contact"><a href="https://twitter.com/turbovanesltd"><i class="fab fa-twitter"></i></a> </p>
                    <p class="eac-contact"><a href="https://www.turbovanesltd.com/#"><i class="fab fa-linkedin-in"></i></a> </p>
                    <p class="eac-contact"><a href="login.html"><i class="fas fa-lock"></i> Login</a> </p>
                </div>
            </div>
        </div>


    </div>

    <!-- start navbar -->

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="<?= \yii\helpers\Url::base(true);  ?>"><img src="./img/turbo-vanes-logo-new.png" class="th-logo" alt="turbo-vanes-logo-new"> </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="toggle-menuu">
                        <i></i>
                        <i></i>
                        <i></i>
                      </span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="products.html">PRODUCTS <i class="fas fa-chevron-down down-ico"></i></a>

                        <div class="down-menu">
                            <p class="eac-item"><a href="turbo-actuator.html">TURBO ACTUATOR</a></p>
                            <p class="eac-item"><a href="position-sensor.html">TURBO ACTUATOR POSITION SENSOR
                                </a></p>
                            <p class="eac-item"><a href="turbo-charger.html">TURBO CHARGER</a></p>
                            <p class="eac-item"><a href="turbo-cleaner.html">TURBO CLEANER</a></p>
                        </div>

                    </li>


                    <li class="nav-item">
                        <a class="nav-link" href="<?= \yii\helpers\Url::toRoute('about') ?>">ABOUT</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#">SUPPORT <i class="fas fa-chevron-down down-ico"></i> <span class="sr-only">(current)</span></a>

                        <div class="down-menu">
                            <p class="eac-item"><a href="return-policy">RETURNS POLICY</a></p>
                            <p class="eac-item"><a href="turbo-problems">TURBO PROBLEMS</a></p>
                            <p class="eac-item"><a href="delivery">DELIVERY</a></p>
                        </div>

                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="news">NEWS</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="contact">CONTACT</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="FAQ">FAQS</a>
                    </li>

                    <li class="nav-item shop-cartt">
                        <a class="nav-link" href="#"><i class="fas fa-shopping-cart"></i> <span class="num-in">2</span> </a>

                        <div class="down-menu">
                            <p class="eac-item no-prods disp-no">No products in the cart.</p>
                            <div class="prods">
                                <div class="eac-prod">
                                    <img src="images/Electric-Actuator-G-33-752406-6NW009206-767933-1-100x100.jpg" class="prod-img" alt="Electric-Actuator">

                                    <p class="prod-det">Electronic Turbo Actuator for Ford S-Max 1.8 Diesel QYWA 123 Garrett 742110-5007S</p>
                                    <span class="num-pri"><span>1</span>x<span>?144.00</span> <i class="fas fa-times remove-prod"></i></span>

                                </div>

                                <div class="eac-prod">
                                    <img src="images/Electric-Actuator-G-33-752406-6NW009206-767933-1-100x100.jpg" class="prod-img" alt="Electric-Actuator">

                                    <p class="prod-det">Electronic Turbo Actuator for Ford S-Max 1.8 Diesel QYWA 123 Garrett 742110-5007S</p>
                                    <span class="num-pri"><span>1</span>x<span>?144.00</span> <i class="fas fa-times remove-prod"></i></span>

                                </div>
                            </div>

                            <div class="sub-tot">
                                <p class="sub">Subtotal:</p>

                                <p class="tot">?<span class="num">144.00</span></p>

                            </div>

                            <div class="view-check row">
                                <div class="col-6">
                                    <a href="cart.html">
                                        <button class="view hvr-bounce-to-top">VIEW CART</button>
                                    </a>
                                </div>

                                <div class="col-6">
                                    <a href="checkout.html">
                                        <button class="chec">CHECKOUT</button>

                                    </a>
                                </div>
                            </div>


                        </div>

                    </li>

                    <li class="nav-item search-on">
                        <div class="nav-link" href="#"><i class="fas fa-search th-search"></i></div>


                        <div class="search-menu">
                            <input type="search" class="want-buy" placeholder="I want to buy...">

                            <i class="fas fa-search search-ico"></i>
                        </div>

                    </li>

                </ul>

            </div>
        </div>
    </nav>

    <!--end navbar -->
    <?
        if(!empty($this->context->brandList)) {
            echo $this->render('_front_page_header_bottom');
        }else {
            echo $this->render('_header_bottom');
        }

    ?>

</div>

<!--end header -->
