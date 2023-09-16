var qfnl_integrations=new Vue({
    el:"#qfnlintegrations",
    mounted:function(){
        this.base_url=document.querySelectorAll("#cfint_base_url")[0].value;
        this.plugins_base_url=document.querySelectorAll("#cfint_plugin_base_url")[0].value;
        this.plugins_base_url +='/';
        if(this.show=="setup")
        {
            this.init();
        }
    },
    data: {
        idcont:0,
        id:0,
        title:'',
        type:'',
        base_url:'',
        plugins_base_url:'',
        data:'',
        position:'footer',
        open:0,
        err:"",
        show:"table",
        code:true,
        reload:0,
        do:"insert",
        err:"",
        success_msg:"",
        integration_types:{tawkdotto:'tawk.to',messenger:'Messenger',skype:'Skype',ganalytic:'Google Analytic',fpixel:'Facebook Pixel',custom:'Custom'},
    },
    methods:{
    /*w:function(txt,arr=[]){
		w(txt,arr);
	},*/
	t:function(txt,arr=[]){
		return t(txt,arr);
	},
  
     init:function(){ var id_doc=document.getElementById("inididintegration");
     if(id_doc.value>0)
     {
         this.id=id_doc.value;
         this.getSettings();
     }
     var replaceid=this.id;
     var thiss=this;
     //alert(thiss.id);
     var new_id=Vue.extend({
         template: '<span><input type="hidden" id="intid" value="0" v-model="id">{{init()}}</span>',
         mounted:function(){},
         data:function(){return {id: replaceid.id};},
         methods:{
             init:function(){
                 this.id=thiss.id;
             },
         },
     });
     var new_id_elem=new new_id().$mount();
     //console.log(new_id_elem);
     this.$el.replaceChild(new_id_elem.$el,id_doc);
     this.idcont=new_id_elem.$el;},
     showDiv:function(show,id=0)
     {
         this.show=show;
         var searchdoc=document.getElementById("searchdivv");
         var designdiv1=document.getElementById("hidecard1");
         var designdiv2=document.getElementById("hidecard2");
         if(show=="table"){
             searchdoc.style.display="block";
             designdiv1.classList.add("card","pb-2","br-rounded");
             designdiv2.classList.add("card-body","pb-2");
            }else{
                searchdoc.style.display="none";
                designdiv1.classList.remove("card","pb-2","br-rounded");
                designdiv2.classList.remove("card-body","pb-2");
            }
         if(show=="table" && this.reload==1)
         {
          

             if(this.do=="insert")
             {  window.location="index.php?page=cfexscript_setups";
             }
             else
             {
                 window.location.reload();
             }
         }
         else
         {
             if(id>0)
             {
                 this.id=id;
              
              // alert(this.base_url);
                 this.popupOpen(this.type);
                 this.getSettings();

               
               
             }
         }
      
         
     },
     toggleCode:function(){
         this.code=(this.code)? false:true;
     },
      popupOpen:function(type,position='footer',code=true){
          this.code=code;
          var thisvue=this;
          if(this.open==1)
          {
              this.popupClose();
              this.popupOpen(type);
          }
          else
       {
          this.open=1;
          this.type=type;
          this.position=position;
          }
          doEscapePopup(function(){if(thisvue.open){thisvue.popupClose();}});
      },
      popupClose:function(){
          this.id=0;
          this.title="";
          this.type="";
          this.data="";
          this.position="footer";
          this.open=0;
          this.err="";
      },
      popUp:function($type="Add"){
          var div=document.createElement("div");
          div.classList.add('row');
          return "";
      },
      saveSettings:function(e){
        //   this.err="<font color='green'>"+this.t("Saving...")+"</font>";
        //   e.target.disabled=true;
        let url=this.base_url+'/index.php?page=ajax';
        let req=new XMLHttpRequest();
          var thiss=this;
          this.err="";
          this.success_msg="";
            this.do=(this.id>0)? "update":"insert";
      thiss.reload=1;

          if(this.title.length>0 && this.data.length>0)
          {
          var req_data={"saveintegration":this.id,"title":this.title,"data":this.data,"position":this.position,"type":this.type};
        //  alert(req_data);

        req.onreadystatechange=()=>{
            if(req.readyState==4)
            {
                if(req.status===200)
                {
                    let res=req.responseText.trim();
              // alert(res);
                    if(res=='1')
                    {
                        this.success_msg="Saved successfully";
                    }
                    else
                    {
                        this.err="Failed to save";
                    }
                }
                else
                {
                    this.err=req.statusText;
                }
            }
        };
        req.open('POST',url,true);
    }
            else
            {
                e.target.disabled=false;
                this.err="<font color='#800033'>"+this.t("Please Provide All Required Data")+"</font>";
            }


            let form_data=new FormData(document.createElement('form'));
            form_data.append('action','cfint_savcredentials');
            form_data.append('cfint_id',this.id);
            form_data.append('cfint_title',this.title);
            form_data.append('cfint_position',this.position);
            form_data.append('cfint_data',this.data);
            form_data.append('cfint_type',this.type);   
            form_data.append('cfint_do',this.do);   
            req.send(form_data);
      },
      getSettings:function(){   
         var thiss=this;
         // var req_data={"gateintegration":this.id};
      this.err="<font color='green'>Loading...</font>";
      let url=this.base_url+'/index.php?page=ajax';
                 let req=new XMLHttpRequest();
          req.onreadystatechange=()=>{
            if(req.readyState==4)
            {
                if(req.status===200)
                {
                   let res=req.responseText.trim();
             //  alert(url);
               try
                  {
                    var json_ob=JSON.parse(res);
                    thiss.title=json_ob.title;
                    thiss.type=json_ob.type;
                    thiss.data=json_ob.data;
                    thiss.position=json_ob.position;
                    thiss.err="";
                    modifytitle(thiss.title,'Integrations');
                    if(thiss.type=="messenger" || thiss.type=="skype")
                    {
                        thiss.code=false;
                        if(thiss.data.indexOf("/script")>0)
                        {
                            thiss.code=true;
                        }
                    }

                  }
                  catch(errr)
                  {
                      thiss.err="Unable To Load Integration";
                     // console.log(errr.message);
                     // console.log(res);
                  }
                 
                }
                
            }
        };
       req.open('POST',url,true);
      
           
         let form_data1=new FormData(document.createElement('form'));
         form_data1.append('action','cfint_updatecredentials');
         form_data1.append('cfeditid',this.id);
           
         req.send(form_data1);
      //   req.open(POST,url,true);
      },
    },
});
