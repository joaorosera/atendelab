// helper simples pra chamar o backend (controller=X&action=Y) sem repetir fetch toda hora
window.AtendeLabApi = (function () {
    var baseUrl = '/atendelab/public/';

    function request(controller, action, opts) {
        opts = opts || {};
        var method = opts.method || 'GET';
        var query = opts.query || {};
        var body = opts.body || null;

        var params = new URLSearchParams(Object.assign({ controller: controller, action: action }, query));
        var options = { method: method, credentials: 'same-origin' };

        if (method !== 'GET' && body !== null) {
            var form = body instanceof FormData ? body : objectToFormData(body);
            options.body = new URLSearchParams([...form.entries()]);
            options.headers = { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' };
        }

        return fetch(baseUrl + '?' + params.toString(), options).then(function (response) {
            return response.text().then(function (text) {
                var data;
                try {
                    data = text ? JSON.parse(text) : {};
                } catch (e) {
                    throw new Error(text || 'Resposta inválida recebida do backend.');
                }

                if (!response.ok || data.erro) {
                    throw new Error(data.erro || data.mensagem || ('Erro HTTP ' + response.status));
                }

                return data;
            });
        });
    }

    function objectToFormData(obj) {
        var form = new FormData();
        for (var key in obj) {
            form.append(key, String(obj[key] ?? ''));
        }
        return form;
    }

    // os controllers de listar() sempre retornam um array direto, mas deixei
    // esse helper porque em algum momento pensei em mudar pra retornar um objeto
    function toList(data) {
        return Array.isArray(data) ? data : [];
    }

    function toObject(data) {
        return data && typeof data === 'object' && !Array.isArray(data) ? data : {};
    }

    function escape(value) {
        return String(value ?? '').replace(/[&<>'"]/g, function (char) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char];
        });
    }

    function escapeAttr(value) {
        return escape(value).replace(/`/g, '&#096;');
    }

    function showAlert(id, message, type) {
        type = type || 'success';
        var element = document.getElementById(id);
        if (!element) return;
        element.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            escape(message) + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }

    return {
        get: function (controller, action, query) { return request(controller, action, { query: query || {} }); },
        post: function (controller, action, body) { return request(controller, action, { method: 'POST', body: body || {} }); },
        toList: toList,
        toObject: toObject,
        escape: escape,
        escapeAttr: escapeAttr,
        showAlert: showAlert
    };
})();
