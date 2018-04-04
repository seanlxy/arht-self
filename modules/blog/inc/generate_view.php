<?php


if( !empty($posts_arr) )
{

	foreach ($posts_arr as $post)
	{

		$description = $post['description'];
		
		$posts_view .= '<article class="col-xs-12 blog-entry">
			'.((!$is_single) ? '<h2><a href="'.$pg_full_url.$post['full_url'].'" title="'.$post['title'].'">'.$post['heading'].'</a></h2>' : '').'
			<p class="author"><i class="fa fa-clock-o"></i> Posted by <a href="'.$pg_full_url.'/author/'.$post['author_url'].'">'.$post['author_name'].'</a> on '.$post['posted_on'].'</p>
			<div>'.$description.'</div>
		</article>';
	}


// 	if( $is_single )
// 	{
// 		$spost = $posts_arr[0];

// 		$author_intro = $spost['author_intro'];
// 		$author_img = ( is_file("{$rootfull}{$spost['author_photo']}") ) ? ' style="background-image:url('.$spost['author_photo'].')"' : '';

// 		$extra_view = '<div class="author-details">
// 	<div class="container">
// 		<div class="row">
// 			<div class="col-xs-12">
// 				<h3 class="sep">About the Author</h3>
// 			</div>
// 			<div class="col-xs-12 col-sm-2">
// 				<div class="img'.((!$author_img) ? ' ni' : '').'"'.$author_img.'></div>
// 			</div>
// 			<div class="col-xs-12 col-sm-10">
// 				<div class="info">
// 					<h4>'.$spost['author_name'].'</h4>
// 					<div>'.$author_intro.'</div>
// 					<p><a href="'.$pg_full_url.'/author/'.$post['author_url'].'">Read More articles by '.$spost['author_name'].' <i class="fa fa-arrow-right"></i></a></p>

// 				</div>
// 			</div>
// 		</div>
// 	</div>
// </div>';

// 	}

	
}



?>