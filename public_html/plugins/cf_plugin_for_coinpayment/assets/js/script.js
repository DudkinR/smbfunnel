import CFPay_methods from './methods.js';

let cfpay_payment_methods = new Vue({
    el: "#cfpay_payment_methods",
    mounted: function() {
        this.base_url = document.querySelectorAll("#cfpay_base_url")[0].value;
        this.plugins_base_url = document.querySelectorAll("#cfpay_plugin_base_url")[0].value;
        this.plugins_base_url += '/';

        let url_ob = new URL(window.location.href);
        this.current_method = url_ob.searchParams.get('page');
        this.current_method = this.current_method.split('_');
        this.current_method = this.current_method[this.current_method.length - 1];

        setTimeout(() => {
            this.processUpdatePopup();
        }, 200);
    },
    data: {
        methods: CFPay_methods,
        open_setup: false,
        selected_method_name: null,
        selected_method_data: {},
        payment_method_id: 0,
        base_url: '',
        plugins_base_url: '',
        err: "",
        success_msg: "",
        current_method: false,
    },
    methods: {
        showSetup: function(doo, method = null) {
            this.err = "";
            this.success_msg = "";
            this.payment_method_id = 0;
            this.selected_method_name = method;
            this.selected_method_data = (method === null) ? ({}) : ({...this.methods[method] });
            this.addEssentialFields(method);
            this.open_setup = doo;
        },
        addEssentialFields: function(method) {
            if (method !== null && (this.selected_method_data.fields !== undefined)) {
                for (let i in this.selected_method_data.fields) {
                    if (this.selected_method_data.fields[i].value === undefined) {
                        this.selected_method_data.fields[i].value = "";
                    }
                    if (this.selected_method_data.fields[i].placeholder === undefined) {
                        this.selected_method_data.fields[i].placeholder = "Enter required data";
                    }
                    if (this.selected_method_data.fields[i].name === undefined) {
                        this.selected_method_data.fields[i].name = "";
                    }
                    if (this.selected_method_data.fields[i].title === undefined) {
                        this.selected_method_data.fields[i].title = "";
                    }
                }
                this.selected_method_data = {...this.selected_method_data };
            }
        },
        replaceSingleQuot: function(data) {
            let reg = /&singlequot;/g;
            return data.replace(reg, "'");
        },
        processUpdatePopup: function() {
            try {
                let doc = document.querySelectorAll("#cfpay_saved_id")[0];
                //alert(doc);
                if (doc !== undefined) {
                    let method_name = this.replaceSingleQuot(document.querySelectorAll('#cfpay_saved_method')[0].value);
                    this.showSetup(true, method_name);

                    this.payment_method_id = doc.value;
                    this.selected_method_name = method_name;

                    if (this.selected_method_data.fields !== undefined) {
                        //cfpay_saved_title
                        //cfpay_saved_fields
                        //cfpay_saved_tax
                        let title = this.replaceSingleQuot(document.querySelectorAll("#cfpay_saved_title")[0].value);

                        let tax = this.replaceSingleQuot(document.querySelectorAll("#cfpay_saved_tax")[0].value);

                        let fields = {};
                        try {
                            fields = JSON.parse(this.replaceSingleQuot(document.querySelectorAll("#cfpay_saved_fields")[0].value));
                        } catch (err) {}
                        fields.title = title;
                        fields.tax = tax;

                        for (let i in fields) {
                            if (this.selected_method_data.fields[i] !== undefined) {
                                this.selected_method_data.fields[i].value = fields[i];
                            }
                        }
                    }

                } else { this.showSetup(true, this.current_method); }
            } catch (err) {}
        },
        saveSetup: function() {
            this.err = "";
            this.success_msg = "";
            if (this.selected_method_name === null) { return; }

            let url = this.base_url + '/index.php?page=ajax';
            let req = new XMLHttpRequest();
            req.onreadystatechange = () => {
                if (req.readyState == 4) {
                    if (req.status === 200) {
                        let res = req.responseText.trim();
                        if (res == '1') {
                            this.success_msg = "Saved successfully";
                            setTimeout(() => {
                                window.location = document.referrer;
                            }, 200);
                        } else {
                            let req_msg = req.responseText;
                            if (req_msg == 'HMAC signature does not match') req_msg = "Invalid API private key passed";
                            this.err = req_msg;
                        }
                    } else {
                        this.err = req.statusText;
                    }
                }
            };
            req.open('POST', url, true);
            let data;
            if (this.selected_method_data['fields'] !== undefined) { data = this.selected_method_data['fields']; } else { this.err = "Nothing to save."; return; }

            let title = 'No name';
            let tax = 0;
            let credentials = {};

            for (let i in data) {
                let val = data[i].value;
                if (typeof(data[i].value) === 'string') { val = data[i].value.trim(); }

                if (data[i].required !== undefined && data[i].required && val.length < 1) {
                    this.err = (data[i].name !== undefined && data[i].name.trim().length > 1) ? `Please provide data for ${data[i].name.trim()}.` : `Please provide all required data.`;
                    return;
                }
                if (i == 'title') {
                    title = val;
                } else if (i == 'tax' && !isNaN(Number(val))) {
                    tax = val;
                } else {
                    credentials[i] = val;
                }
            }

            //alert(this.payment_method_id);
            //return;
            let form_data = new FormData(document.createElement('form'));
            form_data.append('action', 'cfpay_savcredentials_' + this.current_method);
            form_data.append('cfpay_payment_id', this.payment_method_id);
            form_data.append('cfpay_payment_title', title);
            form_data.append('cfpay_payment_method', this.selected_method_name);
            form_data.append('cfpay_payment_credentials', JSON.stringify(credentials));
            form_data.append('cfpay_payment_tax', tax);
            req.send(form_data);
        },
        cfCoinpaymentCopyText: function(textVal) {
            navigator.clipboard.writeText(textVal);
            var x = document.getElementById("cfCoinpayment_snackbar");
            x.classList.add("show");
            setTimeout(function() { x.classList.remove('show') }, 3000);
        }
    }
});

export default cfpay_payment_methods;