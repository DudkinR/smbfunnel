var request=new ajaxRequest();
var payments=new Vue(
{  
	el:"#vuepayment",
	data:{
		payid:"",payname:"",
		baserow:true,err:"",
		paypal:false,pclient_id:"",pclient_secret:"",ptitle:"",ptax:"0",
		stripe:false,sclient_id:"",sclient_secret:"",stitle:"",stax:"0",
		payu:false,payuclient_id:"",payu_salt:"",payu_title:"",payu_tax:"",payu_type:"1",
		instamojo:false,imtitle:"",imclient_id:"",imclient_secret:"",imclient_salt:"",imtax:"0",
		authorizenet:false,aclient_id:"",aclient_secret:"",atitle:"",atax:"0",
		razorpay:false,rpkey_id:"",rpsecret_key:"",rptitle:"",rptax:"0",
		ccavenue:false,ccav_title:"",ccav_client_id:"",ccav_client_secret:"",ccav_client_salt:"",ccav_tax:"0",ccav_type:"1",
		midtrans:false,mid_title:"",mid_client_id:"",mid_client_secret:"",mid_client_salt:"",mid_tax:"0",mid_type:"1",
		toyyibpay:false,toy_title:"",toy_client_id:"",toy_client_secret:"",toy_tax:"",toy_type:"1",
		xendit:false,xndtitle:"",xndsecret_key:"",xndpublic_key:"",xndtax:"0",

		jvzoo_ipn:false,ititle:"",iclient_id:"N/A",iclient_secret:"",itax:"N/A",
		warriorplus_ipn:false,paykickstart_ipn:false,paydotcom_ipn:false,
		thrivecart_ipn:false,clickbank_ipn:false,
	}, 
	mounted:function(){
	 	document.onreadystatechange = () => { 
    if (document.readyState == "complete") {
		document.getElementById("editformcontainer").style.display="block";  
        this.editrecords();
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
		editrecords:function(){
			this.err="";
			var thisvue=this;
			if(document.getElementById('payname') && document.getElementById('payname').value){
		this.payname = document.getElementById('payname').value;
		if (this.payname == "paypal") {
		this.payid = document.getElementById('payid').value;
		this.ptitle = document.getElementById('paytitle').value;
		this.pclient_id = document.getElementById('payclientid').value;
        this.pclient_secret = document.getElementById('payclientsec').value;
        this.ptax = document.getElementById('paytax').value;
		this.paypal=true;
		}
		else if(this.payname == "stripe"){
		this.payid = document.getElementById('payid').value;
		this.stitle = document.getElementById('paytitle').value;
		this.sclient_id = document.getElementById('payclientid').value;
        this.sclient_secret = document.getElementById('payclientsec').value;
        this.stax = document.getElementById('paytax').value;
		this.stripe=true;
		}
		else if(this.payname == "authorize.net"){
		this.payid = document.getElementById('payid').value;
		this.atitle = document.getElementById('paytitle').value;
		this.aclient_id = document.getElementById('payclientid').value;
        this.aclient_secret = document.getElementById('payclientsec').value;
        this.atax = document.getElementById('paytax').value;
		this.authorizenet=true;
		}
		else if(this.payname=="instamojo")
		{
		this.payid = document.getElementById('payid').value;	
		this.imtitle = document.getElementById('paytitle').value;
		this.imclient_id = document.getElementById('payclientid').value;
        this.imclient_secret = document.getElementById('payclientsec').value;
        this.imclient_salt = document.getElementById('paysalt').value;
        this.imtax = document.getElementById('paytax').value;
        
        this.instamojo=true;
		}
		else if(this.payname=="payu")
		{
			this.payid = document.getElementById('payid').value;	
			this.payu_title = document.getElementById('paytitle').value;
			this.payuclient_id= document.getElementById('payclientid').value;
        	this.payu_salt = document.getElementById('paysalt').value;
			this.payu_tax= document.getElementById('paytax').value;
			this.payu_type=document.getElementById('pay_type').value;
        
        	this.payu=true;
		}
		else if(this.payname == "razorpay")
		{
			this.payid = document.getElementById('payid').value;
			this.rptitle = document.getElementById('paytitle').value;
			this.rpkey_id = document.getElementById('payclientid').value;
			this.rpsecret_key = document.getElementById('payclientsec').value;
			this.rptax = document.getElementById('paytax').value;
			this.razorpay=true;
		}
		else if(this.payname=="ccavenue")
		{
			this.payid = document.getElementById('payid').value;	
			this.ccav_title = document.getElementById('paytitle').value;
			this.ccav_client_id = document.getElementById('payclientid').value;
			this.ccav_client_secret = document.getElementById('payclientsec').value;
			this.ccav_client_salt = document.getElementById('paysalt').value;
			this.ccav_tax = document.getElementById('paytax').value;
			this.ccav_type=document.getElementById('pay_type').value;
			this.ccavenue=true;
		}
		else if(this.payname == "xendit"){
			this.payid = document.getElementById('payid').value;
			this.xndtitle = document.getElementById('paytitle').value;
			this.xndpublic_key=document.getElementById("payclientid").value;
			this.xndsecret_key = document.getElementById('payclientsec').value;
			this.xndtax = document.getElementById('paytax').value;
			this.xendit=true;
		}
		else if(this.payname=="jvzoo_ipn")
		{
		this.payid = document.getElementById('payid').value;	
	   	this.ititle = document.getElementById('paytitle').value;	
		this.iclient_secret = document.getElementById('payclientsec').value;	
        this.jvzoo_ipn=true;
		}
		else if(this.payname=="warriorplus_ipn")
		{
		this.payid = document.getElementById('payid').value;	
		this.ititle = document.getElementById('paytitle').value;	
		this.iclient_secret = document.getElementById('payclientsec').value;	
        this.warriorplus_ipn=true;
		}
		else if(this.payname=="paykickstart_ipn")
		{
		this.payid = document.getElementById('payid').value;	
		this.ititle = document.getElementById('paytitle').value;		
		this.iclient_secret = document.getElementById('payclientsec').value;	
        this.paykickstart_ipn=true;
		}
		else if(this.payname=="paydotcom_ipn")
		{
		this.payid = document.getElementById('payid').value;	
		this.ititle = document.getElementById('paytitle').value;			
		this.iclient_secret = document.getElementById('payclientsec').value;	
        this.paydotcom_ipn=true;
		}
		else if(this.payname=="thrivecart_ipn")
		{
		this.payid = document.getElementById('payid').value;	
		this.ititle = document.getElementById('paytitle').value;			
		this.iclient_secret = document.getElementById('payclientsec').value;	
        this.thrivecart_ipn=true;
		}
		else if(this.payname=="clickbank_ipn")
		{
		this.payid = document.getElementById('payid').value;	
		this.ititle = document.getElementById('paytitle').value;			
		this.iclient_secret = document.getElementById('payclientsec').value;	
        this.clickbank_ipn=true;
		}
		else if(this.payname=="midtrans")
		{
			this.payid = document.getElementById('payid').value;	
			this.mid_title = document.getElementById('paytitle').value;
			this.mid_client_id = document.getElementById('payclientid').value;
			this.mid_client_secret = document.getElementById('payclientsec').value;
			this.mid_client_salt = document.getElementById('paysalt').value;
			this.mid_tax = document.getElementById('paytax').value;
			this.mid_type=document.getElementById('pay_type').value;
			this.midtrans=true;
		}
		else if(this.payname=="toyyibpay")
		{
			this.payid = document.getElementById('payid').value;	
			this.toy_title = document.getElementById('paytitle').value;
			this.toy_client_id = document.getElementById('payclientid').value;
			this.toy_client_secret = document.getElementById('payclientsec').value;
			this.toy_tax = document.getElementById('paytax').value;
			this.toy_type=document.getElementById('pay_type').value;
			this.toyyibpay=true;
		}
		try{
			doEscapePopup(function(){thisvue.closeForm(thisvue.payname);});
		}catch(err){}
	}
		},
		showForm:function(paymenttype){
		// this.mainblock=true;
		this.err="";
		var thisvue=this;
		if (paymenttype == "paypal") {
			this.paypal=true;
			this.stripe=false;
			this.authorizenet=false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "stripe") {
			this.stripe = true;
			this.paypal = false;
			this.authorizenet = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "payu") {
			this.stripe = false;
			this.paypal = false;
			this.authorizenet = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;
			this.instamojo=false;
			this.payu=true;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "authorizenet") {
			this.authorizenet = true;
			this.paypal = false;
			this.stripe = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;this.thrivecart_ipn=false;this.clickbank_ipn=false;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "jvzoo_ipn") {
			this.authorizenet = false;
			this.paypal = false;
			this.stripe = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=true;this.thrivecart_ipn=false;this.clickbank_ipn=false;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "warriorplus_ipn") {
			this.authorizenet = false;
			this.paypal = false;
			this.stripe = false;
			this.warriorplus_ipn=true;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;this.thrivecart_ipn=false;this.clickbank_ipn=false;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "paykickstart_ipn") {
			this.authorizenet = false;
			this.paypal = false;
			this.stripe = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=true;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;this.thrivecart_ipn=false;this.clickbank_ipn=false;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "paydotcom_ipn") 
		{
			this.authorizenet = false;
			this.paypal = false;
			this.stripe = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=true;
			this.jvzoo_ipn=false;this.thrivecart_ipn=false;this.clickbank_ipn=false;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "thrivecart_ipn") 
		{
			this.authorizenet = false;
			this.paypal = false;
			this.stripe = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;this.thrivecart_ipn=true;this.clickbank_ipn=false;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "clickbank_ipn") 
		{
			this.authorizenet = false;
			this.paypal = false;
			this.stripe = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;this.thrivecart_ipn=false;this.clickbank_ipn=true;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "instamojo") 
		{
			this.authorizenet = false;
			this.paypal = false;
			this.stripe = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;this.thrivecart_ipn=false;this.clickbank_ipn=false;
			this.instamojo=true;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "razorpay") 
		{
			this.authorizenet = false;
			this.paypal = false;
			this.stripe = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;this.thrivecart_ipn=false;this.clickbank_ipn=false;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=true;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "ccavenue") 
		{
			this.authorizenet = false;
			this.paypal = false;
			this.stripe = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;this.thrivecart_ipn=false;this.clickbank_ipn=false;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=true;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "midtrans") 
		{
			this.authorizenet = false;
			this.paypal = false;
			this.stripe = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;this.thrivecart_ipn=false;this.clickbank_ipn=false;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=true;
			this.toyyibpay=false;
			this.xendit=false;
		}
		else if (paymenttype == "toyyibpay") 
		{
			this.authorizenet = false;
			this.paypal = false;
			this.stripe = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;this.thrivecart_ipn=false;this.clickbank_ipn=false;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=true;
			this.xendit=false;
		}
		else if (paymenttype == "xendit") 
		{
			this.authorizenet = false;
			this.paypal = false;
			this.stripe = false;
			this.warriorplus_ipn=false;this.paykickstart_ipn=false;this.paydotcom_ipn=false;
			this.jvzoo_ipn=false;this.thrivecart_ipn=false;this.clickbank_ipn=false;
			this.instamojo=false;
			this.payu=false;
			this.razorpay=false;
			this.ccavenue=false;
			this.midtrans=false;
			this.toyyibpay=false;
			this.xendit=true;
		}
		
		try{
			doEscapePopup(function(){thisvue.closeForm(paymenttype);});
		}catch(err){}
	},
	closeForm:function(type){
		if (type == "paypal") {
			this.paypal=false;
		}
		else if(type == "stripe"){
			this.stripe=false;
		}
		else if(type == "authorizenet"){
			this.authorizenet=false;
		}
		else if(type == "jvzoo_ipn"){
			this.jvzoo_ipn=false;
		}
		else if(type == "warriorplus_ipn"){
			this.warriorplus_ipn=false;
		}
		else if(type == "paykickstart_ipn"){
			this.paykickstart_ipn=false;
		}
		else if(type == "paydotcom_ipn"){
			this.paydotcom_ipn=false;
		}
		else if(type == "thrivecart_ipn"){
			this.thrivecart_ipn=false;
		}
		else if(type=="clickbank_ipn")
		{
			this.clickbank_ipn=false;
		}
		else if(type=="instamojo")
		{
			this.instamojo=false;
		}
		else if(type=="payu")
		{
			this.payu=false;
		}
		else if(type=="razorpay")
		{
			this.razorpay=false;
		}
		else if(type=="ccavenue")
		{
			this.ccavenue=false;
		}
		else if(type=="midtrans")
		{
			this.midtrans=false;
		}
		else if(type=="toyyibpay")
		{
			this.toyyibpay=false;
		}
		else if(type=="xendit")
		{
			this.xendit=false;
		}
	},
	saveIPN:function(type){
		try
		{
		thisvue=this;
		if(this.ititle.trim().length>1)
		{
			var sdata={"payment":1,"paymenttype":type,"title":this.ititle,"clientid":this.iclient_id,"clientsecret":this.iclient_secret,"tax":this.itax,"payid":this.payid};
			request.postRequestCb('req.php',sdata,function(data){
				if(data.trim()=='1')
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("Records Saved Successfully.")+"</center></p>";
					setTimeout(function(){
					thisvue[type]=false;},500);
				}
				else
				{
					thisvue.err=data.trim();
				}
			});
		}
		else
		{
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details")+"</center></p>";
		}
		}
		catch(errr){this.err=errr.message;}
	},
	savepaypal:function(){
		if (this.pclient_id.length>0 && this.pclient_secret.length>0 && this.ptitle.length>0 && this.ptax.length>0) {
		var thisvue=this;
		this.err="";
		var data={"payment":1,"paymenttype":"paypal","title":this.ptitle,"clientid":this.pclient_id,"clientsecret":this.pclient_secret,"tax":this.ptax,"payid":this.payid};
		request.postRequestCb('req.php',data,function(result){
			// alert(result);
			if(result.trim() == '1')
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("Records Saved Successfully.")+"</center></p>";
					setTimeout(function(){thisvue.paypal=false;},500);
				}
			    else
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Record Already Exists.")+"</center></p>";
				}
		});
		}
		else{   
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t('Please Fill All Details.')+"</center></p>";
		}
	},
	saveXendit:function(){
		if (this.xndtitle.length>0 && this.xndsecret_key.length>0) {
		var thisvue=this;
		this.err="";
		var data={"payment":1,"paymenttype":"xendit",'clientid':this.xndpublic_key,"title":this.xndtitle, "clientsecret":this.xndsecret_key,"tax":this.xndtax,"payid":this.payid};
		request.postRequestCb('req.php',data,function(result){
			if(result.trim() == '1')
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("Records Saved Successfully.")+"</center></p>";
					setTimeout(function(){thisvue.xendit=false;},500);
				}
			    else
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Record Already Exists.")+"</center></p>";
				}
		});
		}
		else{   
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},
	saveccavenue:function(){
		if (this.ccav_client_id.length>0 && this.ccav_client_secret.length>0 && this.ccav_title.length>0 && this.ccav_client_salt.length>0 && this.ccav_tax.length>0 && this.ccav_type.length>0) {
		var thisvue=this;
		this.err="";
		var data={"payment":1,"paymenttype":"ccavenue","title":this.ccav_title,"clientid":this.ccav_client_id,
		"clientsecret":this.ccav_client_secret,"tax":this.ccav_tax,"salt":this.ccav_client_salt,"pay_type":this.ccav_type,"payid":this.payid};
		request.postRequestCb('req.php',data,function(result){
			// alert(result);
			if(result.trim() == '1')
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("Records Saved Successfully.")+"</center></p>";
					setTimeout(function(){thisvue.ccavenue=false;},500);
				}
			    else
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Record Already Exists.")+"</center></p>";
				}
		});
		}
		else{   
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},
	savemidtrans:function(){
		if (this.mid_client_id.length>0 && this.mid_client_secret.length>0 && this.mid_title.length>0 && this.mid_client_salt.length>0 && this.mid_tax.length>0 && this.mid_type.length>0) {
		var thisvue=this;
		this.err="";
		var data={"payment":1,"paymenttype":"midtrans","title":this.mid_title,"clientid":this.mid_client_id,
		"clientsecret":this.mid_client_secret,"tax":this.mid_tax,"salt":this.mid_client_salt,"pay_type":this.mid_type,"payid":this.payid};
		request.postRequestCb('req.php',data,function(result){
			if(result.trim() == '1')
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("Records Saved Successfully.")+"</center></p>";
					setTimeout(function(){thisvue.midtrans=false;},500);
				}
			    else
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Record Already Exists.")+"</center></p>";
				}
		});
		}
		else{   
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},
	savetoyyibpay:function(){
		if (this.toy_client_id.length>0 && this.toy_client_secret.length>0 && this.toy_title.length>0  && this.toy_tax.length>0 && this.toy_type.length>0) {
		var thisvue=this;
		this.err="";
		var data={"payment":1,"paymenttype":"toyyibpay","title":this.toy_title,"clientid":this.toy_client_id,"clientsecret":this.toy_client_secret,"tax":this.toy_tax,"salt":"","pay_type":this.toy_type,"payid":this.payid};
		request.postRequestCb('req.php',data,function(result){
			if(result.trim() == '1')
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("Records Saved Successfully.")+"</center></p>";
					setTimeout(function(){thisvue.toyyibpay=false;},500);
				}
			    else
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Record Already Exists.")+"</center></p>";
				}
		});
		}
		else{   
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},
	savePayu:function(){
		if (this.payuclient_id.length>0 && this.payu_salt.length>0 && this.payu_title.length>0 && this.payu_tax.length>0) {
		var thisvue=this;
		this.err="";
		var data={"payment":1,"paymenttype":"payu","title":this.payu_title,"clientid":this.payuclient_id,"salt":this.payu_salt,"tax":this.payu_tax,"pay_type":this.payu_type,"payid":this.payid};
		request.postRequestCb('req.php',data,function(result){
			//alert(result);
			if(result.trim() == '1')
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("Records Saved Successfully.")+"</center></p>";
					setTimeout(function(){thisvue.paypal=false;},500);
				}
			    else
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Record Already Exists.")+"</center></p>";
				}
		});
		}
		else{   
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},
	savestripe:function(){
		if (this.sclient_id.length>0 && this.sclient_secret.length>0 && this.stitle.length>0) {
		var thisvue=this;
		this.err="";
		var data={"payment":1,"paymenttype":"stripe","title":this.stitle,"clientid":this.sclient_id,"clientsecret":this.sclient_secret,"tax":this.stax,"payid":this.payid};
		request.postRequestCb('req.php',data,function(result){
			// alert(result);
			if(result.trim() == '1')
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("Records Saved Successfully.")+"</center></p>";
					setTimeout(function(){thisvue.stripe=false;},500);
				}
			    else
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Record Already Exists.")+"</center></p>";
				}
		});
		}
		else{   
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},
	saveauthorizenet:function(){
		if (this.aclient_id.length>0 && this.aclient_secret.length>0 && this.atitle.length>0) {
		var thisvue=this;
		this.err="";
		var data={"payment":1,"paymenttype":"authorize.net","title":this.atitle,"clientid":this.aclient_id,"clientsecret":this.aclient_secret,"tax":this.atax,"payid":this.payid};

		request.postRequestCb('req.php',data,function(result){
			// alert(result);
			if(result.trim() == '1')
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("Records Saved Successfully.")+"</center></p>";
					setTimeout(function(){thisvue.authorizenet=false;},500);
				}
			    else
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Record Already Exists.")+"</center></p>";
				}
		});
		}
		else{   
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},
	saverazorpay:function(){
		if (this.rpkey_id.length>0 && this.rpsecret_key.length>0 && this.rptitle.length>0) {
		var thisvue=this;
		this.err="";
		var data={"payment":1,"paymenttype":"razorpay","title":this.rptitle,"clientid":this.rpkey_id,"clientsecret":this.rpsecret_key,"tax":this.rptax,"payid":this.payid};
		request.postRequestCb('req.php',data,function(result){
			// alert(result);
			if(result.trim() == '1')
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>Records Saved Successfully.</center></p>";
					setTimeout(function(){thisvue.razorpay=false;},500);
				}
			    else
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Record Already Exists.")+"</center></p>";
				}
		});
		}
		else{   
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},
	saveinstamojo:function(){
		if (this.imclient_id.length>0 && this.imclient_secret.length>0 && this.imtitle.length>0 && this.imclient_salt.length>0 && this.imtax.length>0) {
		var thisvue=this;
		this.err="";
		var data={"payment":1,"paymenttype":"instamojo","title":this.imtitle,"clientid":this.imclient_id,
		"clientsecret":this.imclient_secret,"tax":this.imtax,"salt":this.imclient_salt,"payid":this.payid};
		request.postRequestCb('req.php',data,function(result){
			// alert(result);
			if(result.trim() == '1')
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: rgb(72, 161, 79);'>"+t("Records Saved Successfully.")+"</center></p>";
					setTimeout(function(){thisvue.instamojo=false;},500);
				}
			    else
				{
					thisvue.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Record Already Exists.")+"</center></p>";
				}
		});
		}
		else{   
			this.err="<p><center style='margin: -17px 0px -8px 0px;color: red;'>"+t("Please Fill All Details.")+"</center></p>";
		}
	},

}
});