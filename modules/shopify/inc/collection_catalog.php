<?php
$collections_catalog = $shop->getCollectionCatalog( $collection_id );

$list               = '';
$tagslist_array     = [];
$vendorlist_array     = [];
foreach($collections_catalog['products'] as $item){
	$id              = $item['id'];
	$title           = $item['title'];
	$body_html       = $item['body_html'];
	$vendor          = $item['vendor'];
	$product_type    = $item['product_type'];
	$created_at      = $item['created_at'];
	$handle          = $item['handle'];
	$updated_at      = $item['updated_at'];
	$published_at    = $item['published_at'];
	$template_suffix = $item['template_suffix'];
	$published_scope = $item['published_scope'];
	$tags            = $item['tags'];
	$variants        = $item['variants'];
	$options         = $item['options'];
	$images          = $item['images'];
	$photo_path      = $item['image']['src'];
	$tags_array      = explode(',', $tags);
	$tagstring_array = [];
	$title_url       = str_replace('+','-',urlencode(strtolower($title)));
	$full_url        = '/shop/product/'.$title_url.'/'.$id;

	$hash_url        = urlencode(strtolower($title));
	$price           = isset($variants[0]) ? '$'.$variants[0]['price'] : '';

	// Insert all tags into the tags array
	if(is_array($tags_array)){
		foreach($tags_array as $item){
			$item = trim($item);
			if(preg_match('/\s/',$item)){
				$item = str_replace(' ','-', $item);
			}

			array_push($tagslist_array, $item);
			array_push($tagstring_array, $item);
		}
	}
	$tags_classes     = implode(' ', array_unique($tagstring_array));

	$tags_string     = implode(',', array_unique($tagstring_array));

	array_push($vendorlist_array, $vendor);
	$vendor_string   = $vendor;

	$option_list = '';
	$count = 0;


	foreach($options as $option){
		
		$option_values = $option['values'];
		$name          = $option['name'];
		$position	   = $option['position'];

		if(isset($option_values)) {
			
			if($name == 'Title'){
				continue;
			}

			$option_list .= '<div class="col-md-12">
							   <p>
								<select name="'.$name.'" style="color:#000;width:100%;" class="option-'.$count.'-'.$id.' option-dropdown option-dropdown-'.$id.'" data-id="'.$id.'">
								<option value="" data-val="" data-length="" data-key="" data-id="">--Select--</option>
								';

			foreach($option_values as $option_key => $option_value){
				$option_list .= '<option value="'. $option_value.'" data-val="'. $option_value.'" data-length="'.count($options).'" data-key="'.$count.'" data-id="'.$id.'">'.ucwords($option_value).'</option>';
			}

			$option_list .= '</select></p>
			                 </div>';
		}
	$count++;
	}

  $list .= '<div class="col-xs-12 col-sm-6 col-lg-4 clm collection_catalog_item '.strtolower($product_type).'-type collection-item-'.$id.' '.$tags_classes.'" data-tags="'.$tags_string.'" data-brand="'.$vendor_string.'" data-id="'.$id.'">
	          <div class="grid__col collection_catalog_item__block">
	            <figure class="grid__figure discover-heading text-center">
	            	<div style="background-image: url('.$photo_path.');" class="grid__figure__img collection-photo"></div>
	                <h5 class="collection_catalog_item--title">'.$title.'</h5>
	                <p class="collection_catalog_item--price collection_catalog_item--price-'.$id.'">'.$price.'</p>
	                <div class="collection_catalog_item__option_list">
	                	'.$option_list.'
	                </div>
	                <div class="collection_catalog_item__btn_holder">
	                	<span class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
	                		<a href="#" class="btn view-details btn--outline product-list-btn" data-handle="'.$handle.'">View Details</a>
	                	</span>
	                	<span class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
	                		<a href="javascript:;" class="disabled btn btn--bg btn--bg-red list-add-cart-btn addtocart product-list-btn product-list-btn-'.$id.'" data-id="'.$id.'">Add to Cart</a>
	                	</span>
	                </div>
	            </figure>
	          </div>
	        </div>';
}
// END FOREACH

// START FILTER BY SEARCH
$product_type_list = '';
if(!empty($tagslist_array)){
	$tagslist_array = array_unique($tagslist_array);
	$product_type_list = '';
	foreach($tagslist_array as $item){
		if($item){
		$tag_class = str_replace(' ','-', $item) .'-item';	
		$tag_class = str_replace('\'','-', $tag_class);
		$product_type_list .= '<li class="custom-multi-select__item '. $tag_class .' tags-class">
								  <label class="custom-checkbox">
									<input type="checkbox" name="sub-category[]" value="'.str_replace(' ','-', $item).'" class="custom-checkbox_input tags-checkbox">
									<span class="custom-checkbox_icon"></span>
									<span class="custom-checkbox_text">'.ucwords($item).'</span>
								  </label>
						       </li>';
	    }
	}
}
 
$vendor_type_list = '';
if(!empty($vendorlist_array)){
	$vendorlist_array = array_unique($vendorlist_array);
	$vendor_type_list = '';
	foreach($vendorlist_array as $item){
		
		if($item){
		$vendor_type_list .= '<li class="custom-multi-select__item">
								  <label class="custom-checkbox">
									<input type="checkbox" name="sub-category[]" value="'.str_replace(' ','-', $item).'" class="custom-checkbox_input brands-checkbox">
									<span class="custom-checkbox_icon"></span>
									<span class="custom-checkbox_text">'.ucwords($item).'</span>
								  </label>
						       </li>';
	    }
	}
}

