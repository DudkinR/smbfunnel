$(document).ready(function () {
    $('#pabbly_form_setup').on('submit', function (e) {
        e.preventDefault();
        $('#savepabblybtn').html('Please wait...');
        $('#savepabblybtn').prop('disabled', true);

        var pabbly_form_data = encodeURIComponent(cfpabbly_inp_ob.getInputs());
        var form = "action=cfpabblyajax&" + "&savepabblybtn=1&form_data=" + pabbly_form_data;
        $.post($("#pabbly_form_ajax").val(), form, function (success) {
            var status = 0, msg = "Something went wrong! Please try again.";
            try {
                var json_parse = JSON.parse(success);
                status = json_parse.status;
                msg = json_parse.msg;
            } catch (e) {}
            cf_pabbly_launch_toast(status);
        });
        $('#savepabblybtn').html('Save Pabbly');
        $('#savepabblybtn').prop('disabled', false);
    });
});
function cf_pabbly_launch_toast(status) {
    var x;
    if(status) {
       x = document.getElementById("cfpabbly_toast_success");
    }
    else {
       x = document.getElementById("cfpabbly_toast_error");
    }

    x.className = "show";
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 5000);
}

function cfpabblyMangeInputs() {
    this.current_inp_count = 0;
    this.createINP = function (name = '', url = '', status = 0, fcurrent_inp_count = 0) {
        try {
            fcurrent_inp_count = fcurrent_inp_count.match(/(\d+)/)[0];
        } catch (e) { }
        if (fcurrent_inp_count == 0) ++this.current_inp_count;
        else this.current_inp_count = fcurrent_inp_count.match(/(\d+)/)[0];
        let inp = document.createElement('div');
        inp.setAttribute('lvl', this.current_inp_count);
        inp.classList.add('lvl-container');
        inp.classList.add('mb-2');

        let inp_html = `<div id="lvl-${this.current_inp_count}" class="row mx-auto">
            <div class="text-left mx-3 my-auto del-close" id="del-${this.current_inp_count}"><i class="fas fa-times-circle delinp"></i></div>
            <div class="pdr-1 m-2">
                <input type="text" class="inpname form-control" placeholder="${t('Enter Webhook Name')}" required value="${name}">
                </div>
            <div class="pdr-0 m-2">
                <input type="text" class="inpurl form-control" placeholder="${t('Enter URL')}" required value="${url}">
            </div>
            <div class="pdr-0 m-2">
                <button type="button" class="btn inpstatus btn-sm btn-primary btn-toggle ${(status) ? 'active' : ''}" data-toggle="button" aria-pressed="${(status)? 'true' : 'false'}" autocomplete="off">
                    <div class="handle"></div>
                </button>
            </div>
        </div>
        `;
        inp.innerHTML = inp_html;

        let main_container = document.querySelectorAll("#cfpabbly_inputs")[0];
        main_container.appendChild(inp);
        main_container.scrollTop = main_container.scrollHeight;

        let _this = this;

        setTimeout(function () {
            let arr = [
                inp.querySelectorAll('input.inpname')[0]
            ];
            let ttl = inp.querySelectorAll('input.inpurl')[0];
            arr.forEach(doc => { doc.disabled = false; });
            ttl.placeholder = "Enter URL";
        }, 200);

        inp.addEventListener('click', function () {
            document.querySelectorAll("#cfpabbly_inputs div[lvl]").forEach(doc => {
                doc.classList.remove('selected_input');
            });
            this.classList.add('selected_input');
        });

        let inp_close = inp.querySelectorAll('.delinp')[0];
        inp_close.inp = inp;
        inp_close.addEventListener('click', function () {
            let inp = this.inp;
            let doc = document.querySelectorAll(`#cfpabbly_inputs`)[0];
            doc.removeChild(inp);
            let count = 0;
            doc.querySelectorAll("div[lvl]").forEach(lvl => {
                ++count;
                lvl.setAttribute('lvl', count);
            });
        });
    };

    this.getInputs = function () {
        let docs = document.querySelectorAll("#cfpabbly_inputs div[lvl]");
        let inputs = [];
        docs.forEach((item, index) => {
            let getVal = function (cls) {
                return item.querySelectorAll(`.${cls}`)[0].value;
            };
            let getId = function (ids) {
                return item.querySelectorAll(`.${ids}`)[0].id;
            };
            let getStatus = function (ids) {
                console.log(item.querySelectorAll(`.${ids}`)[0].classList.contains('active'));
                return item.querySelectorAll(`.${ids}`)[0].classList.contains('active');
            };

            let url = getVal('inpurl');
            let name = getVal('inpname');
            let del = getId('del-close');
            let status = getStatus('inpstatus');
            inputs.push({ name, url, status, del });
        });
        return JSON.stringify(inputs);
    };
}