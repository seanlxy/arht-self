// General App
(function(w){

    w.App = function(){
        this.config = {
            serviceUrl:'/requests/service',
            loadedGalleries:{}
        };
    };

    w.App.prototype.init = function() {
        var ths = this;

        this.config = $.extend(true, this.config, jsVars);

        this.toggleNav('.toggle-snav');
        this.toggleElm('.toggle-elm');
        this.initNewsletterSignup('#news-signup-form');
        this.initPhotoswipe('[data-launch-gallery]');
        this.initShopifyCustomSelect('.custom-select');
        //this.matchElmHeight('.collection_catalog_item__block');
        this.togglePaymentCCs('input[name="payment-method"]');

        this.openTeamModal();

        setTimeout(function(){
            app.initTeamShuffle('#shuffle');
        }, 500);

        // Scroll Top Trigger

        $('.scroll-trigger').on('click', function(e){
            e.preventDefault();

            var scrollTo = $(window).height();
                scrollPoint = $(window).scrollTop();

            if( scrollPoint < scrollTo ) {
                $('html, body').delay(200).animate({ scrollTop: scrollTo }, { duration: 350 });
            }

        });

        this.initSlick('.main-slider__slick',{
            autoplay: true,
            prevArrow: '<span class="fa fa-angle-left prev">',
            nextArrow: '<span class="fa fa-angle-right next">',
            fade: true
        });

        if ($(window).width() >= 992) {
            var $header = $('#header');
            var $window = $(window).on('resize', function(){
               var height = $(this).height() - $header.height() -70;
               $('.main-slider__slick__slide').height(height);
               $('#slider-container').height(height - 150);
            }).trigger('resize'); 
        }

        $('#donate-btn').on('click', function(e){
            $(this).addClass('active');
            $('#donation-section').slideToggle('slow', function(){
                $('#donate-btn').toggleClass('active', $(this).is(':visible'));
            });
        }); 

        $('#accept_terms').on('click', function(){
            $('#terms_check').attr('checked', true);
        });

        $('#mobile-donate-btn').on('click', function(e){
            $(this).addClass('active');
            $('#donation-section').fadeIn('fast', function(){
                $('#mobile-donate-btn').toggleClass('active', $(this).is(':visible'));
            });
        }); 

        $('#mobile-btn').on('click', function(e){
            $(this).addClass('active');
            $('#main-menu').fadeIn('fast', function(){
                $('#mobile-btn').toggleClass('active', $(this).is(':visible'));
            });
        }); 

        $('.close-btn').on('click', function(e){
            $(this).parent().fadeOut('fast');
            $('#mobile-donate-btn').removeClass('active');
            // $('#main-menu').fadeOut('fast');
        }); 

        

        this.toggleDonationPricing();
        this.initAutofill();
    };

     w.App.prototype.initShopifyCustomSelect = function(elm) {
            var openedCls = 'custom-select--opened';
            $(document).on('click', elm, function(e){
                e.stopPropagation();
                var self = $(this);
                if( !self.hasClass('custom-select--disabled') ) {
                    $(elm).not(self).removeClass(openedCls);
                    if( !self.hasClass(openedCls) ) {
                        self.addClass(openedCls);
                    } else {
                        self.removeClass(openedCls);
                    }
                }
            }).on('click', elm+' .custom-select__item', function(e){
                e.stopPropagation();

                var self  = $(this),
                    value = self.data('value'),
                    label = self.data('label'),
                    parent = self.parents(elm);

                parent.find('.custom-select__item').removeClass('custom-select__item--selected');
                self.addClass('custom-select__item--selected');
                
                parent.find('input[type="hidden"]').val(value).trigger('change');
                parent.find('.custom-select__label').text(label);
                parent.removeClass(openedCls);

                var pathname   = window.location.pathname,
                    array      = pathname.split ('/'),
                    shop       = array[1];

                window.location.replace(window.location.protocol + "//" + window.location.host + '/' + shop + '/' + value);

            }).on('click', function(e){
                $(elm).removeClass(openedCls);
            });
    };

    w.App.prototype.toggleDonationPricing = function(){

        var donationAmtElmSel            = '.donation-radio__input',
            donationOtherAmtToggleElmSel = '#other-amount-checkbox',
            donationOtherAmtElmSel       = '#other-amount-input',
            inviCls                      = 'invisible';
            
        $(document).on('change', donationAmtElmSel, function(){
            var self = $(this),
                isChecked = self.is(':checked');

            if( isChecked ) {
                $(donationOtherAmtElmSel).addClass(inviCls);
                $(donationOtherAmtToggleElmSel).removeAttr('checked');
            }

        }).on('change', donationOtherAmtToggleElmSel, function(){
            var self      = $(this),
                isChecked = self.is(':checked'),
                donationOtherAmtElm = $(donationOtherAmtElmSel);

            if( isChecked ) {

                donationOtherAmtElm.removeClass(inviCls);
                $(donationAmtElmSel).removeAttr('checked');

            } else {

                donationOtherAmtElm.addClass(inviCls);

            }
        });



    };

    w.App.prototype.openTeamModal = function()
    {

        if($('.team-wrap').length){

            var openModal = $('.team a[data-team]'),
            modal = $('.modal.team-modal'),
            closeModal = $('.modal .fa-times');

            openModal.on('click',function(e){

                e.preventDefault();
                var key = $(this).data('team'),
                target = $('.team-member[data-team="'+key+'"]');

                target.fadeIn();
                modal.addClass('open');
                $('html').addClass('no-of');

            });

            closeModal.on('click',function(){

                modal.removeClass('open');
                $('html').removeClass('no-of');
                $('.team-member').fadeOut();

            });

        };

        if($('.team-modal').length){

            var modalNav = $('.team-nav');

            modalNav.on('click',function(e){

                e.preventDefault();

                var totalDiv = $('.team-modal .team-member').length,
                current = $('.team-modal .team-member:visible');
                currentIndex = current.index() + 1;

                current.hide();

                if($(this).hasClass('next'))
                {
                    if(currentIndex == totalDiv)
                    {
                        $('div.team-member:first').fadeIn();
                    }else{
                        current.next().fadeIn();
                    }

                }else{

                    if(currentIndex == 1)
                    {
                        $('div.team-member:last').fadeIn();
                    }else{
                        current.prev().fadeIn();
                    }
                }
                
            });
        }
    };

    w.App.prototype.initTeamShuffle = function(elm)
    {
        var jElm = $(elm);
        if(jElm.length)
        {
            jElm.shuffle({
                group:'all',
                itemSelector:'.team',
                speed:450
            });

            var shuffBtn = $('.filter-nav li a');

            shuffBtn.on('click', function(e) {
                
                e.preventDefault();

                jElm.shuffle( 'shuffle', $(this).attr('data-group') );

                shuffBtn.removeClass('active');
                $(this).addClass('active');
                
            });

        }
    };

    w.App.prototype.getVar = function(property, obj){
        if(obj.hasOwnProperty(property)) return obj[property];

        for(var prop in obj)
        {
            if(obj[prop].hasOwnProperty(property))
            {
                return obj[prop][property];
            }
        }
        
        return false;
    };


    w.App.prototype.getConfigItem = function(prop)
    {
        return this.getVar(prop, this.config);

    };

    w.App.prototype.toggleNav = function(elm){
        var jElm = $(elm);

        if( jElm.length )
        {
            jElm.on('click', function(e){
                e.preventDefault();

                var self = $(this),
                    target = self.next('.sub-menu');

                if( target.length )
                {
                    target.toggle();
                    self.toggleClass('active');
                    
                }

            });

        }
    };

    w.App.prototype.togglePaymentCCs = function(elm){
  
       $(document).on('click', elm, function(){

            var self = $(this),
                target = $(self.data('target')),
                hiddenCls = 'hidden';


            $('.icon-grp').addClass(hiddenCls);

            if( target.length )
            {
               target.removeClass(hiddenCls);
                
            }

        });
    };


    w.App.prototype.toggleElm = function(elm){
        var jElm = $(elm);

        if( jElm.length ) {
            jElm.on('click', function(e){
                e.preventDefault();

                var self = $(this),
                    targetSel = self.attr('href'),
                    target = $(targetSel),
                    activeCls = 'active';

                if( target.length ) {
                    
                    if( !self.hasClass(activeCls) ) {

                        target.addClass(activeCls);
                        $('[href="'+targetSel+'"]').addClass(activeCls);

                    } else {
                        target.removeClass(activeCls);
                        $('[href="'+targetSel+'"]').removeClass(activeCls);
                    }
                }
                
            });

        }
    };


    w.App.prototype.matchElmHeight = function(elm) {
        var jElm = (typeof elm == 'string') ? $(elm) : elm;

        if( jElm )
        {
            jElm.css('height','auto');

            var height = 0;

            jElm.each(function(i, el) {
                var jEl = $(el),
                elHeight = jEl.height();

                if( elHeight > height ) height = elHeight;
            });

            jElm.css('height', height);
        }
    };

    w.App.prototype.initNewsletterSignup = function(elm)
    {
        var jElm = $(elm);

        if(jElm.length)
        {

            var triggerBtn = jElm.find('#newsletter-submit');

            if(triggerBtn.length)
            {
                triggerBtn.on('click', function(e){
                    e.preventDefault();

                    var emailAddress =  $.trim(jElm.find('#signup-email').val()),
                    msg = '',
                    msgType = 'text-dange';


    
                    var msgHodler = jElm.find('.msg');

                    if(emailAddress)
                    {
                        var emailRegex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                        
                        if(emailRegex.test(emailAddress))
                        {
                            $.post(app.getConfigItem('serviceUrl'), 'action=sign-up&email='+emailAddress, function(response){
                              
                                if(response.msg)
                                {
                                    msgHodler.removeAttr('class').addClass('msg '+response.type).html(response.msg);
                                }


                                if(response.isValid)
                                {
                                    setTimeout(function(){
                                        msgHodler.removeClass(response.type).html('');
                                        jElm.find('#full-name').val('');
                                        jElm.find('#signup-email').val('');
                                        
                                    }, 5000);
                                }


                                return false;

                            }, 'json');
                        }
                        else
                        {
                            msg = 'Invalid email address provided.';
                        }
                    }
                    else
                    {
                        msg = 'Your email address is required.';

                    }



                    if(msg)
                    {
                        msgHodler.removeAttr('class').addClass('msg '+msgType).html(msg);
                    }

                });
            }
        }
    };

    w.App.prototype.initMap = function( canvas ) {
        canvas = document.getElementById( canvas );
        
        if( canvas ) {
        
            var mapStyles = [];

            if( mapStyles.length == 0 ) {
                $.get(app.getConfigItem('serviceUrl'), 'action=fetch-map-styles', function( styles ){
                    mapStyles = styles;

                    renderMap(canvas);

                }, 'json');
            }
            else {
                renderMap(canvas);
            }

            function renderMap( canvas ) {
                var map,
                    route,
                    markerIcon = new google.maps.MarkerImage( "/graphics/sprite.png", new google.maps.Size(16, 28), new google.maps.Point(433, 0) ),
                    gmMarkers  = [],
                    infoWindow;

                if( canvas ) {

                    $.get(app.getConfigItem('serviceUrl'), 'action=fetch-destination-map', function(response){

                        if( response ) {

                            infoWindow = new google.maps.InfoWindow({
                                content: ''
                            });


                            map = new google.maps.Map(canvas, {
                                center: {lat: 13.817738419751203, lng: 10.722125000000009},
                                zoom: 2,
                                // minZoom: 2,
                                styles:mapStyles,
                                scrollwheel:false,
                                draggable:(( $(window).width() > 991 ) ? true : false) 
                            });

                        

                            if( response.length > 0 ) {

                                var bounds = new google.maps.LatLngBounds();
                                
                                _.each(response, function( markerData ){
                                    var pos = new google.maps.LatLng(markerData.lat , markerData.lng);
                                    

                                    var marker = new google.maps.Marker({
                                        position: pos,
                                        map: map,
                                        title: markerData.title,
                                        ind: markerData.hIndex,
                                        icon:markerIcon,
                                        infoWindowContent:''
                                    });

                                    gmMarkers.push(marker);


                                    google.maps.event.addListener(marker, "click", function(){
                                        var ths = this;

                                        infoWindow.setContent('<h5><a href="'+markerData.uri+'">'+ths.title+'</a></h5>');
                                        infoWindow.open(map, marker);

                                    }); // Marker click


                                    // bounds.extend(pos);

                                });

                            }

                        }

                    }, 'json');
                }

            }

        }

    };

    w.App.prototype.initSlick = function(elm, opts) {
        
        var jElm = $(elm);

        if( jElm.length )
        {
            var defaults = {};

            opts = $.extend(true, defaults, opts);
            
            jElm.slick(opts);
        }
    };

    w.App.prototype.initPhotoswipe = function(elm) {

        if( app.getConfigItem('data').initGallery )
        {

            var template = app.getConfigItem('templates').galleryModal;
 
            if( $('.pswp').length == 0 )
            {
                $('body').append(template);

            }

            var options = {
                index: 0,
                shareEl:false,
                preload:[1,3],
                history:false,
                bgOpacity:0.9
            };

            function fetchGalleryPhotos( key, index, callback ) {
                if( !key ) return false;

                if( !app.config.loadedGalleries.hasOwnProperty(key) ) {

                    $.get(app.getConfigItem('serviceUrl'), 'action=fetch-gallery&key='+key, function( response ){
                        app.config.loadedGalleries[key] = response;


                        if(typeof callback === 'function') callback.call();
                    }, 'json');

                }
                else {
                    if(typeof callback === 'function') callback.call();
                }
                
            }


            function generateView( key, index ) {
                
                if( !key ) return false;

                var photos = app.config.loadedGalleries[key];

                if( photos.length > 0 ) {

                    if( index ) options.index = index;

                    var gallery = new PhotoSwipe( $('.pswp').get(0), PhotoSwipeUI_Default, photos, options);
                            
                    gallery.init();

                }
            }

            $('body').on('click', elm, function(e){
                e.preventDefault();

                var self = $(this),
                    key   = (self.is('li')) ? self.parents('ul').data('gallery') : self.data('launch-gallery'),
                    index = self.data('index');


                if( !self.hasClass('launch-gallery') ) {

                    fetchGalleryPhotos( key, index, function(){
                        generateView(key, index);
                    });
                    
                }
                else if( self.attr('href') && self.data('size') ) {

                    var galleryItems = self.parents('.gallery').find('.item a');
                    var galleryPhotos = [];
                    var index = self.parents('.item').index();

                    galleryItems.each(function(i, elm){
                        
                        var JElm = $(elm),
                        size = JElm.data('size').split('x'),
                        src = JElm.attr('href');

                        galleryPhotos.push({ src:src, w: size[0], h: size[1]})
                    });

                    options.index = index;

                    var gallery = new PhotoSwipe( $('.pswp').get(0), PhotoSwipeUI_Default, galleryPhotos, options);
                            
                    gallery.init();
                    
                }

            });

        }

         w.App.prototype.initAutofill = function() {

            // This example displays an address form, using the autocomplete feature
            // of the Google Places API to help users fill in the information.

            // This example requires the Places library. Include the libraries=places
            // parameter when you first load the API. For example:
            // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

            var placeSearch, autocomplete;
            var componentForm = {
                street_number: 'short_name',
                route: 'long_name',
                sublocality_level_1: 'long_name',
                locality: 'long_name',        
                postal_code: 'short_name'
            };

            function initAutocomplete() {
                // Create the autocomplete object, restricting the search to geographical
                // location types.
                autocomplete = new google.maps.places.Autocomplete(
                    /** @type {!HTMLInputElement} */(document.getElementById('address')),
                    {types: ['geocode']});

                // When the user selects an address from the dropdown, populate the address
                // fields in the form.
                autocomplete.addListener('place_changed', fillInAddress);
            }

            function fillInAddress() {
                // Get the place details from the autocomplete object.
                var place = autocomplete.getPlace();
                var fullAddress =[];
                for (var component in componentForm) {
                  document.getElementById(component).value = '';
                  document.getElementById(component).disabled = false;
                }

                // Get each component of the address from the place details
                // and fill the corresponding field on the form.
                for (var i = 0; i < place.address_components.length; i++) {
                  var addressType = place.address_components[i].types[0];
                    if (componentForm[addressType]) {
                       var val = place.address_components[i][componentForm[addressType]];
                       document.getElementById(addressType).value = val;
                    }

                    if (addressType == "street_number") {
                      fullAddress[0] = val;
                    } else if (addressType == "route") {
                      fullAddress[1] = val;
                    }
                }
                document.getElementById('address').value = fullAddress.join(" ");
            }

            // Bias the autocomplete object to the user's geographical location,
            // as supplied by the browser's 'navigator.geolocation' object.
            function geolocate() {
                if (navigator.geolocation) {
                  navigator.geolocation.getCurrentPosition(function(position) {
                    var geolocation = {
                      lat: position.coords.latitude,
                      lng: position.coords.longitude
                    };
                    var circle = new google.maps.Circle({
                      center: geolocation,
                      radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                  });
                }
            }


            $('#address').on('focus', function(){
                geolocate();
            })

            initAutocomplete();

         }

    };

}(window));
var app = new App();