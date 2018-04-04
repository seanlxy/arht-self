<?php
$featured_cruises_view = '';


$cruises = fetch_all("SELECT c.`public_token`, `no_of_days`, `no_of_nights`, IF(c.`standard_price`, c.`standard_price`, 'POA') AS price_from,
	c.`is_featured`, c.`on_special`, pmd.`menu_label`, pmd.`title`, pmd.`url`, pmd.`full_url`, pmd.`thumb_photo`, pmd.`short_description`,
	cr.`code` AS currency_code, cr.`symbol` AS currency_symbol, dpmd.`menu_label` AS destination_menu_label
	FROM `cruise` c
	LEFT JOIN `page_meta_data` pmd
	ON(pmd.`id` = c.`page_meta_data_id`)
	LEFT JOIN `currency` cr
	ON(cr.`id` = c.`currency_id`)
	LEFT JOIN `destination` d
	ON(d.`id` = c.`destination_id`)
	LEFT JOIN `page_meta_data` dpmd
	ON(dpmd.`id` = d.`page_meta_data_id`)
	WHERE c.`is_featured` = 'Y'
	AND pmd.`status` = 'A'
	ORDER BY pmd.`rank`");

if( !empty($cruises) )
{
	$featured_cruises_view = '
	<div class="grid-wrapper">
	    <header>
	        <p class="h2 intro line"><span>Featured Expeditions</span></p>
	    </header>
	    <div class="grid">';


	foreach ($cruises as $cruise)
	{

		$thumb_photo = $cruise['thumb_photo'];
		$title       = ($cruise['title']) ? ' title="'.$cruise['title'].'"' : '';

		$cruise_public_token = $cruise['public_token'];

		$is_shortlisted = in_array($cruise_public_token, $shortlist);
		
		$compare_btn_cls   = ($is_shortlisted) ? 'minus' : 'plus';
		$compare_btn_title = ($is_shortlisted) ? 'Remove from compare list.' : 'Add to compare list.';
		$compare_btn_label = ($is_shortlisted) ? 'Remove' : 'Compare';

		$is_featured = ( $cruise['is_featured'] === 'Y' ) ? '<span class="feat">Featured Expedition</span>' : '';
		$on_special  = ( $cruise['on_special'] === 'Y' ) ? '<span class="spec"> <i class="glyphicons glyphicons-star"></i>Special</span>' : '';

		$featured_cruises_view .= '<div class="col">
            <figure style="background-image:url('.$thumb_photo.');"'.$title.'>
                '.$is_featured.$on_special.'
            </figure>
            <section style="background-image:url('.$thumb_photo.');">
                <div class="inner">
                    <h2>'.$cruise['menu_label'].'</h2>
                    <p class="detail">'.$cruise['no_of_days'].' days/'.$cruise['no_of_nights'].' nights<span>|</span>From '.$cruise['currency_code'].' '.$cruise['currency_symbol'].$cruise['price_from'].'</p>
                    <p class="detail">Location:<span>'.$cruise['destination_menu_label'].'</span></p>
                    <p>'.$cruise['short_description'].'</p>
                    <div>
                        <a href="'.$cruise['full_url'].'"'.$title.' class="btn white">MORE INFO</a>
                        <a href="#" class="comp'.(($is_shortlisted) ? ' added' : '').'" data-shortlist="'.$cruise_public_token.'" title="'.$compare_btn_title.'">
                        	<i class="glyphicons glyphicons-circle-'.$compare_btn_cls.'"></i> 
                        	<span>'.$compare_btn_label.'</span>
                        </a>
                    </div>
                </div>
            </section>
        </div>';
	}


	$featured_cruises_view .= '
		</div><!-- /.grid -->
	</div><!-- /.grid-wrapper(featured cruises) -->';

}

$tags_arr['featured_cruises'] = $featured_cruises_view;

?>