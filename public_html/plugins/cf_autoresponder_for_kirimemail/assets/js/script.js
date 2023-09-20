import CFkirim_methods from './methods.js';

let cfkirim_autoresponder_methods=new Vue({
    el: "#cfkirim_autoresponder_methods",
    mounted:function(){
        this.base_url=document.querySelectorAll("#cfkirim_base_url")[0].value;
        this.plugins_base_url=document.querySelectorAll("#cfkirim_plugin_base_url")[0].value;
        this.plugins_base_url +='/';

        setTimeout(()=>{
            this.processUpdatePopup();
        },200);
    },
    data: {
        methods: CFkirim_methods,
        open_setup:false,
        selected_method_name: null,
        selected_method_data: {},
        autoresponder_id:0,
        base_url:'',
        plugins_base_url:'',
        err:"",
        success_msg:""
    },
    methods: {
        showSetup: function(doo, method=null)
        {
            this.err="";
            this.success_msg="";
            this.autoresponder_id=0;
            this.selected_method_name= method;
            this.selected_method_data=(method===null)? ({}):({...this.methods[method]});
            this.addEssentialFields(method);
            this.open_setup= doo;
        },
        addEssentialFields:function(method){
            if(method !==null && (this.selected_method_data.fields !==undefined))
            {
                for(let i in this.selected_method_data.fields)
                {
                    if(this.selected_method_data.fields[i].value===undefined)
                    {
                        this.selected_method_data.fields[i].value="";
                    }
                    if(this.selected_method_data.fields[i].placeholder===undefined)
                    {
                        this.selected_method_data.fields[i].placeholder="Enter required data";
                    }
                    if(this.selected_method_data.fields[i].name===undefined)
                    {
                        this.selected_method_data.fields[i].name="";
                    }
                    if(this.selected_method_data.fields[i].title===undefined)
                    {
                        this.selected_method_data.fields[i].title="";
                    }
                }
                this.selected_method_data={...this.selected_method_data};
            }
        },
        replaceSingleQuot:function(data){
            let reg=/&singlequot;/g;
            return data.replace(reg,"'");
        },
        processUpdatePopup:function()
        {
            try
            {
                let doc=document.querySelectorAll("#cfautores_saved_id")[0];
                if(doc !==undefined)
                {
                    let method_name=this.replaceSingleQuot(document.querySelectorAll('#cfautores_saved_method')[0].value);
                    this.showSetup(true, method_name);

                    this.autoresponder_id=doc.value;
                    this.selected_method_name=method_name;

                    if(this.selected_method_data.fields !==undefined)
                    {
                 
                        let title=this.replaceSingleQuot(document.querySelectorAll("#cfautores_saved_title")[0].value);

                        let fields={};
                        try
                        {
                            fields=JSON.parse(this.replaceSingleQuot(document.querySelectorAll("#cfautores_saved_fields")[0].value));
                        }catch(err){}
                        fields.title=title;
                        
                        for(let i in fields)
                        {
                            if(this.selected_method_data.fields[i] !==undefined)
                            {
                                this.selected_method_data.fields[i].value=fields[i];
                            }
                        }
                    }

                }
            }catch(err){}
        },
        saveSetup:function(){
            this.err="";
            this.success_msg="";
            if(this.selected_method_name===null){return;}

            let url=this.base_url+'/index.php?page=ajax';
            let req=new XMLHttpRequest();
            req.onreadystatechange=()=>{
                if(req.readyState==4)
                {
                    if(req.status===200)
                    {
                        let res=req.responseText.trim();
                        if(res=='1')
                        {
                            this.success_msg="Saved successfully";
                        }
                        else
                        {
                            this.err="Unable to save the setup";
                        }
                    }
                    else
                    {
                        this.err=req.statusText;
                    }
                }
            };
            req.open('POST',url,true);
            let data;
            if(this.selected_method_data['fields'] !==undefined)
            {data=this.selected_method_data['fields'];}
            else
            {this.err="Nothing to save.";return;}

            let title='No name';
            let email='';
            let credentials={};

            for(let i in data)
            {
                let val=data[i].value;
                if(typeof(data[i].value)==='string')
                {val=data[i].value.trim();}

                if(data[i].required !==undefined && data[i].required && val.length<1)
                {
                    this.err=(data[i].name !==undefined && data[i].name.trim().length>1)? `Please provide data for ${data[i].name.trim()}.`:`Please provide all required data.`;
                    return;
                }
                if(i=='email')
                {
                    email=data[i].value.trim();
                    var mailformat = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
                    if(email.match(mailformat))
                    {
                      // console.log('Valid email address');
                    }
                    else
                    { 
                        this.err=`Please enter valid email`;
                        return;
                    }
                }
                if(i=='title')
                {
                    title=val;
                }
                else
                {
                    credentials[i]=val;
                }
            }

            let form_data=new FormData(document.createElement('form'));
            form_data.append('action','cfkirim_savcredentials');
            form_data.append('cfkirim_id',this.autoresponder_id);
            form_data.append('cfkirim_title',title);
            form_data.append('cfkirim_method',this.selected_method_name);
            form_data.append('cfkirim_credentials',JSON.stringify(credentials));
            form_data.append('cfkirim_email',email);
            req.send(form_data);
        }
    }
});

export default cfkirim_autoresponder_methods;