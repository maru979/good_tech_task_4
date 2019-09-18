$(document).ready(function () {
    $("[data-form='call_me']").on('click', () => {
        $("#call_me.form").slideDown();
        $(".over").fadeIn();
    });
    $("[data-form='calc_1']").on('click', () => {
        $("#calc_1.form").slideDown();
        $(".over").fadeIn();
    });
    $("[data-form='calc_2']").on('click', () => {
        $("#calc_2.form").slideDown();
        $(".over").fadeIn();
    });
    $("[data-form='calc_3']").on('click', () => {
        $("#calc_3.form").slideDown();
        $(".over").fadeIn();
    });
    $(".close").on('click', () => {
        $(".form").slideUp();
        $(".over").fadeOut();
    });
    $(".over").on('click', () => {
        $(".form").slideUp();
        $(".over").fadeOut();
    });
});

//?utm_source=vk&utm_medium=banner&utm_campaign=google-poisk&utm_content=banner-full1&utm_term=window_installation&referrer=facebook.com
window.onload = function(){
    if (!document.cookie){
        var now = new Date();
        now.setTime(now.getTime() + 3 * 3600 * 1000);
        var utms = getSearchUTMs();
        if(Object.keys(utms).length > 0){
            document.cookie = 'utm_source=' + utms.utm_source + '; expires=' + now.toUTCString() + '; path=/';
            document.cookie = 'utm_medium=' + utms.utm_medium + '; expires=' + now.toUTCString() + '; path=/';
            document.cookie = 'utm_content=' + utms.utm_content + '; expires=' + now.toUTCString() + '; path=/';
            document.cookie = 'utm_campaign=' + utms.utm_campaign + '; expires=' + now.toUTCString() + '; path=/';
            document.cookie = 'utm_term=' + utms.utm_term + '; expires=' + now.toUTCString() + '; path=/';
            document.cookie = 'referrer=' + utms.referrer + '; expires=' + now.toUTCString() + '; path=/';
        }
    }
}

function sendData(form_id, index){
    var formData = {
        "form": {
        "id": form_id,
        "page": window.location.href.split('?')[0]
      },
      "utm": {
        "utm_source": getCookie('utm_source'),
        "utm_medium": getCookie('utm_medium'),
        "utm_campaign": getCookie('utm_campaign'),
        "utm_content": getCookie('utm_content'),
        "utm_term": getCookie('utm_term'),
        "referrer": getCookie('referrer')
      },
      "contact": {
        "name": document.getElementsByName('name')[index].value,
        "email": document.getElementsByName('email')[index].value,
        "phone": document.getElementsByName('phone')[index].value
      },
    };

    if (index != 3){  
        var addData = {
                "fields": {
                "height": document.getElementsByName('height')[index].value,
                "width": document.getElementsByName('width')[index].value,
                "profile": document.getElementsByName('profile')[index].value,
                "number": document.getElementsByName('number')[index].value,
                "mechanism": document.getElementsByName('mechanism')[index].value
            }
        };
         Object.assign(formData,addData);
    }

    $.ajax({
        type: 'POST',
        url: 'main.php',
        data: {'queryData': JSON.stringify(formData)},
        dataType: 'json',
    });

/*    setTimeout(function () {
      window.location.href = 'thanks.html';  
    }, 200);*/
    
}

function getSearchUTMs() {
    var utmstr = window.location.search.substr(1);
    if (utmstr != null && utmstr != ""){
        var utms = {};
        var utmarr = utmstr.split("&");
        for ( var i = 0; i < utmarr.length; i++) {
            var tmparr = utmarr[i].split("=");
            utms[tmparr[0]] = tmparr[1];
        }
        return utms;
    }
    else{
        return {};
    } 
}

function getCookie(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2) return parts.pop().split(";").shift();
}