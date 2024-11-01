/* global zypentoAdminVariables, jQuery */
export function zypentoAjaxRequest(data, onSuccess, onFailure){

    var nonce = '';
    var wpRestNonce = '';

    if ( 'undefined' !== typeof zypentoAdminVariables ) {

        if ( Object.prototype.hasOwnProperty.call(zypentoAdminVariables, 'nonce') ) {
            nonce = zypentoAdminVariables.nonce;
        }

        if ( Object.prototype.hasOwnProperty.call(zypentoAdminVariables, 'wpRestNonce') ) {
            wpRestNonce = zypentoAdminVariables.wpRestNonce;
        }

    }

    if ( 'undefined' !== typeof zypentoBlockVariables ) {

        if ( Object.prototype.hasOwnProperty.call(zypentoBlockVariables, 'nonce') ) {
            nonce = zypentoBlockVariables.nonce;
        }

        if ( Object.prototype.hasOwnProperty.call(zypentoBlockVariables, 'wpRestNonce') ) {
            wpRestNonce = zypentoBlockVariables.wpRestNonce;
        }

    }

    if ( 'undefined' !== typeof zypentoJsVariables ) {

        if ( Object.prototype.hasOwnProperty.call(zypentoJsVariables, 'nonce') ) {
            nonce = zypentoJsVariables.nonce;
        }

        if ( Object.prototype.hasOwnProperty.call(zypentoJsVariables, 'wpRestNonce') ) {
            wpRestNonce = zypentoJsVariables.wpRestNonce;
        }

    }

    data.sendData['nonce'] = nonce;
    console.log(data);

    jQuery.ajax({

        type: 'POST',
        url: data.url,
        data: data.sendData,
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-WP-Nonce', wpRestNonce);
        },
        timeout: data.timeout,
        success: function(jsonData) { console.log(jsonData);

            onSuccess(jsonData);

        },
        error: function(xhr, status, error) { console.log( xhr, status, error );

            onFailure(xhr, status, error);

        },

    });

}
