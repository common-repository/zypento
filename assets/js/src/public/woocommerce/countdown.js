(function( $ ) {
	
	'use strict';
	
	$(function () {

        var zypTimer;

        console.log( $('#zypento-variable-timer').length );

        if (
            ! Object.prototype.hasOwnProperty.call(zypentoJsVariables, "features") ||
            ! Object.prototype.hasOwnProperty.call(zypentoJsVariables.features, "saleCountdownTimer")
        ) {
            return;
        }

        console.log('saleCountdownTimer');

        var zypType = $('#zypento-variable-timer').attr('data-zypento-type');

        function zypentoFindiFSale(variant) {

            var result = false;

            var productVariations = $('#zypento-variable-timer-actual').attr('data-variation-sale');
            productVariations = JSON.parse( productVariations ); console.log( productVariations );

            $.each(productVariations, function (key, val) {
                if ( key === variant ) {
                    result = val;
                }
            });

            /*
            productVariations.forEach(function (item, index) { console.log( item, index );
                if ( variant == item.variation_id ) {
                    result = item.is_in_stock;
                }
            });
            */

            return result;

        }

        $('.woocommerce-variation-add-to-cart .variation_id').on('change', function(){

            $('#zypento-variable-timer').css({'display':'none'});
            $('#zypento-variable-timer-actual').html('');

            if ( zypTimer ) {
                clearInterval(zypTimer);
            }

            var variant = $('.variation_id').val();

            if ( '' !== variant ) {

                var sale = zypentoFindiFSale( variant ); console.log( sale );
                if ( '' !== sale ) {

                    sale = ( sale * 1000 );

                    zypTimer = setInterval(function() {

                        // Get today's date and time
                        var now = new Date().getTime();
                          
                        // Find the distance between now and the count down date
                        var distance = sale - now;
                          
                        // Time calculations for days, hours, minutes and seconds
                        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                          
                        // Output the result in an element with id="demo"
                        var zypentoTimerHtml = `<span>${days}<span>Days</span></span><span>${hours}<span>Hours</span></span><span>${minutes}<span>Mins</span></span><span>${seconds}<span>Secs</span></span>`;
                        document.getElementById("zypento-variable-timer-actual").innerHTML = zypentoTimerHtml;
                        
                        // If the count down is over, write some text 
                        if (distance < 0) {
                          clearInterval(zypTimer);
                        }
                      }, 1000);

                    $('#zypento-variable-timer').css({'display':'block'});

                    
                }

            }

        });

        if ( 'simple' === zypType ) {

            var sale = $('#zypento-variable-timer-actual').attr('data-variation-sale');

            if ( '' !== sale ) {

                sale = ( sale * 1000 );

                zypTimer = setInterval(function() {
    
                        // Get today's date and time
                        var now = new Date().getTime();
                        
                        // Find the distance between now and the count down date
                        var distance = sale - now;
                        
                        // Time calculations for days, hours, minutes and seconds
                        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        
                        // Output the result in an element with id="demo"
                        var zypentoTimerHtml = `<span>${days}<span>Days</span></span><span>${hours}<span>Hours</span></span><span>${minutes}<span>Mins</span></span><span>${seconds}<span>Secs</span></span>`;
                        document.getElementById("zypento-variable-timer-actual").innerHTML = zypentoTimerHtml;
                        
                        // If the count down is over, write some text 
                        if (distance < 0) {
                        clearInterval(zypTimer);
                        }
                    }, 
                    1000
                ); 

            }   

        }

    });


})( jQuery );
