<?php 	

if ($page == $page_home->url) {
	$sponsors_view = <<<H
		
		<section class="section section--white-bg">
	        <div class="container">
	            <div class="row">
	                <div class="col-md-12">
	                    <header class="section__header text-center">
	                        <h2 class="section__heading section__heading--red">Our Sponsors</h2>
	                    </header>
	                </div>
	            </div>

	            <div class="row">
	                <figure class="col-xs-12 col-sm-8 col-md-4 text-center figure">
	                    <a href="https://www.westpac.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/westpac.png" class="figure__img"></a>
	                </figure>
	                <figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="http://www.aucklandcouncil.govt.nz" target="_blank" class="figure__link"><img src="/graphics/sponsors/aucklandcouncil.png" class="figure__img"></a>
	                </figure>
	                <figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="http://www.dcndrilling.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/dcndrilling.png" class="figure__img"></a>
	                </figure>
	                <figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="http://www.constructors.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/dominion.png" class="figure__img"></a>
	                </figure>
             		<figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="http://www.fujifilm.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/fujifilm.png" class="figure__img"></a>
	                </figure>
	                <figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="http://www.halcyonlights.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/halcyon.png" class="figure__img"></a>
	                </figure>
	                <figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="https://www.hirepool.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/hirepool.png" class="figure__img"></a>
	                </figure>
					<figure class="col-xs-12 col-sm-4 col-md-2 text-center figure" style="line-height: 90px">
	                    <a href="http://www.paknsave.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/paknsave.png" class="figure__img"></a>
	                    <a href="http://www.newworld.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/newworld.png" class="figure__img"></a>
	                </figure>
	                <figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="https://leaseplan.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/leaseplan.png" class="figure__img"></a>
	                </figure>
	                <figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="http://www.mansons.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/mansons.png" class="figure__img"></a>
	                </figure>
					<figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="http://www.newstalkzb.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/newstalk.png" class="figure__img"></a>
	                </figure>
	                <figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="http://www.douglas.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/douglas.png" class="figure__img"></a>
	                </figure> 
	                <figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="https://www.tvnz.co.nz/one-news" target="_blank" class="figure__link"><img src="/graphics/sponsors/1news.png" class="figure__img"></a>
	                </figure>
	                <figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="http://www.vodafone.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/vodafone.png" class="figure__img"></a>
	                </figure>
	                <figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="http://www.wickliffe.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/wickliffe.png" class="figure__img"></a>
	                </figure>
	                <figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="http://www.fujixerox.co.nz" target="_blank" class="figure__link"><img src="/graphics/sponsors/fujixerox.png" class="figure__img"></a>
	                </figure>
	                <figure class="col-xs-12 col-sm-4 col-md-2 text-center figure">
	                    <a href="http://www.tomahawk.co.nz/" target="_blank" class="figure__link"><img src="/graphics/sponsors/tomahawk.png" class="figure__img"></a>
	                </figure>
	            </div> 

	            <div class="row">
	                <div class="col-md-12 text-center">
	                   <span class="section__span">Would you like to become a sponsor? <a href="{$page_contact->full_url}" class="btn btn--outline section__span__btn">Get In Touch</a></span>
	                </div>
	            </div>           
	        </div>
	    </section>

H;
}

$tags_arr['sponsors-section'] = $sponsors_view;

 ?>