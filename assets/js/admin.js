(()=>{var e={531:()=>{!function(e){"use strict";jQuery((function(){console.log(zypentoAdminVariables)}))}()}},t={};function n(o){var a=t[o];if(void 0!==a)return a.exports;var s=t[o]={exports:{}};return e[o](s,s.exports,n),s.exports}(()=>{"use strict";var e;n(531),(e=jQuery)((function(){console.log(zypentoAdminVariables),e(".zypento-admin-setting-action-toggle").on("click",(function(){var t="no"===e(this).attr("data-zyp-status")?"yes":"no";e(this).attr("data-zyp-status",t)})),e(".zypento-admin-settings-action-button").on("click",(function(){var t,n,o,a,s,r={};e(".zypento-admin-settings-overlay").css({display:"block"}),e(".zypento-admin-settings-message").text("").attr("data-zypento-type",""),e(".zypento-admin-settings-content").find(".zypento-admin-setting-container").each((function(){var t=e(this).find(".zypento-admin-setting-action-toggle").attr("data-zyp-setting"),n=e(this).find(".zypento-admin-setting-action-toggle").attr("data-zyp-status");t=t.replace(/-/g,"_"),r[t]=n})),r=JSON.stringify(r),t={url:zypentoAdminVariables.api.admin.settings,sendData:{action:"enabled-features",value:r}},n=function(t){if(Object.prototype.hasOwnProperty.call(t,"result")&&t.result)e(".zypento-admin-settings-message").text(zypentoAdminVariables.labels.settingsSuccess).attr("data-zypento-type","success"),e(".zypento-admin-settings-overlay").css({display:"none"});else{var n=zypentoAdminVariables.labels.error;Object.prototype.hasOwnProperty.call(t,"data")&&Object.prototype.hasOwnProperty.call(t.data,"error")&&Object.prototype.hasOwnProperty.call(t.data.error,"reason")&&t.data.error.reason&&(n=t.data.error.reason),e(".zypento-admin-settings-message").text(n).attr("data-zypento-type","error"),e(".zypento-admin-settings-overlay").css({display:"none"})}},o=function(){e(".zypento-admin-settings-message").text(zypentoAdminVariables.labels.error).attr("data-zypento-type","error"),e(".zypento-admin-settings-overlay").css({display:"none"})},a="",s="","undefined"!=typeof zypentoAdminVariables&&(Object.prototype.hasOwnProperty.call(zypentoAdminVariables,"nonce")&&(a=zypentoAdminVariables.nonce),Object.prototype.hasOwnProperty.call(zypentoAdminVariables,"wpRestNonce")&&(s=zypentoAdminVariables.wpRestNonce)),"undefined"!=typeof zypentoBlockVariables&&(Object.prototype.hasOwnProperty.call(zypentoBlockVariables,"nonce")&&(a=zypentoBlockVariables.nonce),Object.prototype.hasOwnProperty.call(zypentoBlockVariables,"wpRestNonce")&&(s=zypentoBlockVariables.wpRestNonce)),"undefined"!=typeof zypentoJsVariables&&(Object.prototype.hasOwnProperty.call(zypentoJsVariables,"nonce")&&(a=zypentoJsVariables.nonce),Object.prototype.hasOwnProperty.call(zypentoJsVariables,"wpRestNonce")&&(s=zypentoJsVariables.wpRestNonce)),t.sendData.nonce=a,console.log(t),jQuery.ajax({type:"POST",url:t.url,data:t.sendData,beforeSend:function(e){e.setRequestHeader("X-WP-Nonce",s)},timeout:t.timeout,success:function(e){console.log(e),n(e)},error:function(e,t,n){console.log(e,t,n),o()}}),console.log(r)}))}))})()})();