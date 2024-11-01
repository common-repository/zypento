import { zypentoAjaxRequest } from "../general/ajaxRequest";

(function( $ ) {
	
	'use strict';
	
	$(function () {

        console.log( zypentoAdminVariables );

        $('.zypento-admin-setting-action-toggle').on('click', function(){

            var value = ( 'no' === $(this).attr('data-zyp-status') ) ? 'yes' : 'no';

            $(this).attr('data-zyp-status', value);

        });

        $('.zypento-admin-settings-action-button').on('click', function(){

            var settings = {};

            $('.zypento-admin-settings-overlay').css({'display':'block'});

            $('.zypento-admin-settings-message').text('').attr('data-zypento-type', '');

            $('.zypento-admin-settings-content').find('.zypento-admin-setting-container').each(function(){

                var key   = $(this).find('.zypento-admin-setting-action-toggle').attr('data-zyp-setting');
                var value = $(this).find('.zypento-admin-setting-action-toggle').attr('data-zyp-status');

                key = key.replace(/-/g, "_");

                settings[key] = value;

            });

            settings = JSON.stringify( settings );

            zypentoAjaxRequest(
                {
                    'url' : zypentoAdminVariables.api.admin.settings,
                    'sendData' : {
                        'action' : 'enabled-features',
                        'value'  : settings,
                    }
                },
                function(jsonData){
                    
                    if( 
                        Object.prototype.hasOwnProperty.call(jsonData, "result") &&
                        jsonData.result
                    ){

                        $('.zypento-admin-settings-message').text(zypentoAdminVariables.labels.settingsSuccess).attr('data-zypento-type', 'success');
                        $('.zypento-admin-settings-overlay').css({'display':'none'});
                        
                    }else{
                        var zypError = zypentoAdminVariables.labels.error;

                        if (
                            Object.prototype.hasOwnProperty.call(jsonData, "data") &&
                            Object.prototype.hasOwnProperty.call(jsonData.data, "error") &&
                            Object.prototype.hasOwnProperty.call(jsonData.data.error, "reason") &&
                            jsonData.data.error.reason
                        ) {
                            zypError = jsonData.data.error.reason;
                        }

                        $('.zypento-admin-settings-message').text(zypError).attr('data-zypento-type', 'error');
                        $('.zypento-admin-settings-overlay').css({'display':'none'});
                    }

                },
                function(){
                    $('.zypento-admin-settings-message').text(zypentoAdminVariables.labels.error).attr('data-zypento-type', 'error');
                    $('.zypento-admin-settings-overlay').css({'display':'none'});
                }
            );   

            console.log( settings );

        });

    });


})( jQuery );