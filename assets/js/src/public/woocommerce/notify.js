import { zypentoAjaxRequest } from "../../general/ajaxRequest";

(function( $ ) {
	
	'use strict';
	
	$(function () {

        //var productVariations = $('.variations_form cart').attr('data-product_variations');

        if (
            ! Object.prototype.hasOwnProperty.call(zypentoJsVariables, "features") ||
            ! Object.prototype.hasOwnProperty.call(zypentoJsVariables.features, "stockNotifier")
        ) {
            return;
        }

        console.log('stockNotifier');

        function zypentoResetNotify(){
            $('.zypento-notify-product-button-text').attr({'data-notify-status': '', 'data-notify-spinner': ''}).text('Notify Me');
        }

        function zypentoResetFormNotify(){
            $('.zypento-notify-product-form-button').attr({'data-notify-status': '', 'data-notify-spinner': ''}).text('Notify Me');
            $('.zypento-notify-product-form').attr({'data-bg':'yes'});
        }

        function zypentoFindStock(variant) {

            var result = false;

            var productVariations = $('.product').find('.variations_form').attr('data-product_variations');
            productVariations = JSON.parse( productVariations );

            productVariations.forEach(function (item, index) {
                if ( variant == item.variation_id ) {
                    result = item.is_in_stock;
                }
            });

            return result;

        }

        $('.woocommerce-variation-add-to-cart .variation_id').on('change', function(){

            var variant = $('.variation_id').val();

            if ( '' !== variant ) {

                $('.zypento-notify-product-form-message').attr('data-type', '').text('').css({'display':'none'});

                var loggedIn = $('.zypento-notify-product-container').attr('data-notify-member');

                if ( 'yes' === loggedIn ) {
                    zypentoResetNotify();
                }

                if ( '' === loggedIn ) {
                    zypentoResetFormNotify()
                }

                $('.zypento-notify-product-container').attr('data-notify-id', variant);

                var stock = zypentoFindStock( variant );
                if ( stock ) {
                    $('#zypento-notify-product').css({'display':'none'});
                } else {
                    var notifyStatus = $('#zypento-notify-product').attr('data-zypento-notify');
                    notifyStatus = JSON.parse( notifyStatus );
                    if( '' !== notifyStatus[variant] ){
                        if ( 'yes' === loggedIn ) {
                            $('.zypento-notify-product-button-text').attr('data-notify-status', 'enabled').text('Back InStock Notification Enabled');
                        }
                        if ( '' === loggedIn ) {
                            $('.zypento-notify-product-form-button').attr('data-notify-status', 'enabled').text('Back InStock Notification Enabled');
                            $('.zypento-notify-product-form').attr({'data-bg':''});
                        } 
                    }
                    $('#zypento-notify-product').css({'display':'block'});
                }

            }

        });

        $('.zypento-notify-product-button-text').on('click', function(){

            $(this).attr('data-notify-spinner', 'yes').html('<span class="zypento-notify-product-button-spinner"></span>Please wait...');

            var zypentoNotifyAction = 'add';
            if ( 'enabled' === $(this).attr('data-notify-status') ) {
                zypentoNotifyAction = 'remove';
            }

            var zypentoNotifyProduct = $(this).closest('.zypento-notify-product-container').attr('data-notify-id');

            zypentoAjaxRequest(
                {
                    'url' : zypentoJsVariables.api.woo.addNotification,
                    'sendData' : {
                        'action' : zypentoNotifyAction,
                        'value'  : JSON.stringify({'product':zypentoNotifyProduct}),
                    }
                },
                function(jsonData){
                    
                    if( 
                        Object.prototype.hasOwnProperty.call(jsonData, "result") &&
                        jsonData.result
                    ){
                        var notifyData = $('#zypento-notify-product').attr('data-zypento-notify');
                        var notifyType = '';
                        if( 0 !== notifyData ){
                            notifyData = JSON.parse( notifyData );
                            if ( 'object' === typeof notifyData && 0 < Object.keys( notifyData ).length ) {
                                notifyType = 'variable';
                            }
                        }

                        if( 'add' === zypentoNotifyAction ) {
                            $('.zypento-notify-product-button-text').attr({'data-notify-status': 'enabled', 'data-notify-spinner':''}).text('Back InStock Notification Enabled');
                            if( 'variable' === notifyType ){
                                notifyData[ zypentoNotifyProduct ] = 'enabled';
                                notifyData = JSON.stringify( notifyData );
                                $('#zypento-notify-product').attr('data-zypento-notify', notifyData);
                            }
                        }
                        if( 'remove' === zypentoNotifyAction ) {
                            $('.zypento-notify-product-button-text').attr({'data-notify-status': '', 'data-notify-spinner':''}).text('Notify Me');
                            if( 'variable' === notifyType ){
                                notifyData[ zypentoNotifyProduct ] = '';
                                notifyData = JSON.stringify( notifyData );
                                $('#zypento-notify-product').attr('data-zypento-notify', notifyData);
                            }
                        }                        
                    }else{
                        zypentoResetNotify();
                        $('.zypento-notify-product-button-message').text('Something went wrong. Please try again...').css({'display':'block'});
                    }

                },
                function(){
                    zypentoResetNotify();
                    $('.zypento-notify-product-button-message').text('Something went wrong. Please try again...').css({'display':'block'});
                }
            );

        });

        $('.zypento-notify-product-form-button').on('click', function(){

            if ( 'yes' === $(this).attr('data-notified') ) {
                return;
            }

            $('.zypento-notify-product-form-message').attr('data-type', '').text('').css({'display':'none'});

            $(this).attr('data-notify-spinner', 'yes').html('<span class="zypento-notify-product-button-spinner"></span>Please wait...');

            var zypentoName  = $('.zypento-notify-product-form-item input[name="zypento-notify-name"]').val();
            var zypentoEmail = $('.zypento-notify-product-form-item input[name="zypento-notify-email"]').val();

            if ( '' === zypentoName || '' === zypentoEmail ) {

                $('.zypento-notify-product-form-message').attr('data-type', 'error').text('Please enter both name and email.').css({'display':'block'});
                $('.zypento-notify-product-form-button').attr({'data-notify-spinner':''}).text('Notify Me');
                return;

            }

            

            var zypentoNotifyProduct = $(this).closest('.zypento-notify-product-container').attr('data-notify-id');
            //$('.zypento-notify-product-form-message').css({'display':'block'});

            zypentoAjaxRequest(
                {
                    'url' : zypentoJsVariables.api.woo.addNotification,
                    'sendData' : {
                        'action' : 'add',
                        'value'  : JSON.stringify({'product':zypentoNotifyProduct, 'email': zypentoEmail, 'name': zypentoName}),
                    }
                },
                function(jsonData){
                    
                    if( 
                        Object.prototype.hasOwnProperty.call(jsonData, "result") &&
                        jsonData.result
                    ){
                        var notifyData = $('#zypento-notify-product').attr('data-zypento-notify');
                        var notifyType = '';
                        if( 0 !== notifyData ){
                            notifyData = JSON.parse( notifyData );
                            if ( 'object' === typeof notifyData && 0 < Object.keys( notifyData ).length ) {
                                notifyType = 'variable';
                            }
                        }
                        $('.zypento-notify-product-form-button').attr({'data-notify-status': 'enabled', 'data-notify-spinner':'', 'data-notified':'yes'}).text('Back InStock Notification Enabled');
                        $('.zypento-notify-product-form').attr({'data-bg':''});
                        if( 'variable' === notifyType ){
                            notifyData[ zypentoNotifyProduct ] = 'enabled';
                            notifyData = JSON.stringify( notifyData );
                            $('#zypento-notify-product').attr('data-zypento-notify', notifyData);
                        }                    
                    }else{
                        zypentoResetFormNotify();
                        $('.zypento-notify-product-form-message').attr('data-type', 'error').text(jsonData.data.error.reason).css({'display':'block'});
                    }

                },
                function(){
                    zypentoResetFormNotify();
                    $('.zypento-notify-product-form-message').attr('data-type', 'error').text('Something went wrong. Please try again...').css({'display':'block'});
                }
            );            

        });

    });


})( jQuery );
