function addClass(obj, cls) {
    var classes = obj.className ? obj.className.split(' ') : [];
    for (var i = 0; i < classes.length; i++) {
        if (classes[i] === cls)
            return; // класс уже есть
    }
    classes.push(cls); // добавить
    obj.className = classes.join(' '); // и обновить свойство
}
function removeClass(obj, cls) {
    var classes = obj.className ? obj.className.split(' ') : [];
    for (var i = 0; i < classes.length; i++) {
        if (classes[i] === cls)
            classes.splice(i, 1);
    }
    obj.className = classes.join(' '); // и обновить свойство
}
function getCoords(elem) {
    var box = elem.getBoundingClientRect();
    var body = document.body;
    var docEl = document.documentElement;
    var scrollTop = window.pageYOffset || docEl.scrollTop || body.scrollTop;
    var scrollLeft = window.pageXOffset || docEl.scrollLeft || body.scrollLeft;
    var clientTop = docEl.clientTop || body.clientTop || 0;
    var clientLeft = docEl.clientLeft || body.clientLeft || 0;
    var top = box.top + scrollTop - clientTop;
    var left = box.left + scrollLeft - clientLeft;
    return {top: Math.round(top), left: Math.round(left)};
}
function animate(opts) {
    var start = new Date; // сохранить время начала
    var timer = setInterval(function () {
        var progress = 0;
        var wait = opts.wait || 0;
        var count = opts.count || 0;
        var i = 0;
        //Если установлена задержка - подождем
        if ((new Date - start) > wait) {
            // вычислить сколько времени прошло
            var progress = (new Date - start - wait) / opts.duration;
        }
        if (progress > 1)
            progress = 1;
        if (progress > 0) {
            // отрисовать анимацию
            opts.step(progress);
        }
        if (progress === 1) {
            i++;
            if (count > 0 && i === count)
                clearInterval(timer); // конец :)
            if (opts.afterAnimate)
                opts.afterAnimate();
            else {
                //переключение слайдов
                opts.next();
                start = new Date;
            }
        }
    }, opts.delay || 10); // по умолчанию кадр каждые 10мс
}
function setSlider(containerId, slideSrc, waitSec) {
    function startSlideShow() {
        var slideCount = slideSrc.length;
        if (slideCount < 2)
            return;
        var slider = document.getElementById(containerId);
        if (slider === null)
            return;
        var imgs = slider.getElementsByTagName("img");
        if (imgs.length < 1)
            return;
        var currentSlide = 0;
        var visibleImg = 0;
        if (imgs.length === 1) {
            var newImg = document.createElement('img');
            newImg.src = slideSrc[1];
            newImg.className = imgs[0].className;
            newImg.style.position = "absolute";
            slider.appendChild(newImg);
        }
        for (var i = 0; i < 2; i++) {
            imgs[i].style.opacity = 1 - i;
            imgs[i].style.filter = "Alpha(Opacity=" + ((1 - i) * 100) + ")";
        }
        visibleImg = 1 - visibleImg;
        currentSlide++;
        animate({
            duration: 2000,
            step: frame,
            delay: 50,
            wait: (waitSec || 2) * 1000,
            next: changeSlide
        });
        function frame(op) { // функция для отрисовки
            imgs[visibleImg].style.opacity = op;
            imgs[visibleImg].style.filter = "Alpha(Opacity=" + (op * 100) + ")";
            imgs[1 - visibleImg].style.opacity = 1.0 - op;
            imgs[1 - visibleImg].style.filter = "Alpha(Opacity=" + ((1.0 - op) * 100) + ")";
        }
        function changeSlide() {
            visibleImg = 1 - visibleImg;
            currentSlide++;
            if (currentSlide === slideCount)
                currentSlide = 0;
            imgs[visibleImg].src = slideSrc[currentSlide];
        }
    }
    window.onload = startSlideShow;
}
function getElementValue(elemId) {
    var elem = document.getElementById(elemId);
    if (elem !== null && elem !== undefined) {
        return elem.value;
    } else {
        return null;
    }
}
function setElementValue(elemId, val) {
    var elem = document.getElementById(elemId);
    if (elem !== null && elem !== undefined)
        elem.value = val;
}
function setElementText(elemId, val) {
    var elem = document.getElementById(elemId);
    if (elem !== null && elem !== undefined)
        elem.innerHTML = val;
}
function Notification(options) {
    var noti = options.elem;
    var mess = options.message;
    var autoClose = options.autoClose || 0;
    var messageElem = document.getElementById('notification_message');
    this.show = function () {
        noti.style.top = "-40px";
        noti.style.display = 'block';//('noti-show');
        if (messageElem !== undefined && mess !== undefined) {
            messageElem.innerHTML = mess;
        }
        animate({
            duration: 500,
            step: function (op) { // функция для отрисовки
                noti.style.top = (-40 + Math.round(40 * op)) + "px";
            },
            next: function () {},
            delay: 10,
            count: 1
        });
        /*if (autoClose>0){
         this.closeafter(autoClose);
         }*/
    };
    /*this.closeafter = async function(sec){
     await sleep(sec*1000);
     this.close();
     };*/
    this.close = function () {
        noti.style.display = 'none';
        return false;
    };
    document.getElementById('noti_close_link').onclick = this.close;
    document.getElementById('noti_bar').onclick = this.close;

    // noti.on('click', '.closeLink', function() {
    //     close();
    // });
}
function sendOrder() {
    //var fldName = document.getElementById("name_field");
    //var fldContact = document.getElementById("cotact_field");
    var err = false;
    //if (fldName.value.length==0){ addClass(fldName,"form-field-req");err=true;}
    //else removeClass(fldName,"form-field-req")
    //if (fldContact.value.length==0){ addClass(fldContact,"form-field-req");err=true;}
    //else removeClass(fldContact,"form-field-req")
    if (!err)
        postForm(
                {
                    token: "SaveOrder",
                    formId: "order_input_form",
                    onSuccess: function (result) {
                        close_dialog();
                        var noti = new Notification({elem: document.getElementById('noti_bar'), message: result.message});
                        noti.show();
                    }
                }
        );
}
function sendTranspOrder() {
    //var fldName = document.getElementById("name_field");
    //var fldContact = document.getElementById("cotact_field");
    var err = false;
    //if (fldName.value.length==0){ addClass(fldName,"form-field-req");err=true;}
    //else removeClass(fldName,"form-field-req")
    //if (fldContact.value.length==0){ addClass(fldContact,"form-field-req");err=true;}
    //else removeClass(fldContact,"form-field-req")
    if (!err)
        postForm(
                {
                    token: "CreateTranspOrder",
                    formId: "transp_order_form",
                    tipsBoxId: "ajaxmessage_box",
                    onSuccess: function (result) {
                        close_dialog();
                        setElementValue("cash_on_delivery", result.cash_on_delivery);
                        setElementValue("transp_number", result.transp_number);
                        var noti = new Notification({elem: document.getElementById('noti_bar'), message: result.message});
                        noti.show();
                    }
                }
        );
}
function sendSubscribe(itemId) {
    //addBasketItemsRequest(itemId,n);
    tipsField = "ajaxmessubscribe" + itemId;
    postForm(
            {
                token: "subscribe",
                formId: "subscribe" + itemId,
                tipsBoxId: "ajaxmessage_box",
                onSuccess: function (result) {
                    close_dialog();
                    var noti = new Notification({elem: document.getElementById('noti_bar')});
                    noti.show();
                }
            });
}
function loadOrder(itemId) {
    getForm(
            {
                token: "Order",
                formId: "order_" + itemId,
                onSuccess: function (result) {
                    open_dialog();
                    setElementText("order_name", result.order_number);
                    setElementValue("order_id", result.order_id);
                    setElementValue("shopify_id", result.shopify_id);
                    setElementValue("delivery_dt", result.delivery_dt);
                    setElementValue("delivery_address", result.delivery_address);
                    setElementValue("status", result.status);
                    setElementValue("status_dt", result.status_dt);
                    setElementValue("payment_status", result.payment_status);
                    setElementValue("payment_dt", result.payment_dt);
                    setElementValue("delivery_total", result.delivery_total);
                    setElementValue("cash_on_delivery", result.cash_on_delivery);
                    setElementValue("comments", result.comments);
                }
            }
    );
}
var popupStatus = 0;
function close_dialog(dialogId = 'dialog') {
    addClass(document.getElementById("mask"), "mask_hide");
    addClass(document.getElementById(dialogId), "mask_hide");
    //removeClass(document.getElementById("name_field"),"form-field-req");
    //removeClass(document.getElementById("cotact_field"),"form-field-req");
}
var op = 1;
function setOpac() {
    var TimerID;
    if (op <= 0.8) {
        document.getElementById("mask").style.opacity = op;
        op = op + 0.02;
        TimerID = setTimeout("setOpac()", 5);
    }
}
function open_dialog(dialogId = 'dialog') {
    var mask = document.getElementById("mask");
    var dialog = document.getElementById(dialogId);
    mask.className = 'mask';
    dialog.className = 'dialog_window';
    var dialogW = dialog.clientWidth;
    var winW = document.body.clientWidth;
    var winH = document.body.clientHeight;
    dialog.style.left = (winW / 2 - dialogW / 2) + "px";
    op = 0.1;
    setOpac();
    //mask.style.opacity = 0.8;
}