// FILTER BY COLLECTION
$collection_list = '';
foreach($collection['items'] as $items) {
	$collection_list .= '<li class="custom-select__item" data-value="'.$items['collection_url'].'" data-label="'.$items['collection_title'].'">'.$items['collection_title'].'</li>';
}


if(empty($collections_catalog['products'])){
$collections_view = '<div class="grid ctr section-shop-page">
							<div class="container-fluid collection-product-list">
                                <div class="row">
									<div class="breadcrumbs">
	                            	  <a href="/shop" class="breadcrumbs__shop">Shop</a> 
	                            	  <i class="fa fa-angle-right"></i> 
	                            	  <span class="breadcrumbs__main_title">'.$collection_title.'</span>
	                            	</div>
                                </div>
                                <div class="row collection-product-list__wrapper">
                                	<aside class="col-xs-12 col-sm-12 col-md-2">
                                	<br>
										<h3>Refine search</h3>
										<div class="form-group">
										  <div>
										    <label for="category">Collections</label>
										  </div>
										  <div class="custom-select">
										    <span class="custom-select__icon"></span>
										    <span class="custom-select__label">'.$collection_title.'</span>
										    <ul class="custom-select__list">
										      '.$collection_list.'
										    </ul>
										    <input type="hidden" name="category" value="'.$collection_id.'" id="category-list" data-append-to="#sub-categories-list">
										  </div>
										</div>
                                	</aside>
                                	<section class="col-xs-12 col-sm-12 col-md-10">
                                    	<div class="well" style="margin-top:30px;">
                                    		There are no products with the current collection.
                                    	</div>
                                    </section>
                                </div>
                            </div>
                        </div>';
} else{
$collections_view = '<div class="grid ctr section-shop-page">
							<div class="container-fluid collection-product-list">
                                <div class="row">
									<div class="breadcrumbs">
	                            	  <a href="/shop" class="breadcrumbs__shop">Shop</a> 
	                            	  <i class="fa fa-angle-right"></i> 
	                            	  <span class="breadcrumbs__main_title">'.$collection_title.'</span>
	                            	</div>
                                </div>
                                <div class="row collection-product-list__wrapper">
                                	<aside class="col-xs-12 col-sm-12 col-md-2">
                                	<br>
										<h3>Refine search</h3>
										<div class="form-group">
										  <div>
										    <label for="category">Collections</label>
										  </div>
										  <div class="custom-select">
										    <span class="custom-select__icon"></span>
										    <span class="custom-select__label">'.$collection_title.'</span>
										    <ul class="custom-select__list">
										      '.$collection_list.'
										    </ul>
										    <input type="hidden" name="category" value="'.$collection_id.'" id="category-list" data-append-to="#sub-categories-list">
										  </div>
										</div>
										<div class="form-group">
										  <div>
										    <label for="category">Brands</label>
										  </div>
										  <div class="custom-multi-select">
										    <ul class="custom-multi-select__list" id="sub-categories-list">
										      '.$vendor_type_list.'
										    </ul>
										  </div>
										  <input type="hidden" name="selected-sub-category" id="selected-sub-category" value="27">
										</div>
										<div class="form-group">
										  <div>
										    <label for="category">Tags</label>
										  </div>
										  <div class="custom-multi-select">
										    <ul class="custom-multi-select__list" id="sub-categories-list">
										      '.$product_type_list.'
										    </ul>
										  </div>
										  <input type="hidden" name="selected-sub-category" id="selected-sub-category" value="27">
										</div>
										<div class="form-group" style="display:none;">
										  <button class="btn btn--bg btn--bg-red list-add-cart-btn refine-search product-list-btn">Refine Search</button>
										</div>
                                	</aside>
                                	<section class="col-xs-12 col-sm-12 col-md-10">
                                    	'.$list.'
                                    </section>
                                </div>
                            </div>
                            <div class="container-fluid product-detail">
                            	<div class="breadcrumbs">
	                            		<a href="/shop" class="breadcrumbs__shop">Shop</a> 
	                            		<i class="fa fa-angle-right"></i> 
	                            		<a href="javascript:;" class="breadcrumbs__shop back-btn">'.$collection_title.'</a>
	                            		<i class="fa fa-angle-right"></i> 
	                            		<span class="breadcrumbs__title"></span>
	                            	</div>
                            	<div class="row product-detail__block">
                                    <div>
							          <div class="flex-viewport">
							            <div class="col-md-4 col-sm-12 col-xs-12">
							            	<img src="" class="grid__figure__img product-photo"/>
							            </div>
							            <div class="col-md-8 col-sm-12 col-xs-12 product-content">
							              <h1 class="product-title">title</h1>
							              <div class="stock-label"></div>
							              <div class="product-price">price</div>
							              <div class="product-sku" style="display:none;"></div>
							              <div class="product-availability" style="display:none;"></div>
							              <div class="product-options"></div>
													  <div class="product-body">body html</div>
													  <div class="product-tags" style="display:none;"></div>  
													  <div class="product-vendor" style="display:none;"></div>
													  <div class="product-weight" style="display:none;"></div>
							              <div class="text-left">
							              	<div class="quantity-description"><input type="number" value="" class="product-detail-cart-qty" min="1"/></div>
							              	<a href="javascript:;" class="btn btn--bg btn--bg-red product-detail-btn product-add-cart-btn disabled">
							              		Add to Cart
							              	</a>
							              </div>
							          </div>
							      </div>
                                </div>
                            </div>
                        </div>';
}

$jsVars['collection_items'] = $collections_catalog;

$tags_arr['mod_view'] .= $collections_view;
$tags_arr['heading']  = $collection_title;

require_once "includes/modal.php";
?>