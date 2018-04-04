/**
* Copyright © 2017 Tomahawk. All rights reserved.
* Author: Jed Diaz
*/

(function( $ ) {

	$.Shop = function( element ) {
		this.$element = $( element );
		this.init();
	};

	$.Shop.prototype = {
		init: function() {
			this.cartPrefix = "shopify-"; 
			this.cartName = this.cartPrefix + "cart";
			this.total = this.cartPrefix + "total";
			this.storage = sessionStorage;
			
			this.$shopUrl = this.getConfigItem('shopify_url');
			this.$formCart = this.$element.find( "#shopping-cart" );
			this.$subTotal = this.$element.find( "#stotal" );
			this.$shoppingCartActions = this.$element.find( ".shopping-cart-actions" ); 
			this.$emptyCartBtn = this.$shoppingCartActions.find( ".empty-cart" ); 
			this.$updateCartBtn = this.$shoppingCartActions.find( ".update-cart" );
			this.currency = "$"; 

			this.refineSearch();
			this.handleDetailQuantityInput();
			this.createCart();
			this.displayCart();
			this.updateCart();
	    	this.controlCartBox();
			this.filterProducts();
			this.handleProductListOption();
			this.handleAddToCartForm();
			this.deleteProduct();
			this.emptyCart();
			this.updatePrice();
			this.updatePriceProductDetail();
			this.displayProductDetail();
			this.handleAddToCartProductDetailForm();
			this.handleCheckout();
		},

		refineSearch: function(){
		
			$('.brands-checkbox').change(function() {

			  $('.collection_catalog_item').hide();

	          var brand = '';
	          var tags = '';
	          var catalog_tags = '';
	          $('.brands-checkbox:checked').each(function(i){
	          	  brand = $(this).val();	
		          $('.collection_catalog_item').each(function(i){
		           	var catalog_item  = $(this);
		           	var catalog_brand = $(this).data('brand');
		           	catalog_tags  = $(this).data('tags');

		           	if(catalog_brand == brand){
		           	 tags += catalog_tags+',';	
		           	 catalog_item.show();
		            }

		          }); 
		      }); 

	          tags = tags.replace(/,\s*$/, "");		
	          tags = tags.split(",");
	         
			  var uniqueTags = [];
			  $.each(tags, function(i, el){
			  	if(el){
			  		el = el.replace(' ', '-');
				 	el = el.replace('\'', '-');
				 	if($.inArray(el, uniqueTags) === -1){ 
						uniqueTags.push(el);
				 	}
			  	}
			  });
	
			  $('.tags-checkbox').each(function(i){
			  	var ths = $(this);
			  	var tag_value = ths.val();	
			  	tag_value = tag_value.replace(' ', '-');
				tag_value = tag_value.replace('\'', '-');
			  	var tag_class = '.'+tag_value+'-item';
				ths.removeAttr('checked');

			  	if($.inArray( tag_value, uniqueTags ) !== -1){
			  		$('.'+tag_value+'-item').show();
			  	} else{
			  		$('.'+tag_value+'-item').hide();
			  	}
			  });

	          if(brand == ''){
	          	$('.tags-class').show();
	          	$('.collection_catalog_item').show();
	          }

			});


			$('.tags-checkbox').change(function() {

			  $('.collection_catalog_item').hide();

	          var tags = '';
	          $('.tags-checkbox:checked').each(function(i){
	          	  tags = $(this).val();	
		          $('.collection_catalog_item').each(function(i){
		           	var catalog_item = $(this);
		           	var catalog_item_brand = catalog_item.data('brand');
		           	if(catalog_item.hasClass(tags)){

		           		// LOOP ALL BRAND IF EXIST
		           		var brand = '';
		           		$('.brands-checkbox:checked').each(function(i){
	          	  			brand = $(this).val();	

	          	  			if(brand == catalog_item_brand){
		           	 			catalog_item.show();
		           	 		}
		           	 	});

		           	 	if(brand == ''){
		           	 		catalog_item.show();
		           	 	}
		            }

		          }); 
		      }); 

	          if(tags == ''){
	          	$('.collection_catalog_item').each(function(i){
	          		var collection       = $(this);
	          		var collection_brand = $(this).data('brand');

	          		var brand = '';
	          		$('.brands-checkbox:checked').each(function(i){
		          	  	brand = $(this).val();	

		          	  	if(brand == collection_brand){
		          			collection.show();
		          		}
		          	});

	          		if(brand == ''){
		          		collection.show();
		          	}

	          	});
	          } 

			});
		},

		handleCartQuantityInput: function(){
			    $('<div class="quantity-nav"><div class="quantity-button quantity-up">+</div><div class="quantity-button quantity-down">-</div></div>').insertAfter('.quantity input');
			    $('.quantity').each(function() {
			      $(this).find('.quantity-up').click(function(event) {
			        var input = $(event.target).closest('td').find('.cart-qty');
			        var oldValue = parseInt(input.val());
			        var max = input.attr('max');

			        if (oldValue >= max) {
			          var newVal = oldValue;
			        } else {
			          var newVal = (oldValue + 1);
			        }

			        input.val(newVal);
			        input.trigger("change");
			      });

			      $(this).find('.quantity-down').click(function(event) {
			      	var input = $(event.target).closest('td').find('.cart-qty');
			      	var oldValue = parseInt(input.val());
			        if (oldValue <= 1) {
			          var newVal = 1;
			        } else {
			          var newVal = (oldValue - 1);
			        }

			        input.val(newVal);
			        input.trigger("change");
			      });

			    });
		},

		handleDetailQuantityInput: function(){
			if(!$('.quantity-description .quantity-nav').length){
				$('<div class="quantity-nav"><div class="quantity-button quantity-up">+</div><div class="quantity-button quantity-down">-</div></div>').insertAfter('.quantity-description input');
			}    
			$('.quantity-description').each(function() {

				$( document ).on( "click", ".quantity-description .quantity-up", function( e ) {

					var oldValue = parseInt($('.product-detail-cart-qty').val()),
					min = $('.product-detail-cart-qty').attr('min'),
					max = $('.product-detail-cart-qty').attr('max');

					if (oldValue >= max) {
						var newVal = oldValue;
					} else {
						var newVal = oldValue + 1;
					}

					$(".quantity-description input").val(newVal);
					$(".quantity-description input").trigger("change");
				});

				$( document ).on( "click", ".quantity-description .quantity-down", function( e ) {
					var oldValue = parseInt($('.product-detail-cart-qty').val()),
					min = $('.product-detail-cart-qty').attr('min'),
					max = $('.product-detail-cart-qty').attr('max');

					if (oldValue <= 1) {
						var newVal = 1;
					} else {
						var newVal = oldValue - 1;
					}

					$(".quantity-description input").val(newVal);
					$(".quantity-description input").trigger("change");
				});
			});

			$( document ).on( "change", ".product-detail-cart-qty", function( e ) {
				var input = $('.quantity-description input[type="number"]'),
				min = input.attr('min'),
				max = input.attr('max');
				var oldValue = parseFloat(input.val());
				var newVal = '';

				if (oldValue > max) {
					newVal = max;
					
					$(".quantity-description input").val( newVal );
					$(".quantity-description input").trigger("change");
				}
				
			});	
		},

		getConfigItem: function(prop) {

			this.config = {}; 
			this.config = $.extend(true, this.config, jsVars);

	        return this.getVar(prop, this.config);
	    },

	    getVar: function(property, obj) {

	        if(obj.hasOwnProperty(property)) return obj[property];

	        for(var prop in obj) {
	            if( obj[prop].hasOwnProperty(property) ) {
	                return obj[prop][property];
	            }
	        }
	        
	        return false;
	    },

		createCart: function() {
			if( this.storage.getItem( this.cartName ) == null ) {
			
				var cart = {};
				cart.items = [];
			
				this.storage.setItem( this.cartName, this._toJSONString( cart ) );
				this.storage.setItem( this.total, "0" );
			}
		},

		deleteProduct: function() {
			
			var self = this;
			if( self.$formCart.length ) {
				
				$( document ).on( "click", ".pdelete a", function( e ) {
					e.preventDefault();

					var cart = self._toJSONObject( self.storage.getItem( self.cartName ) );
					var items = cart.items;

					var productId = $( this ).data( "product" );
					var newItems = [];
					for( var i = 0; i < items.length; ++i ) {
						var item = items[i];
						var product = item.id;	

						if( product == productId ) {
							items.splice( i, 1 );
						}
					}
					newItems = items;
					
					var updatedCart = {};
					updatedCart.items = newItems;

					var updatedTotal = 0;
					var totalQty = 0;
					if( newItems.length == 0 ) {
						updatedTotal = 0;
						totalQty = 0;
					} else {
						for( var j = 0; j < newItems.length; ++j ) {
							var prod = newItems[j];
							var sub = prod.price * prod.qty;
							updatedTotal += sub;
							totalQty += prod.qty;
						}
					}

					self.storage.setItem( self.total, self._convertNumber( updatedTotal ) );
					self.storage.setItem( self.cartName, self._toJSONString( updatedCart ) );
					$( this ).parents( ".cart-item" ).remove();
					//self.$subTotal[0].innerHTML = self.currency + " " + self.storage.getItem( self.total );
					self.$subTotal[0].innerHTML = self.currency + " " + updatedTotal; 
				});
			}
		},

		controlCartBox: function() {
			$(document).on( "click", ".close-cart", function(e){
	          e.preventDefault();

	          	$('.shopping-cart-overlay').hide();
	          	$('.shop-modal').hide();
	        }); 

	        $(document).on( "click", ".open-cart-btn", function(e){
	          e.preventDefault();

	          	$('.shopping-cart-overlay').show();
	          	$('.shop-modal').show();
	        });
		},

		filterProducts: function() {
			$(document).on( "change", ".shopify-filter", function(e){
	          e.preventDefault();

	          var filter_type = $(this).val();

	          if(filter_type == 'all'){
	              
	              $('.collection_catalog_item').fadeIn( "fast" );

	          } else{

	              $(".shopify-filter option").not(":eq(0)").each(function(){
	                var value = $(this).val();

	                if(filter_type !== value){
	                  $( "." + value ).hide();
	                } 
	              });

	              $('.'+filter_type).fadeIn( "fast" );

	          }
	        });
		},

		handleProductListOption: function(){
			var self = this;
			var obj   = {};

			$( document ).on( "change", ".option-dropdown", function( e ) {
				e.preventDefault();
				 var selected_id     = $(this).find(':selected').data('id');			
				 var length = parseInt( $(this).find(':selected').data('length') );
				// var key    = parseInt( $(this).find(':selected').data('key') );
				// var value  = $(this).find(':selected').data('val');
				var len = '';
				$('.option-dropdown-'+selected_id).each(function(){ 
					var id     = $(this).find(':selected').data('id');			
					var length = parseInt( $(this).find(':selected').data('length') );
					var key    = parseInt( $(this).find(':selected').data('key') );
					var value  = $(this).find(':selected').data('val');

					if(id){
						obj[key] = { key: key, value: value };
						var keys = Object.keys(obj);
						len = keys.length
					}
				});

				if(len == length){
					self.__checkItemProductListOption(selected_id, obj);
					obj = {};
				}

				//console.log(obj);

				/*if(id){ 
					obj[key] = { key: key, value: value };

					var keys = Object.keys(obj);
					var len = keys.length

					if(len == length){
						self.__checkItemProductListOption(id, obj);
						obj = {};
					}
				}*/

			});	
		},

		__checkItemProductListOption: function( id, obj ){
			var self = this;

			var item = '';
	        var text = '';

	        for (var key in obj) {
			    if (obj.hasOwnProperty(key)) {
			        item = obj[key].value; 
			        text += item + ' / ';
			    }
			}

			var title_str = text.substring(0, text.length - 2);
			var title_str = title_str.trim();		

			var item = self._getVariant( id, title_str );

			if( item != null ){
				$('.collection_catalog_item--price-'+id).html( this.currency + item.price );
			}

			if( item.inventory_quantity == 0 ) {
				$('.product-list-btn-'+id).addClass('disabled');
			} else{
				$('.product-list-btn-'+id).removeClass('disabled');
			}
		
		},

		displayProductDetail: function() {
			var self = this;

			$(document).on( "click", ".view-details", function(e){
	          e.preventDefault();

	          var handle     = $(this).data('handle'),
	              pathname   = window.location.pathname,
	              array      = pathname.split ('/'),
	              shop       = array[1],
	              collection = array[2];

	          if (history.pushState) {
		          var newurl = window.location.protocol + "//" + window.location.host + '/' + shop + '/' + collection + '/' + handle;
		          window.history.pushState({path:newurl},'',newurl);
		      }

	          self._showDetailProduct( handle );

	          $("html, body").animate({ scrollTop: 0 }, 600);

	        });

	        $(document).on( "click", ".back-btn", function(e){
	        	e.preventDefault();

	        	var pathname = window.location.pathname,
	        		array      = pathname.split ('/');
	        		shop       = array[1],
	              	collection = array[2];

	        	//history.pushState('', document.title, window.location.pathname);

	        	if (history.pushState) {
		          var newurl = window.location.protocol + "//" + window.location.host + '/' + shop + '/' + collection;
		          window.history.pushState({path:newurl},'',newurl);
		      	}

	        	$('.collection-product-list').show();
	        	$('.product-detail').hide();
	        });

	        // WHEN PRODUCT IS CLICKED AND BROWSER IS RELOADED
	        var pathname = window.location.pathname,
	       	    array    = pathname.split ('/');

	       	if(array[3] !== undefined){
	       		var handle = array[3];

	       		self._showDetailProduct( handle );
	       	}
		},

		_showDetailProduct: function( current_handle ) {
			var self = this;

			var collection_items =  self.getConfigItem('collection_items');
			var products = collection_items['products'];

			for( var i = 0; i < products.length; ++i ) {
				var item            = products[i];
				var title           = item.title;	
				var body_html       = item.body_html;	
				var created_at      = item.created_at;	
				var handle          = item.handle;	
				var id              = item.id;	
				var image           = item.image;	
				var images          = item.images;	
				var options         = products[i].options;	
				var product_type    = item.product_type;	
				var published_at    = item.published_at;	
				var published_scope = item.published_scope;	
				var tags            = item.tags;	
				var template_suffix = item.template_suffix;	
				var variants        = item.variants;	
				var vendor          = item.vendor;	

				var sku             = variants[0].sku;	
				var weight          = variants[0].weight + ' ' + variants[0].weight_unit;	

				if( current_handle == handle ){
					$( ".product-options").show();	
					var availability    = 'In Stock';
					if(variants[0].inventory_management !== null){
						availability = variants[0].inventory_quantity + ' in stock';
						item_available = parseInt(variants[0].inventory_quantity);
						$('.product-detail-cart-qty').attr('max', item_available);
					} else{
						$('.product-detail-cart-qty').attr('max', '99999');
					}	

					var no_variation = false;
					if(variants[0].inventory_quantity == 0){
						var stockLabel = 'Out of Stock';
						var option_list = '';
					} else{
						var stockLabel = '';
						var option_list = '';
						$.each(options, function (o) {
							var name = options[o].name;

							if(name == 'Title'){
								no_variation = true;
							}

						    option_list += '<div class="product-options__label">' + name + ': </div><div class="product-options__items">';
						    $.each(options[o].values, function (key, val) {
						        option_list += '<a href="javascript:;" class="option-link option-item-' + o +'" data-value="' + val + '" data-selected="" data-length="' + options.length + '" data-key="' + o +'""  data-id="' + id +'">' + val + '</a>';
						    });
						    option_list += '</div>';
						});	
					}

					var product_price = this.currency + variants[0].price;
					if(variants[0].compare_at_price){
						product_price = '<span class="compare-price">' + this.currency + variants[0].compare_at_price + '</span>' + ' ' + '<span>' + this.currency + variants[0].price + ' </span> <span class="sale">Sale</span>';
					}

					$('.stock-label').text(stockLabel);
					$('.product-detail').attr('data-id', id);
					$('.product-detail').attr('class', 'container-fluid product-detail product-detail-' + id);
					$('.product-photo').attr('src', image.src);
					$('.product-title').text( title ); 
					$('.product-availability').html( 'Availability: <span class="content">' + availability + '</span>' ); 

					$('.product-sku').html( 'SKU: <span class="content">' + sku + '</span>' ); 
					$('.product-vendor').html( 'Vendor: <span class="content">' + vendor + '</span>' ); 
					$('.product-weight').html( 'Weight: <span class="content">' + weight + '</span>' ); 

					$('.breadcrumbs__title').text( title ); 
					$('.product-price').html( product_price );
					$('.product-options').html( option_list );
					$('.product-body').html( body_html );
					$('.product-tags').html( 'Tags: <span class="content">' + tags + '</span>' ); 
					$('.product-detail-cart-qty').val(1);
					$('.product-detail-btn').addClass('disabled');

					if(no_variation){
						$( ".option-item-0" ).trigger( "click" );
						$( ".product-options").hide();
					}
				}

			}

			$('.collection-product-list').hide();
	        $('.product-detail').show();
		},

		// Update Price Product Detail
		updatePriceProductDetail: function(){
			var self = this;
			var obj   = {};

			$( document ).on( "click", ".option-link", function( e ) {
				e.preventDefault();

				var id    = $(this).data('id');
			
				var length = parseInt( $(this).data('length') );
				var key = parseInt( $(this).data('key') );
				var value = $(this).data('value'); 

				if($.isEmptyObject(obj)){
					$('.product-detail-' + id + ' .option-link').removeClass('item-selected');

					$('.product-detail-' + id + ' .option-item-' + key).attr('data-selected','');
					$(this).attr('data-selected','true');
					$('.product-detail-' + id + ' .option-item-' + key).removeClass('item-selected');
					$(this).addClass('item-selected');
				} else{
					$('.product-detail-' + id + ' .option-item-' + key).attr('data-selected','');
					$(this).attr('data-selected','true');
					$('.product-detail-' + id + ' .option-item-' + key).removeClass('item-selected');
					$(this).addClass('item-selected');
				}	

				obj[key] = { key: key, value: value };

				$('.product-detail-cart-qty').val(1);

				// GET LENGTH, IF ALL OPTIONS ARE SELECTED CHECK PRODUCT DETAIL
				var keys = Object.keys(obj);
				var len = keys.length

				if(len == length){

					self.__checkItemProductDetail(id, obj);
					obj = {};
				}
			});

		},

		__checkItemProductDetail: function( id, obj ){
			var self = this;

			var item = '';
	        var text = '';

	        for (var key in obj) {
			    if (obj.hasOwnProperty(key)) {
			        item = obj[key].value; 
			        text += item + ' / ';
			    }
			}

			var title_str = text.substring(0, text.length - 2);
			var title_str = title_str.trim();		

			var item = self._getVariant( id, title_str );
			window.product_detail_item = item;

			$('.product-sku').html( 'SKU: <span class="content">' + item.sku + '</span>' ); 
			$('.product-weight').html( 'Weight: <span class="content">' + item.weight + '</span>' ); 
			$('.product-vendor').html( 'Vendor: <span class="content">' + item.vendor + '</span>' ); 

			if( item != null ){

				$('.product-price').html( this.currency + item.price );

				if( item.inventory_management != null ) {
					$(".product-detail").find('.product-add-cart-btn').removeClass('disabled');
					$(".product-detail").find('.product-add-cart-btn').addClass('addproductdetailstocart');
					

					$('.product-detail-cart-qty').attr('max', item.inventory_quantity);
					var availability = item.inventory_quantity + ' in stock';
					$('.product-availability').html( 'Availability: <span class="content">' + availability + '</span>' ); 

					if(item.inventory_quantity == 0) {
						$(".product-detail").find('.product-add-cart-btn').removeClass('addproductdetailstocart');
						$(".product-detail").find('.product-add-cart-btn').addClass('disabled');
					} 
				} else{
					$(".product-detail").find('.product-add-cart-btn').removeClass('disabled');
					$(".product-detail").find('.product-add-cart-btn').addClass('addproductdetailstocart');
				}
			}
		},

		handleAddToCartProductDetailForm: function() {
			var self = this;

		 	$(document).on( "click", ".addproductdetailstocart", function(e){
	         e.preventDefault();

	         /* GET ALL OPTION SELECTED*/ 
	         var option_title = "";
	         var option_id    = 0;
	         $( ".option-link.item-selected" ).each(function( index ) {
	      	   option_item = $(this).data('value');
	      	   option_id   = $(this).data('id');
	      	   option_title += option_item + ' / ';
	         });

			 var option_title_str = option_title.substring(0, option_title.length - 2);
			 option_title = option_title_str.trim();		

			 var item = self._getVariant( option_id, option_title );
             /* END GET ALL OPTIONS SELECTED */

	          var current_qty = $('.product-detail-cart-qty').val();
			  item.qty = current_qty;

			  // Max quantity control
	          if(item.inventory_management !== 'null' && item.inventory_management !== null){
	          	var item_max_quantity = item.inventory_quantity;
	      	  	if( current_qty > item_max_quantity ){
	      	  		item.qty = item_max_quantity;
	      	  	}
	      	  }
	      	  // End Max quantity control

			  if( item != null ){
				  if( item.length !== 0 ){

				  	  var current_item_id = item.id;
					  var exist = self._checkUpdateSingleItemExist( item );
					  
					  if(exist == 'N'){
						self._addToCart(item);
					  } 

					  self.calculateAndUpdatePrice();				  
				  }
			  } 

			  self.displayCart();

			  $( ".open-cart-btn" ).trigger( "click" );
	        }); 
		},
		// END Update Price Product Detail
		
		calculateAndUpdatePrice: function() {
			var self = this;

			var cart = self._toJSONObject( self.storage.getItem( self.cartName ) );
			var cart_items = cart.items;
			var total_price = 0;
			for( var i = 0; i < cart_items.length; ++i ) {
				var cart_qty   = self._convertString(cart_items[i].qty);
				var cart_price = self._convertString(cart_items[i].price);

				total_price = total_price + (cart_qty * cart_price); 
			}

			self.storage.setItem( self.total, total_price );	
		},

		displayCart: function() {
			var self = this;

			if( this.$formCart.length ) {
				var cart = this._toJSONObject( this.storage.getItem( this.cartName ) );

				var items          = cart.items;
				var $tableCart     = this.$formCart.find( ".shopping-cart" );
				var $tableCartBody = $tableCart.find( "tbody" );

				$tableCartBody.html('');

				if( items.length == 0 ) {
					$tableCartBody.html( "" );	
				} else {

					for( var i = 0; i < items.length; ++i ) {
						var item = items[i];
						var id = item.id;
						var photo = item.photo;
						var product = item.product;
						var variants = item.variants;

						var inventory_management = item.inventory_management;
						var sku = item.sku;
						var weight = item.weight;
						var vendor = item.vendor;
						var inventory_quantity = item.inventory_quantity;

						var max_inventory = inventory_quantity;
						var qty = (item.qty > inventory_quantity) ? inventory_quantity : item.qty;
						
						if(item.inventory_management == 'null' || item.inventory_management == null){
							max_inventory = '10000';
							qty = item.qty;
						} 

						var price = item.price;
						var calculate_price = price * qty;
						calculate_price = Math.round(calculate_price * 100) / 100;
						var totalprice = this.currency + calculate_price;
						

						var hidden_html = "<span class='cart-price' style='display:none;'>" + item.price + "</span>" + 
										  "<span class='cart-variants' style='display:none;'>"+ variants + "</span>" + 
										  "<span class='cart-id' style='display:none;'>"+ id + "</span>" +
										  '<input type="text" value="' + id + '" name="id[]" style="display: none;" />' +
										  "<input type='text' value='" + sku + "' name='sku' style='display: none;' />" +
										  "<input type='text' value='" + weight + "' name='weight' style='display: none;' />" + 
										  "<input type='text' value='" + vendor + "' name='vendor' style='display: none;' />" +
										  "<input type='text' value='" + inventory_management + "' name='inventory_management' style='display: none;' />" +
										  "<input type='text' value='" + inventory_quantity + "' name='inventory_quantity' style='display: none;' />";

										for (var n = 0; n < qty; ++ n){
										  hidden_html += '<input type="text" value="1" name="qty[]" style="display: none;" />';
										}

						if(variants == 'Default Title'){
							variants = '';
						}

						var html = "<tr class='cart-item'>" +
									  "<td class='shopping-cart__photo-display'><img src='" + photo + "' class='cart-photo' width='50'/></td>" +
									  "<td class='shopping-cart__qty'><table>" +
									      "<tr>"  +
									        "<td><span class='cart-title'>" + product + "</span></td>"  +
									      "</tr>"  +
									      "<tr>"  +
									        "<td><div class='quantity'><input type='number' value='" + qty + "' class='cart-qty' min='1' max='" + max_inventory + "'  style='width:100px;'/></div></td>"  +
									      "</tr>"  +
									      "</table>"  +
 									  "</td>"  +
									  "<td class='shopping-cart__information hidden-xs'><table>"  +
									      "<tr>"  +
									       "<td><span class='shopping-cart__variants'>" + variants + "</span></td>"  +
									      "</tr>"  +
									      "<tr class='shopping-cart__price'>"  +
									       "<td><b class='total-item-price'>" + totalprice + "</b></span>" + hidden_html + "</td>"  +
									      "</tr>"  +
									      "</table>"  +
									  "</td>"  +
									  "<td class='pdelete'><a href='javascript:;' data-product='" + id + "'><i class='fa fa-times-circle-o'></i></a></td>" +
									 "</tr>";

						$tableCartBody.html( $tableCartBody.html() + html );
				
						$('.checkout-form-block').append('<input type="text" value="'+id+'" name="id[]" style="display: none;" /><input type="text" value="'+qty+'" name="qty[]" style="display: none;" />'); 						


					}
				}

				if( items.length == 0 ) {
					this.$subTotal[0].innerHTML = this.currency + " " + 0.00;
				} else {	
					var total = this.storage.getItem( this.total );
					total = Math.round(total * 100) / 100;

					this.$subTotal[0].innerHTML = this.currency + " " + total;
				}
			}

			// ON Change Value
			$('.cart-qty').on( "change", function() {
			  	$( ".update-cart" ).trigger( "click" );
			});

			this.handleCartQuantityInput();
		},


		// Update Price Listing
		updatePrice: function(){
			var self = this;

			$( document ).on( "change", ".option-dropown", function( e ) {
				e.preventDefault();
				var id    = $(this).data('id');
				
				self.__checkItem(id);
			});

			$( ".collection_catalog_item" ).each(function( index ) {
			  	var id = $(this).data('id');

			  	self.__checkItem( id );
			});

		},

		// CHECK ITEM AND CHANGE THE PRICE, DISABLE THE ADD TO CART BUTTON IF INVENTORY IS EMPTY

		__checkItem: function( id ){
			var self = this;

			var item = '';
	        var text = '';
			$(".collection-item-"+id+ " :input").each(function(index, elm){
				item     = $(elm).val(); 
				text += item + ' / ';
			});

			var title_str = text.substring(0, text.length - 2);
			var title_str = title_str.trim();		

			var item = self._getVariant( id, title_str );

			if( item != null ){

				$(".collection-item-"+id).find('.collection_catalog_item--price').text( '$' + item.price );


				if( item.inventory_management != null ) {
					//$(".collection-item-"+id).find('.addtocart').show();
					$(".collection-item-"+id).find('.list-add-cart-btn').removeClass('disabled');
					$(".collection-item-"+id).find('.list-add-cart-btn').addClass('addtocart');
					if(item.inventory_quantity == 0) {

						//$(".collection-item-"+id).find('.addtocart').hide();
						$(".collection-item-"+id).find('.list-add-cart-btn').removeClass('addtocart');
						$(".collection-item-"+id).find('.list-add-cart-btn').addClass('disabled');

					} 
				}

			}
		},
		// END Update Price Listing

		handleAddToCartForm: function() {
			var self = this;

			$(document).on( "click", ".addtocart", function(e){
	          e.preventDefault();

	          var id = $(this).data('id');
	          var item = '';
	          var text = '';

	          $(".collection-item-"+id+ " :input").each(function(index, elm){
			    item     = $(elm).val(); 
			    text += item + ' / ';
			  });

	          var title_str = text.substring(0, text.length - 2);
	          var title_str = title_str.trim();			  

			  var item = self._getVariant( id, title_str );

			  if( item != null ){
				  if( item.length !== 0 ){

					  var exist = self._checkUpdateSingleItemExist( item );

					  if(exist == 'N'){
						  self._addToCart(item);
					  } 
				  }
			  }
			  
			  self.calculateAndUpdatePrice();	
			  
			  self.displayCart();

			  // Open Cart Modal
			  $( ".open-cart-btn" ).trigger( "click" );
	        });
		},

		_checkUpdateSingleItemExist: function( item ){
			var self = this;

			var cart = this._toJSONObject( this.storage.getItem( this.cartName ) );
		    var items = cart.items;
		    var isExist = 'N';
		    for( var i = 0; i < items.length; ++i ) {
		    	var id = items[i].id;
		    	
		    	if(id == item.id) {

		    		if(item.inventory_management != null){
			    		var total_qty = (parseInt(items[i].qty) + parseInt(item.qty));
			    		items[i].qty = (total_qty >= items[i].inventory_quantity) ? items[i].inventory_quantity : self._convertString(items[i].qty) + self._convertString( item.qty );
		    		} else{
		    			items[i].qty = self._convertString(items[i].qty) + self._convertString( item.qty );
		    		}
		    		isExist = 'Y';
		    	}
		    	
		    }
		    
		    if(isExist == 'Y'){
				cart.items = items;
		    	self.storage.setItem( self.cartName, self._toJSONString( cart ) );

		    	return 'Y';
			} else{
				return 'N';
			}
		},

		_getVariant: function( item_id, title ) {
			var self = this;

			var collection_items =  self.getConfigItem('collection_items');
			var products = collection_items['products'];

			for( var i = 0; i < products.length; ++i ) {
				var variants = products[i].variants;	
				var product_id = products[i].id;	
				var product_title = products[i].title;	
				var product_photo = products[i].image.src;
				var product_vendor = products[i].vendor;	

				for( var v = 0; v < variants.length; ++v ) {
					
					if( variants[v].title == title && product_id == item_id ){

						var item = {};
						item = {
							id: variants[v].id,
							product: product_title,
							photo: product_photo,
							price: variants[v].price,
							variants: variants[v].title,
							sku: variants[v].sku,
							weight: variants[v].weight + ' ' + variants[v].weight_unit,
							vendor: product_vendor,
							qty: 1,
							inventory_management: variants[v].inventory_management,
							inventory_quantity: variants[v].inventory_quantity
						};

						return item;
					} 

					if( title == '' && variants[v].product_id == item_id ) {

						var item = {};
						item = {
							id: variants[v].id,
							product: product_title,
							photo: product_photo,
							price: variants[v].price,
							variants: '',
							qty: 1,
							inventory_management: variants[v].inventory_management,
							inventory_quantity: variants[v].inventory_quantity
						};

						return item;
					}

				}

			}
		},

		// Updates the cart
		
		updateCart: function() {
		  var self = this;

		  if( self.$updateCartBtn.length ) {
			self.$updateCartBtn.on( "click", function() {
				var $rows = self.$formCart.find( ".cart-item" );
				var cart = self.storage.getItem( self.cartName );
				var shippingRates = self.storage.getItem( self.shippingRates );
				var total = self.storage.getItem( self.total );
	
				var updatedTotal = 0;
				var totalQty = 0;
				var updatedCart = {};
				updatedCart.items = [];
				
				$rows.each(function() {
					var $row = $( this );
					var pid = $.trim( $row.find( ".cart-id" ).text() );
					var pname = $.trim( $row.find( ".cart-title" ).text() );
					var photo = $.trim( $row.find( ".cart-photo" ).attr('src') );
					var pqty = self._convertString( $row.find( ".cart-qty" ).val() );
					var pprice = self._convertString( self._extractPrice( $row.find( ".cart-price" ) ) );
					var variants = $.trim( $row.find( ".cart-variants" ).text() );
					var inventory_management = $.trim( $row.find( "input[name=inventory_management]" ).val() );
					var inventory_quantity = $.trim( $row.find( "input[name=inventory_quantity]" ).val() );
					var sku = $.trim( $row.find( "input[name=sku]" ).val() );
					var vendor = $.trim( $row.find( "input[name=vendor]" ).val() );
					var weight = $.trim( $row.find( "input[name=weight]" ).val() );

					//$('.cart-error-msg'+pid).text('');
					//$('.cart-error-msg'+pid).hide();
				
					if(inventory_management == 'null'){
					} else{
						if(pqty > inventory_quantity){
							$('.cart-error-msg').html('Value must not exceed more than quantity');
							$('.cart-error-msg').show().delay(8000).fadeOut();
							pqty = inventory_quantity;
						} else{
							$('.cart-error-msg').html('');
							$('.cart-error-msg').hide();
						}
					}

					var cartObj = {
						id: pid,
						inventory_management: inventory_management,
						inventory_quantity: inventory_quantity,
						sku: sku,
						vendor: vendor,
						weight: weight,
						product: pname,
						photo: photo,
						price: pprice,
						variants: variants,
						qty: pqty
					};

					updatedCart.items.push( cartObj );
					
					var subTotal = pqty * pprice;
					updatedTotal += subTotal;
					totalQty += pqty;
				});

				self.storage.setItem( self.total, self._convertNumber( updatedTotal ) );
				self.storage.setItem( self.cartName, self._toJSONString( updatedCart ) );
				
				self.displayCart();
			});
		  }

		},

		// Handle Checkout 
		handleCheckout: function(){
			var self = this;

			$('.checkout-btn').on( "click", function() {	

				$('.shopify-loader').show();	

				var cart = self._toJSONObject( self.storage.getItem( 'shopify-cart' ) );

				var cart_items = cart.items;
				var baseUrl = self.$shopUrl + '/cart';
				var variantPath = [];

				for( var i = 0; i < cart_items.length; ++i ) {
					variantPath.push([cart_items[i].id, cart_items[i].qty]);
				}

				var variantOutput = encodeURIComponent(JSON.stringify(variantPath));
				var fullUrl = baseUrl + '?p=' + variantOutput;

				setTimeout(function () { 
					self._emptyCart();
		    		$('.shopify-loader').hide();
		    		window.location.replace(fullUrl); 
		    	}, 5000); 

		  });
		},

		_addToCart: function( values ) {
			var cart = this.storage.getItem( this.cartName );

			var cartObject = this._toJSONObject( cart );
			var cartCopy = cartObject;
			var items = cartCopy.items;
			items.push( values );
			
			this.storage.setItem( this.cartName, this._toJSONString( cartCopy ) );
		},

		// Empties the cart by calling the _emptyCart() method
		// @see $.Shop._emptyCart()
		
		emptyCart: function() {
			var self = this;
			if( self.$emptyCartBtn.length ) {
				self.$emptyCartBtn.on( "click", function() {
					self._emptyCart();
				});
			}
		},

		// Empties the session storage
		_emptyCart: function() {
			this.storage.clear();

			$('.shopping-cart tbody').html('');
		},
		
		/* Format a number by decimal places
		 * @param num Number the number to be formatted
		 * @param places Number the decimal places
		 * @returns n Number the formatted number
		 */
		_formatNumber: function( num, places ) {
			var n = num.toFixed( places );
			return n;
		},
		
		/* Extract the numeric portion from a string
		 * @param element Object the jQuery element that contains the relevant string
		 * @returns price String the numeric string
		 */
		_extractPrice: function( element ) {
			var self = this;
			var text = element.text();
			var price = text.replace( self.currencyString, "" ).replace( " ", "" );
			return price;
		},
		
		/* Converts a numeric string into a number
		 * @param numStr String the numeric string to be converted
		 * @returns num Number the number
		 */
		_convertString: function( numStr ) {
			var num;
			if( /^[-+]?[0-9]+\.[0-9]+$/.test( numStr ) ) {
				num = parseFloat( numStr );
			} else if( /^\d+$/.test( numStr ) ) {
				num = parseInt( numStr, 10 );
			} else {
				num = Number( numStr );
			}
			
			if( !isNaN( num ) ) {
				return num;
			} else {
				console.warn( numStr + " cannot be converted into a number" );
				return false;
			}
		},
		
		/* Converts a number to a string
		 * @param n Number the number to be converted
		 * @returns str String the string returned
		 */
		_convertNumber: function( n ) {
			var str = n.toString();
			return str;
		},
		
		/* Converts a JSON string to a JavaScript object
		 * @param str String the JSON string
		 * @returns obj Object the JavaScript object
		 */
		_toJSONObject: function( str ) {
			var obj = JSON.parse( str );
			return obj;
		},
		
		/* Converts a JavaScript object to a JSON string
		 * @param obj Object the JavaScript object
		 * @returns str String the JSON string
		 */
		_toJSONString: function( obj ) {
			var str = JSON.stringify( obj );
			return str;
		},
		
		
		/* Add an object to the cart as a JSON string
		 * @param values Object the object to be added to the cart
		 * @returns void
		 */
		_addToCart: function( values ) {
			var cart = this.storage.getItem( this.cartName );
			
			var cartObject = this._toJSONObject( cart );
			var cartCopy = cartObject;
			var items = cartCopy.items;
			items.push( values );
			
			this.storage.setItem( this.cartName, this._toJSONString( cartCopy ) );
		}

	};

	$(function() {
		if ( $( ".section-shop-page" ).length ) {
			var shop = new $.Shop( ".shop-page" );
		}
	});

})( jQuery );