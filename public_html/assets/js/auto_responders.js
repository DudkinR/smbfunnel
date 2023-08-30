var request=new ajaxRequest();
var formshow=new Vue(
{    
	el:"#vueshowforms",
	data:{
		autoname:"",
		mainblock:true,err:"",
		mailengine:false,mailengine_apiurl:"",mailengine_apikey:"",mailengine_listid:"",mailengine_title:"",mailengine_email:"",
		activecampaign:false,active_apiurl:"",active_apikey:"",active_listid:"",active_title:"",
		mailchimp:false,mail_apikey:"",mail_listid:"",mail_email:"",mail_title:"",

		mailwizz:false,mailwizz_apiurl:"",mailwizz_apipublic:"",mailwizz_apiprivate:"",mailwizz_listid:"",mailwizz_title:"",mailwizz_email:"",
		
		sendiio:false,sendiio_apikey:"",sendiio_secret:"",sendiio_listid:"",sendiio_title:"",sendiio_email:"",
		mymailit:false,mymailit_apikey:"",mymailit_title:"",mymailit_email:"",

		moosend:false,moos_apikey:"",moos_listid:"",moos_title:"",moos_email:"",

		mautic:false,mautic_title:"",mautic_apiurl:"",mautic_access_token:"",mautic_api_key:"",mautic_api_secret:"",mautic_api_listid:"", mautic_email:"", mautic_auth_type:2,
		
		getresponse:false,get_apikey:"",get_campaignid:"",get_email:"",get_title:"",
		constantcont:false,const_apikey:"",const_token:"",const_listid:"",const_email:"",const_title:"",
		ontraport:false,ontra_apikey:"",ontra_appid:"",ontra_email:"",ontra_title:"",
		hubspot:false,hub_apikey:"",hub_email:"",hub_title:"",
		aweber:false,acc_id:"",cus_email:"",list_id:"",cus_title:"",

		mailerlite:false,mailerlite_apikey:"",mailerlite_title:"",mailerlite_email:"",mailerlite_listid:"",
		pursueapp:false,pursueapp_apikey:"",pursueapp_listid:"",pursueapp_email:"",pursueapp_title:"",
		autoid:"",
	},
	 mounted:function(){
	 	document.onreadystatechange = () => { 
    if (document.readyState == "complete") {
		document.getElementById("editorformcontainer").style.display="block";
        // alert(this.autoname);
        this.editform();
    }
}
  },
	methods:{
	/*w:function(txt,arr=[]){
	w(txt,arr);
	},*/
	t:function(txt,arr=[]){
		return t(txt,arr);
	},
	editform:function(){
		// alert(this.autoname);
		if(document.getElementById('autoname') && document.getElementById('autoname').value){
		this.autoname = document.getElementById('autoname').value;
		if (this.autoname == "mailengine") {
			this.autoid = document.getElementById('autoid').value;
			this.mailengine_title = document.getElementById('edittitle').value;
			this.mailengine_apikey = document.getElementById('editapikey').value;
			this.mailengine_apiurl = document.getElementById('editapiurl').value;
			this.mailengine_listid = document.getElementById('editlistid').value;
			this.mailengine=true;
			}
		if (this.autoname == "activecampaign") {
		this.autoid = document.getElementById('autoid').value;
		this.active_title = document.getElementById('edittitle').value;
		this.active_apikey = document.getElementById('editapikey').value;
        this.active_apiurl = document.getElementById('editapiurl').value;
        this.active_listid = document.getElementById('editlistid').value;
		this.activecampaign=true;
		}
		else if(this.autoname == "constantcont"){
		this.autoid = document.getElementById('autoid').value;
		this.const_title = document.getElementById('edittitle').value;
		this.const_apikey = document.getElementById('editapikey').value;
        this.const_token = document.getElementById('editaccesstkn').value;
        this.const_listid = document.getElementById('editlistid').value;
		this.constantcont=true;
		}
		else if(this.autoname == "getresponse"){
		this.autoid = document.getElementById('autoid').value;
		this.get_title = document.getElementById('edittitle').value;
		this.get_apikey = document.getElementById('editapikey').value;
        this.get_campaignid = document.getElementById('editcampid').value;
			this.getresponse=true;
		}
		else if(this.autoname == "hubspot"){
		this.autoid = document.getElementById('autoid').value;
		this.hub_title = document.getElementById('edittitle').value;
		 this.hub_apikey = document.getElementById('editapikey').value;
			this.hubspot=true;
		}
		else if(this.autoname == "mailchimp"){
		this.autoid = document.getElementById('autoid').value;
		this.mail_title = document.getElementById('edittitle').value;
		this.mail_apikey = document.getElementById('editapikey').value;
        this.mail_listid = document.getElementById('editlistid').value;
			this.mailchimp=true;
		}
		else if(this.autoname == "ontraport"){
		this.autoid = document.getElementById('autoid').value;
		this.ontra_title = document.getElementById('edittitle').value;
		this.ontra_apikey = document.getElementById('editapikey').value;
        this.ontra_appid = document.getElementById('editappid').value;
			this.ontraport=true;
		}
		else if(this.autoname == "aweber"){
		this.autoid = document.getElementById('autoid').value;
		this.cus_title = document.getElementById('edittitle').value;
        this.acc_id = document.getElementById('editappid').value;
        this.list_id = document.getElementById('editlistid').value;
			this.aweber=true;
		}
		else if(this.autoname == "sendiio"){
				this.autoid = document.getElementById('autoid').value;
				this.sendiio_title = document.getElementById('edittitle').value;
				this.sendiio_apikey = document.getElementById('editapikey').value;
				this.sendiio_secret = document.getElementById('editaccesstkn').value;
				this.sendiio_listid = document.getElementById('editlistid').value;
				this.sendiio=true;
			}
		else if(this.autoname == "mymailit"){
			this.autoid = document.getElementById('autoid').value;
			this.mymailit_title = document.getElementById('edittitle').value;
			this.mymailit_apikey = document.getElementById('editapikey').value;
			this.mymailit=true;
		}
		else if (this.autoname == "mailwizz") {
			this.autoid = document.getElementById('autoid').value;
			this.mailwizz_title = document.getElementById('edittitle').value;
			this.mailwizz_apipublic = document.getElementById('editapikey').value;
			this.mailwizz_apiprivate = document.getElementById('editaccesstkn').value;
			this.mailwizz_apiurl = document.getElementById('editapiurl').value;
			this.mailwizz_listid = document.getElementById('editlistid').value;
			this.mailwizz = true;
		}
		else if(this.autoname == "moosend"){
			this.autoid = document.getElementById('autoid').value;
			this.moos_title = document.getElementById('edittitle').value;
			this.moos_apikey = document.getElementById('editapikey').value;
			this.moos_listid = document.getElementById('editlistid').value;
				this.moosend=true;
		}
		else if(this.autoname == "mautic"){
			this.autoid = document.getElementById('autoid').value;
			this.mautic_title = document.getElementById('edittitle').value;
			this.mautic_api_secret= document.getElementById('editapikey').value;
			this.mautic_api_listid = document.getElementById('editlistid').value;
			this.mautic_apiurl = document.getElementById('editapiurl').value;
			this.mautic_api_key=document.getElementById('editappid').value;
			
				this.mautic=true;
		}
		else if(this.autoname == "mailerlite"){
			this.autoid = document.getElementById('autoid').value;
			this.mailerlite_title = document.getElementById('edittitle').value;
			this.mailerlite_apikey = document.getElementById('editapikey').value;
			this.mailerlite_listid = document.getElementById('editlistid').value;
			this.mailerlite=true;
		}
		else if(this.autoname == "pursueapp"){
			this.autoid = document.getElementById('autoid').value;
			this.pursueapp_title = document.getElementById('edittitle').value;
			this.pursueapp_apikey = document.getElementById('editapikey').value;
			this.pursueapp_listid = document.getElementById('editlistid').value;
			this.pursueapp=true;
		}
		/*else if(this.autoame=="mautic")
		{
			this.mautic=true;
		}*/
	}
	try{
		doEscapePopup(function(){thisvue.closeForm(thisvue.autoname);});
	}catch(err){}
	},
	closeForm:function(type){
		this.err="";
		if (type == "mailengine") {
			this.mailengine=false;
		}
		if (type == "activecampaign") {
			this.activecampaign=false;
		}
		else if(type == "constantcont"){
			this.constantcont=false;
		}
		else if(type == "getresponse"){
			this.getresponse=false;
		}
		else if(type == "hubspot"){
			this.hubspot=false;
		}
		else if(type == "mailchimp"){
			this.mailchimp=false;
		}
		else if(type == "ontraport"){
			this.ontraport=false;
		}
		else if (type == "aweber") {
			this.aweber=false;
		}
		else if (type == "sendiio") {
			this.sendiio=false;
		}
		else if(type=="mymailit")
		{
			this.mymailit=false;
		}
		else if(type=="mailwizz")
		{
			this.mailwizz=false;
		}
		else if(type =="moosend")
		{
			this.moosend=false;
		}
		else if(type =="mautic")
		{
			this.mautic=false;
		}
		else if(type=="mailerlite")
		{
			this.mailerlite=false;
		}
		else if(type=="pursueapp")
		{
			this.pursueapp=false;
		}
	},
	showForm:function(autotype){
		// this.mainblock=true;
		var thisvue=this;
		this.err="";
		if (autotype == "mailengine") {
			this.mailengine=true;
			this.activecampaign=false;
			this.constantcont=false;
			this.getresponse=false;
			this.hubspot=false;
			this.mailchimp=false;
			this.ontraport=false;
			this.aweber=false;
			this.sendiio=false;
			this.mymailit=false;
			this.mailwizz=false;
			this.moosend=false;
			this.mautic=false;
			this.mailerlite=false;
			this.pursueapp=false;
		}
		if (autotype == "activecampaign") {
			this.activecampaign=true;
			this.constantcont=false;
			this.getresponse=false;
			this.hubspot=false;
			this.mailchimp=false;
			this.ontraport=false;
			this.aweber=false;
			this.sendiio=false;
			this.mymailit=false;
			this.mailwizz=false;
			this.moosend=false;
			this.mautic=false;
			this.mailerlite=false;
			this.pursueapp=false;
		}
		else if(autotype == "constantcont"){
			this.constantcont=true;
			this.activecampaign=false;
			this.getresponse=false;
			this.hubspot=false;
			this.mailchimp=false;
			this.ontraport=false;
			this.aweber=false;
			this.sendiio=false;
			this.mymailit=false;
			this.mailwizz=false;
			this.moosend=false;
			this.mautic=false;
			this.mailerlite=false;
			this.pursueapp=false;
		}
		else if(autotype == "getresponse"){
			this.getresponse=true;
			this.activecampaign=false;
			this.constantcont=false;
			this.hubspot=false;
			this.mailchimp=false;
			this.ontraport=false;
			this.aweber=false;
			this.sendiio=false;
			this.mymailit=false;
			this.mailwizz=false;
			this.moosend=false;
			this.mautic=false;
			this.mailerlite=false;
			this.pursueapp=false;
		}
		else if(autotype == "hubspot"){
			this.hubspot=true;
			this.activecampaign=false;
			this.getresponse=false;
			this.getresponse=false;
			this.mailchimp=false;
			this.ontraport=false;
			this.aweber=false;
			this.sendiio=false;
			this.mymailit=false;
			this.mailwizz=false;
			this.moosend=false;
			this.mautic=false;
			this.mailerlite=false;
			this.pursueapp=false;
		}
		else if(autotype == "mailchimp"){
			this.mailchimp=true;
			this.constantcont=false;
			this.activecampaign=false;
			this.getresponse=false;
			this.hubspot=false;
			this.ontraport=false;
			this.aweber=false;
			this.sendiio=false;
			this.mymailit=false;
			this.mailwizz=false;
			this.moosend=false;
			this.mautic=false;
			this.mailerlite=false;
			this.pursueapp=false;
		}
		else if(autotype == "ontraport"){
			this.ontraport=true;
			this.constantcont=false;
			this.activecampaign=false;
			this.getresponse=false;
			this.hubspot=false;
			this.mailchimp=false;
			this.aweber=false;
			this.sendiio=false;
			this.mymailit=false;
			this.mailwizz=false;
			this.moosend=false;
			this.mautic=false;
			this.mailerlite=false;
			this.pursueapp=false;
		}
		else if(autotype == "aweber"){
			this.aweber=true;
			this.constantcont=false;
			this.activecampaign=false;
			this.getresponse=false;
			this.hubspot=false;
			this.mailchimp=false;
			this.ontraport=false;
			this.sendiio=false;
			this.mymailit=false;
			this.mailwizz=false;
			this.moosend=false;
			this.mautic=false;
			this.mailerlite=false;
			this.pursueapp=false;
		}
		else if(autotype == "sendiio"){
			this.aweber=false;
			this.constantcont=false;
			this.activecampaign=false;
			this.getresponse=false;
			this.hubspot=false;
			this.mailchimp=false;
			this.ontraport=false;
			this.sendiio=true;
			this.mymailit=false;
			this.mailwizz=false;
			this.moosend=false;
			this.mautic=false;
			this.mailerlite=false;
			this.pursueapp=false;
		}
		else if(autotype == "mymailit"){
			this.aweber=false;
			this.constantcont=false;
			this.activecampaign=false;
			this.getresponse=false;
			this.hubspot=false;
			this.mailchimp=false;
			this.ontraport=false;
			this.sendiio=false;
			this.mymailit=true;
			this.mailwizz=false;
			this.moosend=false;
			this.mautic=false;
			this.mailerlite=false;
			this.pursueapp=false;
		}
		else if(autotype == "mailwizz"){
			this.aweber=false;
			this.constantcont=false;
			this.activecampaign=false;
			this.getresponse=false;
			this.hubspot=false;
			this.mailchimp=false;
			this.ontraport=false;
			this.sendiio=false;
			this.mymailit=false;
			this.mailwizz=true;
			this.moosend=false;
			this.mautic=false;
			this.mailerlite=false;
			this.pursueapp=false;
		}
		else if(autotype == "moosend"){
			this.aweber=false;
			this.constantcont=false;
			this.activecampaign=false;
			this.getresponse=false;
			this.hubspot=false;
			this.mailchimp=false;
			this.ontraport=false;
			this.sendiio=false;
			this.mymailit=false;
			this.mailwizz=false;
			this.moosend=true;
			this.mautic=false;
			this.mailerlite=false;
			this.pursueapp=false;
		}
		else if(autotype == "mautic"){
			this.aweber=false;
			this.constantcont=false;
			this.activecampaign=false;
			this.getresponse=false;
			this.hubspot=false;
			this.mailchimp=false;
			this.ontraport=false;
			this.sendiio=false;
			this.mymailit=false;
			this.mailwizz=false;
			this.moosend=false;
			this.mautic=true;
			this.mailerlite=false;
			this.pursueapp=false;
		}
		else if(autotype == "mailerlite"){
			this.aweber=false;
			this.constantcont=false;
			this.activecampaign=false;
			this.getresponse=false;
			this.hubspot=false;
			this.mailchimp=false;
			this.ontraport=false;
			this.sendiio=false;
			this.mymailit=false;
			this.mailwizz=false;
			this.moosend=false;
			this.mautic=false;
			this.mailerlite=true;
			this.pursueapp=false;
		}
		else if(autotype == "pursueapp"){
			this.aweber=false;
			this.constantcont=false;
			this.activecampaign=false;
			this.getresponse=false;
			this.hubspot=false;
			this.mailchimp=false;
			this.ontraport=false;
			this.sendiio=false;
			this.mymailit=false;
			this.mailwizz=false;
			this.moosend=false;
			this.mautic=false;
		    this.mailerlite=false;
			this.pursueapp=true;
		}
		try{doEscapePopup(function(){thisvue.closeForm(autotype);});}catch(err){}
	},
	getMauticAccessToken:function(){
		// let url=new URL(window.location.href);
		// let public_key=this.mautic_api_key.trim();
		// let apiurl=this.mautic_apiurl.trim();
		// let secret=this.mautic_api_secret.trim();

		// if( public_key.length<1 || apiurl.length<1 || secret.length<1 )
		// {
		// 	this.err=`<span class="text-danger">Please provide valid Mautic installation URL, Public key & Secret key to process</span>`;
		// 	return;
		// }

		// url.searchParams.delete('page');
		// url.searchParams.append('page','processmauutic');
		// url.searchParams.append('do_auth',1);
		// url.searchParams.append('mautic_appid', public_key);
		// url.searchParams.append('mautic_apiurl', apiurl);
		// url.searchParams.append('mautic_secret', secret);
		// url.searchParams.append('mautic_outh_type',this.mautic_auth_type)
		// window.open(url.href,"_blank","location=yes,height=570,width=520,scrollbars=yes,status=yes");
	},
	getMyMailItId:function(){
		let p=prompt(this.t(`Please enter Standard Form Embed Code`),'');
		p=(p !==null)? p.trim():'';
		if(p.length>4)
		{
		  let err_doc=document.querySelectorAll(`.mymailiterr`)[0];
		  try
		  {
			let doc=document.createElement(`div`);
			doc.innerHTML=p;
			let ifr=doc.querySelectorAll(`iframe`)[0];
			let url=new URL(ifr.src);
			let form_id=url.searchParams.get(`p`);
			if(form_id)
			{
			  this.mymailit_apikey=form_id;
			  this.err='';
			}
			else
			{
			  this.err=`<p><center style='margin: -17px 0px -8px 0px;color: red;'>${this.t('Please enter valid data')}</center></p>`;
			}
		  }catch(err){
			this.err=`<p><center style='margin: -17px 0px -8px 0px;color: red;'>${err.message}</center></p>`;
		  }
		}
	},
	saveMymailit:function(e){
		// this.mainblock=false;
		// this.mailchimp=true;
		if (this.mymailit_apikey.length>0 &&this.mymailit_title.length>0) {
			var thisvue = this;
			this.err="";
			var data={"mymailit":1,"autotype":"mymailit","apikey":this.mymailit_apikey,"listid":"null","apiurl":"null","appid":"null","accesstoken":"null","campid":"null","email":this.mymailit_email,"title":this.mymailit_title,"autoid":this.autoid};
			// alert(data);
			e.target.disabled=true;
			request.postRequestCb('req.php',data,function(result){
				e.target.disabled=false;
					if(result.trim()=='1')
					{
						thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+this.t("API Details Saved Successfully")+"</center></p>";
						// alert(err);
					}
					else
					{
						thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
						// alert(err);
						// alert("Status:"+result.trim());
					}
			});
			}
			else{
				this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t("Please Fill All Details.")+"</center></p>";
			}
	},
	saveMailwizz:function(e){
		console.log('saving mailwizz');
		if (this.mailwizz_title.length>0 && this.mailwizz_apipublic.length>0 && this.mailwizz_apiprivate.length>0 && this.mailwizz_apiurl.length>0 && this.mailwizz_email.length>0) {
			var thisvue = this;
			this.err="";
			var data={"mailwizz":1,"autotype":"mailwizz","apikey":this.mailwizz_apipublic,"apiprivate":this.mailwizz_apiprivate,"apiurl":this.mailwizz_apiurl,"listid":this.mailwizz_listid,"appid":"null","campid":"null","accesstoken":this.mailwizz_apiprivate,"email":this.mailwizz_email,"title":this.mailwizz_title,"autoid":this.autoid};
			console.log(data);
			e.target.disabled=true;
			request.postRequestCb('req.php',data,function(result){
				e.target.disabled=false;
				if(result.trim()=='1')
					{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+this.t("API Details Saved Successfully")+"</center></p>";
					// alert(err);
					}
				    else
					{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
					// alert(err);
					// alert("Status:"+result.trim());
					}
			});
		} else {
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t("Please Fill All Details.")+"</center></p>";
		}
	},
	saveMoosend:function(e){
		// this.mainblock=false;
		// this.mailchimp=true;
		if (this.moos_apikey.length>0 && this.moos_listid.length>0 && this.moos_title.length>0) {
		var thisvue = this;
		this.err="";
		var data={"moosend":1,"autotype":"moosend","apikey":this.moos_apikey,"listid":this.moos_listid,"apiurl":"null","appid":"null","accesstoken":"null","campid":"null","email":this.moos_email,"title":this.moos_title,"autoid":this.autoid};
		// alert(data);
		e.target.disabled=true;
		request.postRequestCb('req.php',data,function(result){
			e.target.disabled=false;
				if(result.trim()=='1')
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+this.t("API Details Saved Successfully")+"</center></p>";
				// alert(err);
				}
			    else
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
				// alert(err);
				// alert("Status:"+result.trim());
				}
		});
		}
		else{
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t("Please Fill All Details.")+"</center></p>";
		}
	},
	saveSendiio:function(e){
		// this.mainblock=false;
		// this.mailchimp=true;
		if (this.sendiio_apikey.length>0 && this.sendiio_listid.length>0 && this.sendiio_title.length>0) {
		var thisvue = this;
		this.err="";
		var data={"sendiio":1,"autotype":"sendiio","apikey":this.sendiio_apikey,"listid":this.sendiio_listid,"apiurl":"null","appid":"null","accesstoken":this.sendiio_secret,"campid":"null","email":this.sendiio_email,"title":this.sendiio_title,"autoid":this.autoid};
		// alert(data);
		e.target.disabled=true;
		request.postRequestCb('req.php',data,function(result){
			e.target.disabled=false;
				if(result.trim()=='1')
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+this.t("API Details Saved Successfully")+"</center></p>";
				// alert(err);
				}
			    else
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
				// alert(err);
				// alert("Status:"+result.trim());
				}
		});
		}
		else{
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t('Please Fill All Details.')+"</center></p>";
		}
	},
	saveMautic:function(e){
		// this.mainblock=false;
		// this.mailchimp=true;
		if (this.mautic_api_key.length>0 && this.mautic_api_secret.length>0 && this.mautic_apiurl.length>0 && this.mautic_email.length>0) {
		var thisvue = this;
		this.err="";
		var data={"mautic":1,"autotype":"mautic","apikey":this.mautic_api_secret,"listid":this.mautic_api_listid,"apiurl":this.mautic_apiurl,"appid":this.mautic_api_key,"accesstoken":this.mautic_access_token,"campid":this.mautic_auth_type,"email":this.mautic_email,"title":this.mautic_title,"autoid":this.autoid};
		// alert(data);
		e.target.disabled=true;
		request.postRequestCb('req.php',data,function(result){
			e.target.disabled=false;
				if(result.trim()=='1')
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+this.t("API Details Saved Successfully")+"</center></p>";
				// alert(err);
				}
			    else
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
				// alert(err);
				// alert("Status:"+result.trim());
				}
		});
		}
		else{
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t("Please Fill All Details.")+"</center></p>";
		}
	},
	saveMailerlite:function(e){
		if (this.mailerlite_apikey.length>0 && this.mailerlite_title.length>0 && this.mailerlite_listid.length > 0 ) {
			var thisvue = this;
			this.err="";
			var data={"mailerlite":1,"autotype":"mailerlite","apikey":this.mailerlite_apikey,"listid":this.mailerlite_listid,"apiurl":"null","appid":"null","accesstoken":"null","campid":"null","email":this.mailerlite_email,"title":this.mailerlite_title,"autoid":this.autoid};
			// alert(data);
			e.target.disabled=true;
			request.postRequestCb('req.php',data,function(result){
				e.target.disabled=false;
					if(result.trim()=='1')
					{
						thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("API Details Saved Successfully")+"</center></p>";
						// alert(err);
					}
					else
					{
						thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
						// alert(err);
						// alert("Status:"+result.trim());
					}
			});
			}
			else{
				this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
			}
	},
	saveMailEngine:function(e){
		if (this.mailengine_title.length>0 && this.mailengine_apiurl.length>0 && this.mailengine_apikey.length>0) {
		var thisvue = this;
		this.err="";
		var data={"mailengine":1,"autotype":"mailengine","apikey":this.mailengine_apikey,"apiurl":this.mailengine_apiurl,"listid":this.mailengine_listid,"appid":"null","campid":"null","accesstoken":"null","email":this.mailengine_email,"title":this.mailengine_title,"autoid":this.autoid};
		// alert(data);
		e.target.disabled=true;
		request.postRequestCb('req.php',data,function(result){
			e.target.disabled=false;
			if(result.trim()=='1')
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+this.t("API Details Saved Successfully")+"</center></p>";
				// alert(err);
				}
			    else
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
				// alert(err);
				// alert("Status:"+result.trim());
				}
		});
		}
		else{
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t("Please Fill All Details.")+"</center></p>";
		}
	},
	saveactivecampaign:function(e){
		if (this.active_title.length>0 && this.active_apiurl.length>0 && this.active_apikey.length>0) {
		var thisvue = this;
		this.err="";
		var data={"activecampaignn":1,"autotype":"activecampaign","apikey":this.active_apikey,"apiurl":this.active_apiurl,"listid":this.active_listid,"appid":"null","campid":"null","accesstoken":"null","email":"null","title":this.active_title,"autoid":this.autoid};
		// alert(data);
		e.target.disabled=true;
		request.postRequestCb('req.php',data,function(result){
			e.target.disabled=false;
			if(result.trim()=='1')
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+this.t("API Details Saved Successfully")+"</center></p>";
				// alert(err);
				}
			    else
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
				// alert(err);
				// alert("Status:"+result.trim());
				}
		});
		}
		else{
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+this.t("Please Fill All Details.")+"</center></p>";
		}
	},
	savemailchimp:function(e){
		// this.mainblock=false;
		// this.mailchimp=true;
		if (this.mail_apikey.length>0 && this.mail_listid.length>0 && this.mail_title.length>0) {
		var thisvue = this;
		this.err="";
		var data={"mailchimp":1,"autotype":"mailchimp","apikey":this.mail_apikey,"listid":this.mail_listid,"apiurl":"null","appid":"null","accesstoken":"null","campid":"null","email":this.mail_email,"title":this.mail_title,"autoid":this.autoid};
		// alert(data);
		e.target.disabled=true;
		request.postRequestCb('req.php',data,function(result){
			e.target.disabled=false;
				if(result.trim()=='1')
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("API Details Saved Successfully")+"</center></p>";
				// alert(err);
				}
			    else
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
				// alert(err);
				// alert("Status:"+result.trim());
				}
		});
		}
		else{
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},
	savegetresponse:function(e){
		// this.mainblock=false;
		// this.getresponse=true;
		if (this.get_apikey.length>0 && this.get_campaignid.length>0 && this.get_title.length>0) {
		var thisvue = this;
		this.err="";
		var data={"getresponse":1,"autotype":"getresponse","apikey":this.get_apikey,"listid":"null","apiurl":"null","accesstoken":"null","appid":"null","campid":this.get_campaignid,"email":this.get_email,"title":this.get_title,"autoid":this.autoid};
		// alert(data);
		e.target.disabled=true;
		request.postRequestCb('req.php',data,function(result){
			e.target.disabled=false;
				if(result.trim()=='1')
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("API Details Saved Successfully")+"</center></p>";
				// alert(err);
				}
			    else
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
				// alert(err);
				// alert("Status:"+result.trim());
				}
		});
		}
		else{
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},
	saveconstantcont:function(e){
		// this.mainblock=false;
		// this.constantcont=true;
		if (this.const_apikey.length>0 && this.const_token.length>0 && this.const_listid.length>0 && this.const_email.length>0 && this.const_title.length>0) {
		var thisvue = this;
		this.err="";
		var data={"constantcont":1,"autotype":"constantcont","apikey":this.const_apikey,"listid":this.const_listid,"apiurl":"null","appid":"null","campid":"null","accesstoken":this.const_token,"email":this.const_email,"title":this.const_title,"autoid":this.autoid};
		// alert(data);
		e.target.disabled=true;
		request.postRequestCb('req.php',data,function(result){
			e.target.disabled=false;
				if(result.trim()=='1')
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("API Details Saved Successfully")+"</center></p>";
				// alert(err);
				}
			    else
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
				// alert(err);
				// alert("Status:"+result.trim());
				}
		});
		}
		else{
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},
	saveontraport:function(e)
	{
		// this.mainblock=false;
		// this.ontraport=true;
		if (this.ontra_apikey.length>0 && this.ontra_appid.length>0 && this.ontra_title.length>0) {
		var thisvue = this;
		this.err="";
		var data={"ontraport":1,"autotype":"ontraport","apikey":this.ontra_apikey,"listid":"null","apiurl":"null","campid":"null","accesstoken":"null","appid":this.ontra_appid,"email":this.ontra_email,"title":this.ontra_title,"autoid":this.autoid};
		// alert(data);
		e.target.disabled=true;
		request.postRequestCb('req.php',data,function(result){
			e.target.disabled=false;
				if(result.trim()=='1')
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("API Details Saved Successfully")+"</center></p>";
				// alert(err);
				}
			    else
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
				// alert(err);
				// alert("Status:"+result.trim());
				}
		});
		}
		else{
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},
	savehubspot:function(e)
	{
		// this.mainblock=false;
		// this.hubspot=true;
		if (this.hub_apikey.length>0 && this.hub_title.length>0 && this.hub_email.length>0) {
		var thisvue = this;
		this.err="";
		var data={"hubspot":1,"autotype":"hubspot","apikey":this.hub_apikey,"listid":"null","apiurl":"null","campid":"null","accesstoken":"null","appid":"null","email":this.hub_email,"title":this.hub_title,"autoid":this.autoid};
		// alert(data);
		e.target.disabled=true;
		request.postRequestCb('req.php',data,function(result){
			e.target.disabled=false;
				if(result.trim()=='1')
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("API Details Saved Successfully")+"</center></p>";
				// alert(err);
				}
			    else
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
				// alert(err);
				// alert("Status:"+result.trim());
				}
		});
		}
		else{
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}	
	},
	saveaweber:function(e){
		if (this.cus_email.length>0 && this.acc_id.length>0 && this.list_id.length>0 && this.cus_title.length>0) {
		var thisvue = this;	
		this.err="";
		var data={"aweber":1,"autotype":"aweber","apikey":"null","listid":this.list_id,"apiurl":"null","campid":"null","accesstoken":"","appid":this.acc_id,"email":this.cus_email,"title":this.cus_title,"autoid":this.autoid};
		// alert(data);
		e.target.disabled=true;
		request.postRequestCb('req.php',data,function(result){
			e.target.disabled=false;
				if(result.trim()=='1')
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("API Details Saved Successfully")+"</center></p>";
				// alert(err);
				}
			    else
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
				// alert(err);
				// alert("Status:"+result.trim());
				}
		});
		}
		else{
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},
	savePursueapp:function(e){
		if (this.pursueapp_title.length>0 && this.pursueapp_email.length>0 && this.pursueapp_listid.length>0 && this.pursueapp_apikey.length>0) {
		var thisvue = this;	
		this.err="";
		var data={"pursueapp":1,"autotype":"pursueapp","apikey":this.pursueapp_apikey,"listid":this.pursueapp_listid,"email":this.pursueapp_email,"autoid":this.autoid,"apiurl":"null","appid":"null","accesstoken":"null","campid":"null","title":this.pursueapp_title};
		//alert(data);
		e.target.disabled=true;
		request.postRequestCb('req.php',data,function(result){
			e.target.disabled=false;
			console.log(result);
				if(result.trim()=='1')
				{
								//	alert("Status:"+result.trim());

				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("API Details Saved Successfully")+"</center></p>";
				//alert(err);
				}
			    else
				{
				thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Failed To Save API Credentials. Please use correct API Credentials.")+"</center></p>";	
				//alert(result);
				//alert("Status:"+result.trim());
				}
		});
		}
		else{
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	}

		}
});


