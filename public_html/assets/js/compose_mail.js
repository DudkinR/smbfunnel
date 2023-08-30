const request=new ajaxRequest();
const cm_storage=new Vuex.Store({
    state:{
        identifier:"",
        selected_smtps:[],
        selected_lists:[],
        custom_emails:"",
        do_spin:true,
        group_size:20,
        delay_between:30,
        email_subject:"",
        email_body:"",
        email_uns:"",
        sent_stat:{total:0,sent:0,init:false,pending:0,started:false},
    },
    getters:{
        getState:function(state){
            return state;
        },
    },
    mutations:{
        setHistory:function(state,arg)
        {
            state[arg.key]=arg.value;
        },
        updateStorage:function(state,arg)
        {
            try
            {
                if(Array.isArray(state[arg.key]))
                {
                    if(arg.keep !==undefined && arg.keep===false)
                    {
                        let is_present=state[arg.key].indexOf(arg.val);
                        if(is_present>-1)
                        {
                            state[arg.key].splice(is_present,1);
                        }
                    }
                    else if(state[arg.key].indexOf(arg.val)<0)
                    {
                        state[arg.key].push(arg.val);
                    }

                }
                else
                {
                    state[arg.key]=arg.val;
                }
            }catch(err){console.log(err);}
        }
    },
});

