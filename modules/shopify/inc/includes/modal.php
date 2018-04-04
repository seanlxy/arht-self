<?php

// CHECKOUT FORM OVERLAY
$tags_arr['modal'] .= '
<a href="javascript:;" class="btn btn--bg btn--bg-blue open-cart-btn">
	<i class="fa fa-shopping-cart"></i> <strong class="floating-cart-title">View Cart</strong>
</a>
<div class="shopping-cart-overlay"></div>
  <div class="shop-modal">
       <form method="post" action="" target="_blank" id="shopping-cart">
		<div class="container">
			<div class="row">
	      <div class="col-xs-12">
					<div class="flex-viewport">
					  <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12 text-right">
					   	<a href="javsacript:;" class="close-cart"><i class="fa fa-close"></i></a>
					  </div>
					</div>
				</div>
			 </div>

	        <div class="row" >
	             <div class="col-xs-12">
					<div class="flex-viewport">
					  <div class="col-md-12 col-sm-12 col-xs-12">
					   <h1 class="shop-modal--cart-title">Your Cart <img src="/graphics/shopify_loader.gif" class="shopify-loader" /> </h1>
					   <p class="cart-error-msg"></p>
					  </div>
					</div>
				</div>
				<div class="col-lg-3 col-md-12 col-xs-11 cart-list">
					<div class="flex-viewport">
					  <table class="shopping-cart">
						   <tbody>
						   </tbody>
					  </table>
					</div>
				</div>
			</div>

			<div class="row footer-checkout shopping-cart-actions">
				<div>
					<div class="flex-viewport">
					  <div class="col-md-6 col-sm-6 col-xs-6 text-left">
					    <span class="footer-checkout__total">Total</span>
					  </div>
					  <div class="col-md-6 col-sm-6 col-xs-4 text-right">
					    <span id="stotal"></span>
					  </div>
					</div>
				 </div>
				 <div>
					<div class="flex-viewport">
					  <div class="col-md-12 col-sm-12 col-xs-11" footer-checkout__shipping-message">
					    Shipping and discount codes are added at checkout.
					  </div>
					</div>
				 </div>
	             <div>
					<div class="flex-viewport">
					  <div class="col-md-12 col-sm-10 col-xs-10">
					  	  	<div class="col-md-6 col-sm-6 col-xs-6 text-left" style="display:none;">
					  	  		<a href="javscript:;" class="btn btn--ghost btn--ghost-blue empty-cart cart-btn"><strong>Empty</strong></a>
					  	  	</div>
					  	  	<div class="col-md-6 col-sm-6 col-xs-6 text-right" style="display:none;">
					  	  		<a href="javscript:;" class="btn btn--ghost btn--ghost-blue update-cart cart-btn"><strong>Update</strong></a>
					  	  	</div>
					  	  	<div style="clear:both;">
					    		<button type="button" class="btn btn--bg btn--bg-blue btn-submit checkout-btn" name="continue" value="1" tabindex="8"><strong>Checkout</strong></button>
					      	</div>
					      	<div class="checkout-block" style="display:none;"></div>
					  </div>
					</div>
				</div>
			</div>

	       </div>
	    </div>
	  </form>
	</div>';
?>
