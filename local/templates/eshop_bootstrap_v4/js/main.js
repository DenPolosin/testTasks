var showPopupThanks = function () {
    var popup = document.createElement('div');
    popup.id = 'popup-thanks';
    popup.classList.add('popup');

    var overlay = document.createElement('div');
    overlay.classList.add('popup-overlay');
    var form = document.createElement('div');
    form.classList.add('popup-form');
    form.innerText = 'Спасибо за уведомление!';

    popup.append(overlay);
    popup.append(form);
    document.body.append(popup);

    setTimeout(function () {
        document.querySelector('#popup-thanks').remove();
    }, 2000);
};

var sendErrorInText = function (event) {
    var enterButtonCode = 13;
    if (event.ctrlKey === true && event.keyCode === enterButtonCode) {
        var selectedText = window.getSelection().toString();
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/local/templates/eshop_bootstrap_v4/ajax/error_handling_in_text.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        var body = 'AJAX=Y&SELECTED_TEXT=' + encodeURIComponent(selectedText) + '&URL=' + document.location.href;
        xhr.send(body);

        xhr.onreadystatechange = function () {
            var stateDoneValue = 4;
            if (this.readyState !== stateDoneValue) {
                return;
            }

            if (this.status !== 200) {
                console.error(this.status + ': ' + this.statusText);
            } else {
                var responseBody = JSON.parse(this.responseText);
                if (responseBody.status === 'error') {
                    console.error(responseBody.msg);
                } else if (responseBody.status === 'success') {
                    showPopupThanks();
                } else {
                    console.log(responseBody);
                }
            }
        }
    }
};

document.addEventListener('keyup', sendErrorInText);