const compose_mail_app=new Vue({
    el:"#compose_mail",
    data:{
        err:"",
    },
    mounted:function(){
        document.addEventListener('DOMContentLoaded',()=>{
            //console.log(this);
            //this.loadTinyMce();
        });
    },
    computed: {
        state:function(){return cm_storage.getters.getState;},
        mailSendButton:function(){
            const stat=this.state.sent_stat;
            return (stat.init && stat.pending !==false)? `<i class="fas fa-spinner fa-spin"></i>&nbsp;${this.t("Sending...")}`:`${t("Send Mail")}`;
        },
    },
    methods:{
        /*w:function(txt,arr=[]){
            w(txt,arr);
        },*/
	    t:function(txt,arr=[]){
        return t(txt,arr);
        },
        updateStorage:function(key,val,keep=true)
        {
            cm_storage.commit('updateStorage',{key:key,val:val,keep:keep});
        },
        doInput:function(e,store_key,detect="value"){
            let data=e.target[detect];
            if(e.target.type=="checkbox")
            {
                this.updateStorage(store_key,data,e.target.checked);
            }
            else
            {
                this.updateStorage(store_key,data);
            }
        },
        composeMail:function(e){
            let sent_stat_ob={total:0,sent:0,init:false,pending:0,started:false};
            sent_stat_ob.force=Math.random();
            this.updateStorage('sent_data',{...sent_stat_ob});

            this.err="";
            let seq_title=this.state.identifier.trim();
            let smtps=this.state.selected_smtps.join(',');
            let lists=this.state.selected_lists.join(',');
            let custom_emails=this.state.custom_emails.trim().split("\n").join(',');
            let group_size=parseInt(this.state.group_size);
            let delay_between=parseInt(this.state.delay_between)*1000;
            let email_subject=this.state.email_subject.trim();
            let email_body=tinyMCE.get("email_content_composemail").getContent().trim();
            let email_uns=this.state.email_uns.trim();
            let seq_token=0;
            //#cc0052
            if(this.state.selected_smtps.length<1 || email_body.length<1)
            {
                this.err="<span style='color:#cc0052'>"+this.t("Make sure you selected SMTP and provided mailing content properly")+"</span>";
                return 0;
            }
            if(isNaN(delay_between) || isNaN(group_size)|| group_size<1)
            {
                this.err="<span style='color:#cc0052'>"+this.t("Please provide numeric value for Group size and Delay. The group size should be one or more.")+"</span>";
                return 0;
            }

            let group_delay_and_extramailstostore=JSON.stringify({
                group: group_size,
                delay: delay_between,
                extra_mails: custom_emails,
            });

            let sent_data=email_subject+"@clickbrk@"+email_body+"@clickbrk@"+email_uns;

            e.target.disabled=true;
            request.postRequestCb("req.php",{compose_cf_mail:1,type:"init",title:seq_title,smtps:smtps,lists:lists,custom_emails:custom_emails,sentdata:sent_data,extra_setup:group_delay_and_extramailstostore},async (data)=>{
                console.log(data);
                data=data.trim();
                if(data=='0')
                {
                    e.target.disabled=false;
                    this.err="<span style='color:#cc0052;'>"+this.t("Unable to initiate the process, no mail sent.")+"</span>";
                }
                else
                {
                    try
                    {
                    this.err="<span style='color:green'>"+this.t("Initiating the process, please wait...")+"</span>";
                    data=JSON.parse(data);
                    seq_token=data.token;
                    let chunk=data.data;
                    console.log(chunk);
                    
                    if(Array.isArray(chunk) && chunk.length>0)
                    {
                        let total_group=Math.ceil(chunk.length/group_size);
                        console.log(total_group);
                        let time_tosend=0;

                        sent_stat_ob.init=true;
                        sent_stat_ob.total=chunk.length;
                        sent_stat_ob.pending=total_group;
                        sent_stat_ob.force=Math.random();
                        this.updateStorage("sent_stat",{...sent_stat_ob});
                        for(let i=0;i<total_group;i++)
                        {
                            let temp_chunk=chunk.slice((i*group_size),(i*group_size+group_size));
                            
                            console.log(new Date());

                            await new Promise((resolve, reject)=>{
                                setTimeout((temp_chunk)=>{

                                    request.postRequestCb('req.php',{compose_cf_mail:1,type:'compose',compose_data:JSON.stringify(temp_chunk),compose_token:seq_token},(data)=>{
                                        resolve(1);
                                        sent_stat_ob.started=true;
                                        console.log(data);
                                        this.err="";
                                        data=data.trim();
                                        --sent_stat_ob.pending;
                                        if(sent_stat_ob.pending<1)
                                        {
                                            e.target.disabled=false;
                                            sent_stat_ob.pending=false;
                                        }
                                        if(!isNaN(data)){
                                            data=parseInt(data);
                                            sent_stat_ob.sent +=data;
                                        }
                                        console.log(JSON.stringify(sent_stat_ob));
                                        sent_stat_ob.force=Math.random();
                                        this.updateStorage("sent_stat",{...sent_stat_ob});
                                        
                                    });
                                    
                                },time_tosend,temp_chunk);
                            });
                            time_tosend=time_tosend+delay_between;
                        }
                    }
                    else
                    {
                        this.err="<span style='color:#cc0052;'>"+this.t("No subscriber found to send mail")+"</span>";
                    }
                    }
                    catch(err)
                    {
                        e.target.disabled=false;
                        this.err=`<span style='color:#cc0052;'>${this.t(err.message)}</span>`;
                        console.log(err)
                    }

                }
            });
        },
        selectAllLists:function(e){
            if(e.target.checked)
            {
                document.querySelectorAll("#select_lists_qfnl input[type=checkbox]").forEach((doc)=>{
                    if(doc.value !=e.target.value)
                    {
                        if(!doc.checked){doc.click();}
                        doc.onclick=function(){ if(e.target.checked){e.target.checked=false;} }
                    }
                });
            }
        },
        loadTinyMce:function()
        {
          //'#cookie_message'
          tinymce.init({
            selector : "#email_content_composemail",
            language: cf_tinymce_lang,
            convert_urls : false, 
         height: 465,
          plugins: 'image,link,code',
          toolbar: 'undo redo | link image | code | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help   ',
          content_css: [
            '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
            '//www.tiny.cloud/css/codepen.min.css'
          ],
          // enable title field in the Image dialog
          image_title: true,
            images_upload_url : 'req.php',
            automatic_uploads : false,
        
            images_upload_handler : function(blobInfo, success, failure) {
              var xhr, formData;
        
              xhr = new XMLHttpRequest();
              xhr.withCredentials = false;
              xhr.open('POST', 'req.php');
        
              xhr.onload = function() {
                var json;
        
                if (xhr.status != 200) {
                  failure('HTTP Error: ' + xhr.status);
                  return;
                }
               
                json = JSON.parse(xhr.responseText.trim());
                
                if (!json || typeof json.location != 'string') {
                  failure('Invalid JSON: ' + xhr.responseText);
                  return;
                }
        
                success(json.location);
              };
        
              formData = new FormData();
              formData.append('tinymceimgupload',1);
              formData.append('file', blobInfo.blob(), blobInfo.filename());
        
              xhr.send(formData);
            },
          });
        }
    }
});