import { zypentoAjaxRequest } from "../../general/ajaxRequest";

(function( $ ) {
	
	'use strict';
	
	$(function () {

        if (
            ! Object.prototype.hasOwnProperty.call(zypentoJsVariables, "features") ||
            ! Object.prototype.hasOwnProperty.call(zypentoJsVariables.features, "saveLater")
        ) {
            return;
        }

        console.log('saveLater');

        function zypentoHideLaterMessage(){

            $('.zypento-save-for-later-message-content').text('');
            $('.zypento-save-for-later-message').attr('data-zypento-type','').css({'display':'none'});

        }

        function zypentoShowLaterMessage( message ){

            $('.zypento-save-for-later-message-content').text(message);
            $('.zypento-save-for-later-message').attr('data-zypento-type','error').css({'display':'block'});

        }

        function zypentoLaterSetup(){

            if ( 
                Object.prototype.hasOwnProperty.call(zypentoJsVariables, 'userLoggedIn') &&
                zypentoJsVariables.userLoggedIn
            ) {

                var zypentoLaterElements = $(".zypento-save-for-later-panel").length;
                var zypentoLaterIndex    = 0;

                $('.product-quantity').css({'position':'relative'});
                $('.product-quantity .quantity').append('<p class="zypento-save-for-later-link">Save For Later</p>');

                if ( ! zypentoLaterElements ) {
                    return;
                }

                if ( zypentoLaterElements ) {
                    if ( zypentoLaterElements > 1 ) {
                        $('.zypento-save-for-later-display-button[data-zyp-button-type="next"]').attr('data-zyp-enabled', '');
                    }
                    $('.zypento-save-for-later-display').css({'display':'block'});
                }

                var zypentoLaterWidth = $('.zypento-save-for-later-content').width();

                console.log( 'line#46', zypentoLaterWidth, zypentoLaterElements );

                $('.zypento-save-for-later-panel').css({'width':zypentoLaterWidth});

                $('.zypento-save-for-later-belt').css({'width': (zypentoLaterWidth * zypentoLaterElements)});

                $('.zypento-save-for-later-display').on('click', '.zypento-save-for-later-display-button', function(){

                    if ( zypentoLaterElements < 2 ) {
                        return;
                    }

                    var type = $(this).attr('data-zyp-button-type');
        
                    console.log( 'zypentoLaterIndex', zypentoLaterIndex );
        
                    if ( 'prev' === type ) {
                        zypentoLaterIndex--;
                        $('.zypento-save-for-later-display-button[data-zyp-button-type="next"]').attr('data-zyp-enabled', '');
                    }
        
                    if ( 'next' === type ) {
                        zypentoLaterIndex++;
                        $('.zypento-save-for-later-display-button[data-zyp-button-type="prev"]').attr('data-zyp-enabled', '');
                    }
        
                    if ( zypentoLaterIndex < 0 ) {
                        zypentoLaterIndex = 0;
                    }
        
                    if ( zypentoLaterIndex >= zypentoLaterElements ) {
                        zypentoLaterIndex = zypentoLaterElements - 1;
                    }
        
                    var zypentoMargin = zypentoLaterIndex * zypentoLaterWidth;
        
                    console.log( 'line#78', zypentoLaterIndex,zypentoMargin );
        
                    if ( 'prev' === type && zypentoLaterIndex <= 0 ) {
                        $(this).attr('data-zyp-enabled', 'no');
                    }
        
                    if ( 'next' === type && zypentoLaterIndex >= (zypentoLaterElements - 1) ) {
                        $(this).attr('data-zyp-enabled', 'no');
                    }
        
                    $('.zypento-save-for-later-belt').animate(
                        {
                            'margin-left': '-' + zypentoMargin + 'px',
                        },
                        500,
                        function(){
        
                        }
                    );
        
                });

                $('.zypento-save-for-later-display').on('click', '.zypento-saved-later-delete', function(){

                    zypentoHideLaterMessage();

                    var id = $(this).closest('.zypento-saved-later-item').attr('data-zyp-id');
                    console.log( 'line#105', id );

                    var $form = $(this).closest('.woocommerce').find( 'form' );

                    block( $form );
                    block( $( 'div.cart_totals' ) );

                    zypentoAjaxRequest(
                        {
                            'url' : zypentoJsVariables.api.woo.saveLater,
                            'sendData' : {
                                'action' : 'remove',
                                'value'  : JSON.stringify({'id':id}),
                            }
                        },
                        function(jsonData){
                            
                            if( 
                                Object.prototype.hasOwnProperty.call(jsonData, "result") &&
                                jsonData.result
                            ){
        
                                window.location.reload();
        
                            }else{
                                zypentoShowLaterMessage( zypentoJsVariables.labels.error );
                                unblock( $form );
                                unblock( $( 'div.cart_totals' ) );
                            }
        
                        },
                        function(){
                            zypentoShowLaterMessage( zypentoJsVariables.labels.error );
                            unblock( $form );
                            unblock( $( 'div.cart_totals' ) );
                        }
                    );

                });

                $('.zypento-save-for-later-display').on('click', '.zypento-saved-later-move', function(){

                    zypentoHideLaterMessage();

                    var id      = $(this).closest('.zypento-saved-later-item').attr('data-zyp-id');
                    var product = $(this).closest('.zypento-saved-later-item').attr('data-zyp-product-id');
                    console.log( 'line#151', id, product );

                    var $form = $(this).closest('.woocommerce').find( 'form' );

                    block( $form );
                    block( $( 'div.cart_totals' ) );

                    zypentoAjaxRequest(
                        {
                            'url' : zypentoJsVariables.api.woo.saveLater,
                            'sendData' : {
                                'action' : 'move',
                                'value'  : JSON.stringify({'id':id, 'product':product}),
                            }
                        },
                        function(jsonData){
                            
                            if( 
                                Object.prototype.hasOwnProperty.call(jsonData, "result") &&
                                jsonData.result
                            ){
        
                                window.location.reload();
        
                            }else{
                                zypentoShowLaterMessage( zypentoJsVariables.labels.error );
                                unblock( $form );
                                unblock( $( 'div.cart_totals' ) );
                            }
        
                        },
                        function(){
                            zypentoShowLaterMessage( zypentoJsVariables.labels.error );
                            unblock( $form );
                            unblock( $( 'div.cart_totals' ) );
                        }
                    );

                });
    
            }

        }

        /**
         * Check if a node is blocked for processing.
         *
         * @param {JQuery Object} $node
         * @return {bool} True if the DOM Element is UI Blocked, false if not.
         */
        var is_blocked = function( $node ) {
            return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
        };

        /**
         * Block a node visually for processing.
         *
         * @param {JQuery Object} $node
         */
        var block = function( $node ) {
            if ( ! is_blocked( $node ) ) {
                $node.addClass( 'processing' ).block( {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                } );
            }
        };

        /**
         * Unblock a node after processing is complete.
         *
         * @param {JQuery Object} $node
         */
        var unblock = function( $node ) {
            $node.removeClass( 'processing' ).unblock();
        };

        /**
         * Removes duplicate notices.
         *
         * @param {JQuery Object} notices
         */
        var remove_duplicate_notices = function( notices ) {
            var seen = [];
            var new_notices = notices;

            notices.each( function( index ) {
                var text = $( this ).text();

                if ( 'undefined' === typeof seen[ text ] ) {
                    seen[ text ] = true;
                } else {
                    new_notices.splice( index, 1 );
                }
            } );

            return new_notices;
        };

        /**
         * Update the .woocommerce div with a string of html.
         *
         * @param {String} html_str The HTML string with which to replace the div.
         * @param {bool} preserve_notices Should notices be kept? False by default.
         */
        var update_wc_div = function( html_str, preserve_notices ) {
            var $html       = $.parseHTML( html_str );
            var $new_form   = $( '.woocommerce-cart-form', $html );
            var $new_totals = $( '.cart_totals', $html );
            var $notices    = remove_duplicate_notices( $( '.woocommerce-error, .woocommerce-message, .woocommerce-info', $html ) );

            // No form, cannot do this.
            if ( $( '.woocommerce-cart-form' ).length === 0 ) {
                window.location.reload();
                return;
            }

            // Remove errors
            if ( ! preserve_notices ) {
                $( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
            }

            if ( $new_form.length === 0 ) {
                // If the checkout is also displayed on this page, trigger reload instead.
                if ( $( '.woocommerce-checkout' ).length ) {
                    window.location.reload();
                    return;
                }

                // No items to display now! Replace all cart content.
                var $cart_html = $( '.cart-empty', $html ).closest( '.woocommerce' );
                $( '.woocommerce-cart-form__contents' ).closest( '.woocommerce' ).replaceWith( $cart_html );

                // Display errors
                if ( $notices.length > 0 ) {
                    show_notice( $notices );
                }

                // Notify plugins that the cart was emptied.
                $( document.body ).trigger( 'wc_cart_emptied' );
            } else {
                // If the checkout is also displayed on this page, trigger update event.
                if ( $( '.woocommerce-checkout' ).length ) {
                    $( document.body ).trigger( 'update_checkout' );
                }

                $( '.woocommerce-cart-form' ).replaceWith( $new_form );
                $( '.woocommerce-cart-form' ).find( ':input[name="update_cart"]' ).prop( 'disabled', true ).attr( 'aria-disabled', true );
                zypentoLaterSetup();

                if ( $notices.length > 0 ) {
                    show_notice( $notices );
                }

                update_cart_totals_div( $new_totals );
            }

            $( document.body ).trigger( 'updated_wc_div' );
        };

        /**
         * Update the .cart_totals div with a string of html.
         *
         * @param {String} html_str The HTML string with which to replace the div.
         */
        var update_cart_totals_div = function( html_str ) {
            $( '.cart_totals' ).replaceWith( html_str );
            $( document.body ).trigger( 'updated_cart_totals' );
        };

        /**
         * Shows new notices on the page.
         *
         * @param {Object} The Notice HTML Element in string or object form.
         */
        var show_notice = function( html_element, $target ) {
            if ( ! $target ) {
                $target = $( '.woocommerce-notices-wrapper:first' ) ||
                    $( '.cart-empty' ).closest( '.woocommerce' ) ||
                    $( '.woocommerce-cart-form' );
            }
            $target.prepend( html_element );
        };

        /*
        $('.woocommerce').on('click', '.zypento-saved-later-delete', function(){

            var $form = $(this).closest('.woocommerce').find( 'form' );

            block( $form );
			block( $( 'div.cart_totals' ) );
            
            zypentoAjaxRequest(
                {
                    'url' : zypentoJsVariables.api.woo.saveLater,
                    'sendData' : {
                        'action' : 'remove',
                        'value'  : JSON.stringify({'product':itemId, 'key':itemKey}),
                    }
                },
                function(jsonData){
                    
                    if( 
                        Object.prototype.hasOwnProperty.call(jsonData, "result") &&
                        jsonData.result
                    ){



                    }else{

                    }

                },
                function(){

                }
            );
            

        });
        */

        $('.woocommerce').on('click', '.zypento-save-for-later-link', function(){

            var item = $(this).closest('.woocommerce-cart-form__cart-item');
            var itemRemove = item.find('.product-remove>a');
            var itemLink = itemRemove.attr('href');
            var itemId = itemRemove.attr('data-product_id');
            var itemName = item.find('.product-name>a').text();
            var itemKey = itemLink.split('?remove_item=')[1].split('&_wpnonce')[0];

            console.log( 'line#386', itemLink, itemName, itemKey );

            var $form = $(this).closest( 'form' );

            block( $form );
            block( $( 'div.cart_totals' ) );

            zypentoAjaxRequest(
                {
                    'url' : zypentoJsVariables.api.woo.saveLater,
                    'sendData' : {
                        'action' : 'add',
                        'value'  : JSON.stringify({'product':itemId, 'key':itemKey}),
                    }
                },
                function(jsonData){
                    
                    if( 
                        Object.prototype.hasOwnProperty.call(jsonData, "result") &&
                        jsonData.result
                    ){



                        $.ajax( {
                            type:     'GET',
                            url:      itemLink,
                            dataType: 'html',
                            success:  function( response ) {
                                update_wc_div( response );
                            },
                            complete: function() {
                                unblock( $form );
                                unblock( $( 'div.cart_totals' ) );
                                $.scroll_to_notices( $( '[role="alert"]' ) );
                            }
                        } );

                    }else{
                        console.log('failed');
                        var zypError = `<div class="woocommerce-message" role="error">Failed to move the ${itemName}</div>`;
                        show_notice( zypError );
                    }

                },
                function(){
                    var zypError = `<div class="woocommerce-message" role="error">Failed to move the ${itemName}</div>`;
                    show_notice( zypError );
                }
            );

        });

        $('body').on('updated_wc_div', function(){

            zypentoLaterSetup();

        });

        zypentoLaterSetup();

    });


})( jQuery );
