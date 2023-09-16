function cfmenuMangeInputs() {
    this.current_inp_count = 0;
    this.createINP = function(name = '', url = '', direct_id = '', extra_ids = '', icon = '', fcurrent_inp_count = 0) {
        try {
            fcurrent_inp_count = fcurrent_inp_count.match(/(\d+)/)[0];
        } catch (e) {}
        if (fcurrent_inp_count == 0) ++this.current_inp_count;
        else this.current_inp_count = fcurrent_inp_count.match(/(\d+)/)[0];
        let inp = document.createElement('div');
        inp.setAttribute('lvl', this.current_inp_count);
        inp.classList.add('lvl-container');
        inp.classList.add('mb-2');

        let inp_html = `<div id="lvl-${this.current_inp_count}" class="row mx-auto">
            <div class="text-left mx-3 my-auto del-close" id="del-${this.current_inp_count}" onclick="cfmenu_remove_nav(this.id)"><i class="fas fa-times-circle delinp"></i></div>
            <input type="hidden" class="extraids" value="${extra_ids}">
            <input type="hidden" class="direct_id" value="${direct_id}">
            <div class="pdr-1 m-2">
                <input type="text" class="inpname form-control" placeholder="${t('Enter Name')}" required value="${name}">
                </div>
            <div class="pdr-0 m-2">
                <input type="text" class="inpurl form-control" placeholder="${t('Enter URL')}" required value="${url}">
            </div>
            <div class="pdr-0 m-2">
                <input type="text" class="inpicon form-control" placeholder="${t('Enter Icon')}" value="${icon}">
            </div>
            <button type="button" class="text-primary" onclick="cfmenu_add_update_nav(this.id)" style="border-radius: 10px;font-size:20px;background:none;outline:none; border:none;" id="button-${this.current_inp_count}"><i class="far fa-plus-square"></i></button>
        </div>
        `;
        inp.innerHTML = inp_html;

        let main_container = document.querySelectorAll("#cfmenu_inputs")[0];
        main_container.appendChild(inp);
        main_container.scrollTop = main_container.scrollHeight;

        let _this = this;

        setTimeout(function() {
            let arr = [
                inp.querySelectorAll('input.inpname')[0]
            ];
            let ttl = inp.querySelectorAll('input.inpurl')[0];
            arr.forEach(doc => { doc.disabled = false; });
            ttl.placeholder = "Enter URL";
        }, 200);

        inp.addEventListener('click', function() {
            document.querySelectorAll("#cfmenu_inputs div[lvl]").forEach(doc => {
                doc.classList.remove('selected_input');
            });
            this.classList.add('selected_input');
        });

        let inp_close = inp.querySelectorAll('.delinp')[0];
        inp_close.inp = inp;
        inp_close.addEventListener('click', function() {
            let inp = this.inp;
            let doc = document.querySelectorAll(`#cfmenu_inputs`)[0];
            doc.removeChild(inp);
            let count = 0;
            doc.querySelectorAll("div[lvl]").forEach(lvl => {
                ++count;
                lvl.setAttribute('lvl', count);
            });
        });
    };

    this.getInputs = function() {
        let docs = document.querySelectorAll("#cfmenu_inputs div[lvl]");
        let inputs = [];
        docs.forEach((item, index) => {
            let getVal = function(cls) {
                return item.querySelectorAll(`.${cls}`)[0].value;
            };
            let getId = function(ids) {
                return item.querySelectorAll(`.${ids}`)[0].id;
            }

            let inpurl = getVal('inpurl');
            let name = getVal('inpname');
            let del = getId('del-close');
            let icon = getVal('inpicon');
            let extraids = getVal('extraids');
            let direct_id = getVal('direct_id');
            icon = icon.replace(/"/g, "'");
            inputs.push({ name, inpurl, icon, del, direct_id, extraids });
        });
        return JSON.stringify(inputs);
    };
}