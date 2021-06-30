var ready = function () {
    var addToBasket = function (event) {
        event.preventDefault();

        var formData = [];
        var elements = this.querySelectorAll('input[name]');
        for (var key in elements) {
            if (!elements.hasOwnProperty(key) || elements[key].value.length === 0 || elements[key].name.length === 0) {
                continue;
            }

            formData.push(encodeURIComponent(elements[key].name) + '=' + encodeURIComponent(elements[key].value));
        }

        if (formData !== []) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/local/templates/eshop_bootstrap_v4/ajax/add_to_basket.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send(formData.join('&'));

            xhr.onreadystatechange = function () {
                var stateDoneValue = 4;
                if (this.readyState !== stateDoneValue) {
                    return;
                }

                if (this.status !== 200) {
                    console.error(this.status + ': ' + this.statusText);
                } else {
                    var responseBody = JSON.parse(this.responseText);
                    var target = document.querySelector('#atc_form');
                    if (responseBody.status === 'error') {
                        console.error(responseBody.msg);
                        target.insertAdjacentElement('beforebegin', getAlert(responseBody.msg, 'error'));
                    } else if (responseBody.status === 'success' && typeof responseBody.msg !== 'undefined') {
                        target.insertAdjacentElement('beforebegin', getAlert(responseBody.msg, 'success'));
                    } else {
                        console.log(responseBody);
                    }
                }
            }
        }
    };
    var getError = function (message) {
        var error = document.createElement('div');
        if (typeof message === 'undefined') {
            return error;
        }

        error.classList.add('atc_error');
        error.innerText = message;

        return error;
    }
    var getAlert = function (message, type) {
        var alert = document.createElement('div');
        if (typeof message === 'undefined') {
            return error;
        }

        alert.classList.add('alert');
        if (type === 'error') {
            alert.classList.add('alert-danger');
        } else if (type === 'success') {
            alert.classList.add('alert-success');
        } else {
            alert.classList.add('alert-primary');
        }

        alert.innerText = message;

        return alert;
    }
    var getProductInfo = function (product) {
        var productInfo = document.createElement('div');
        productInfo.classList.add('atc_product-info');

        if (product.hasOwnProperty('DETAIL_PICTURE')) {
            var img = document.createElement('img');
            img.setAttribute('src', product.DETAIL_PICTURE);
            var imgContainer = document.createElement('div');
            imgContainer.classList.add('atc_image');
            imgContainer.append(img);
            productInfo.append(imgContainer);
        }

        var infoContainer = document.createElement('div');
        infoContainer.classList.add('atc_info');

        if (product.hasOwnProperty('NAME')) {
            var name = document.createElement('p');
            name.classList.add('atc_product-name');
            name.innerText = product.NAME;
            infoContainer.append(name);
        }

        if (product.hasOwnProperty('PROPERTIES')) {
            for (var key in product.PROPERTIES) {
                if (!product.PROPERTIES.hasOwnProperty(key) || !product.PROPERTIES[key].hasOwnProperty('VALUE')) {
                    continue;
                }

                var property = document.createElement('p');
                property.innerText = product.PROPERTIES[key].NAME + ': ' + product.PROPERTIES[key].VALUE;
                infoContainer.append(property);
            }
        }

        if (product.hasOwnProperty('DETAIL_TEXT')) {
            var detailText = document.createElement('div');
            detailText.classList.add('atc_description');
            detailText.innerHTML = product.DETAIL_TEXT;
            infoContainer.append(detailText);
        }

        productInfo.append(infoContainer);

        return productInfo;
    }
    var addProductInfo = function () {
        var parentElement = this.closest('.atc_block');
        if (this.value === '') {
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/local/templates/eshop_bootstrap_v4/ajax/get_product_info.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        var body = 'AJAX=Y&XML_ID=' + encodeURIComponent(this.value) + '&URL=' + document.location.href;
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
                var productInfoBlock = parentElement.querySelector('.atc_product-info');
                if (responseBody.status === 'error') {
                    console.error(responseBody.msg);
                    if (productInfoBlock !== null) {
                        productInfoBlock.innerHTML = '';
                        productInfoBlock.append(getError(responseBody.msg));
                    }
                } else if (responseBody.status === 'success' && typeof responseBody.data !== 'undefined') {
                    var productInfo = getProductInfo(responseBody.data);
                    if (productInfoBlock !== null) {
                        productInfoBlock.replaceWith(productInfo);
                    }
                } else {
                    console.log(responseBody);
                }
            }
        }
    };
    var getRow = function () {
        var block = document.createElement('div');
        block.classList.add('atc_block');

        var input = document.createElement('input');
        input.setAttribute('type', 'text');
        input.setAttribute('name', 'XML_IDS[]');
        input.classList.add('atc_xml-id');
        input.addEventListener('blur', addProductInfo);
        var inputContainer = document.createElement('div');
        inputContainer.classList.add('atc_input_container');

        var productInfo = document.createElement('div');
        productInfo.classList.add('atc_product-info');

        var button = document.createElement('input');
        button.setAttribute('type', 'button');
        button.classList.add('btn');
        button.classList.add('btn-danger');
        button.classList.add('atc_button_delete');
        button.value = window.buttonDeleteButtonName || 'X';
        button.addEventListener('click', function () {
            var row = this.closest('.atc_block')
            if (row !== null) {
                row.remove();
            }
        });
        var buttonContainer = document.createElement('div');
        buttonContainer.classList.add('atc_delete_row');

        inputContainer.append(input);
        buttonContainer.append(button);
        block.append(inputContainer);
        block.append(productInfo);
        block.append(buttonContainer);

        return block;
    };
    var getAddRowButton = function () {
        var block = document.createElement('div');
        block.id = 'atc_block_add_row_button';
        block.classList.add('atc_block');
        var input = document.createElement('input');
        input.setAttribute('type', 'button');
        input.id = 'atc_add_row_button';
        input.classList.add('btn');
        input.classList.add('btn-primary');
        input.classList.add('atc_add_row_button');
        input.value = window.buttonAddButtonName || '+';
        input.addEventListener('click', function () {
            var target = document.querySelector('#atc_block_add_row_button');
            if (target !== null) {
                target.insertAdjacentElement('beforebegin', getRow());
            }
        });

        block.append(input);

        return block;
    };

    var container = document.querySelector('#atc_container');
    if (container !== null) {
        container.append(getRow());
        container.append(getAddRowButton());
    }

    var form = document.querySelector('#atc_form');
    if (form !== null) {
        form.addEventListener('submit', addToBasket);
    }
};

document.addEventListener('DOMContentLoaded', ready)
