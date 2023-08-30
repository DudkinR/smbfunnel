var request=new ajaxRequest();
var globalbtnhtml="";
var authcreate=new Vue(
{
	el:"#veuauththsection",
	data:{
		host:"localhost",db:"",user:"",pass:"",port:"",prefix:"cloud_funnels_",createtable:true,createuser:false,createsmtp:false,csrfval:"",err:"",step:0,username:"",email:"",userpass:"",repass:"",remember:false,autologin:0,
		fpwdstep:0,fpwdotp:"",
		refresh_needed:false,
		language_selected:false,
		current_language:"",
	},
	mounted:function(){this.createcsrf();},
	methods:
	{
	/*w:function(txt,arr=[]){
		w(txt,arr);
	},*/
	t:function(txt,arr=[]){
		return t(txt,arr);
	},
	refreshNeeded:function(assign=0){
		if(assign===0)
		{
		return this.refresh_needed;
		}
		else if(assign.indexOf("refresh")>=0)
		{
		this.refresh_needed=true;
		this.err="Please refresh the page and try again";
		}
	},
	createcsrf:function(){
		var instance=this;
		request.createCSRF(function(data){instance.csrfval=data;});
		},
	
	createConfig:function(e){
		//config create
		//e.target.disabled=true;
		if(this.refreshNeeded()){return;}
		var thisvue=this;
		this.err="";

		if(this.host.length>0&& this.user.length>0 &&this.prefix.length>0)
		{
		var data={"createconfig":1,"host":this.host,"user":this.user,"pass":this.pass,"dbname":this.db,"port":this.port,"pref":this.prefix,"token":this.csrfval};
		request.postRequestCb('req.php',data,function(res){
			thisvue.refreshNeeded(res);
			e.target.disabled=false;
			thisvue.createcsrf();
			if(res.trim()=='1')
				{
				thisvue.createtable=false;
				thisvue.createuser=true;
				++thisvue.step;
				thisvue.err="";
				}
			    else
				{
				thisvue.err="<div style='overflow-wrap:break-word;max-height:60px;max-width:100%;overflow-y:scroll;'><center>"+res.trim()+"</center></div>";	
				}
		});
		}
		else
		{
			setTimeout(function(){
			e.target.disabled=false;
			},500);
			this.err="<p><center>Please enter required data including a prefix</center></p>";
		}
    },
	
	createUser:function(e){
		//e.target.disabled=true;
		if(this.refreshNeeded()){return;}
		var thisvue=this;
		this.err="";
		var data={"createuser":1,"user-name":this.username,"user-email":this.email,"user-pass":this.userpass,"token":this.csrfval,"app_language":this.current_language};
		var passptrn_str=/[a-zA-z]+/;var passptrn_number=/[0-9]+/;var passptrn_spc=/[^a-zA-z0-9]+/;
		var emlpattern=/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		
		if(!(passptrn_str.test(this.userpass) && passptrn_number.test(this.userpass) && passptrn_spc.test(passptrn_spc) && this.userpass.length>=8))
		{
			this.err="Please use password with combination of alpha-numeric and special characters and minimum length should 8";
		}
		else if(this.userpass !=this.repass)
		{
			this.err="Password Not Matched";
		}
		else if(! emlpattern.test(this.email))
		{
			this.err="Invalid Email";
		}
		if(this.err.length<1)
		{
			request.postRequestCb('req.php',data,function(res)
			{
			thisvue.refreshNeeded(res);	
			e.target.disabled=false;	
			thisvue.createcsrf();
			if(res.trim()=='1')
				{
					++thisvue.step;
				    thisvue.err="";
					window.location="index.php?page=login";
				}
				else
				{
					thisvue.err="<div style='overflow-wrap:break-word;max-height:60px;max-width:100%;overflow-y:scroll;'><center>"+res.trim()+"</center></div>";
				}
			});
		}
		else
		{
			setTimeout(function(){
				e.target.disabled=false;
				},500);
		}
	},
	userLogin:function(e){
		//e.target.disabled=true;
		if(this.refreshNeeded()){return;}
		//var remember="";
		var thisvue=this;
		this.err="";

		var data={"admin_login":1,"email":this.email,"pass":this.userpass,"token":this.csrfval};
		if(this.remember)
		{
			data.remember=1;
		}

		request.postRequestCb('req.php',data,function(data){
			console.log(data);
			thisvue.refreshNeeded(data);
			e.target.disabled=false;
			if(data.trim()=='0')
			{
				thisvue.createcsrf();
				thisvue.err="Invalid Credentials";
			}
		else if(isJSON(data.trim()))
			{
				var data=isJSON(data.trim());
				if(thisvue.autologin==1)
					{
						window.opener = self;
                        window.close();
					}
					else
					{
				window.location="index.php?page="+data.redirect+"";
					}
			}
			else
			{
				thisvue.createcsrf();
				thisvue.err=data;
			}
			});
	},
	forgotPassword:function(e){
		if(this.refreshNeeded()){return;}
		var thisvue=this;
		this.err="";
		//e.target.disabled=true;
		if(this.fpwdstep==0)
		{
			var reqs={"admin_forgot_password":1,"email":this.email,"token":this.csrfval};
			//createcsrf();
			request.postRequestCb('req.php',reqs,function(data){
				thisvue.refreshNeeded(data);
				e.target.disabled=false;
				thisvue.createcsrf();
				if(data.trim()=='1'){++thisvue.fpwdstep;}
				else
				{thisvue.err=data.trim();}
			});
			
		}
		else if(this.fpwdstep==1)
		{
			var reqs={"admin_forgot_password":1,"otp":this.fpwdotp,"token":this.csrfval};
			request.postRequestCb('req.php',reqs,function(data){
				thisvue.refreshNeeded(data);
				e.target.disabled=false;
				thisvue.createcsrf();
				if(data.trim()=='1'){++thisvue.fpwdstep;}
				else
				{thisvue.err=data.trim();}
			});
		}
		else if(this.fpwdstep==2)
		{
			//e.target.disabled=true;
			var reqs={"admin_forgot_password":1,"pass":this.userpass,"repass":this.repass,"token":this.csrfval};
			
			var passptrn_str=/[a-zA-z]+/;var passptrn_number=/[0-9]+/;var passptrn_spc=/[^a-zA-z0-9]+/;
		
			if(!(passptrn_str.test(this.userpass) && passptrn_number.test(this.userpass) && passptrn_spc.test(passptrn_spc) && this.userpass.length>=8))
			{
			this.err="Please use password with combination of alpha-numeric and special characters and minimum length should 8";
			}
			else if(this.userpass !=this.repass)
			{
			this.err="Password Not Matched";
			}

			if(this.err.length<1)
			{
				request.postRequestCb('req.php',reqs,function(data){
				thisvue.refreshNeeded(data);
				e.target.disabled=false;
				thisvue.createcsrf();
				if(data.trim()=='1'){++thisvue.fpwdstep;
				e.target.display="none";
				setTimeout(function(){window.location="index.php?page=login";},1000);
				}
				else
				{thisvue.err=data.trim();}
			});
			}
			else
			{
				setTimeout(function(){
					e.target.disabled=false;
					},500);	
			}			
			
		}
	},
	}
});
function addProcesser(doc)
{
	globalbtnhtml=doc.innerHTML;
	doc.innerHTML='<span class="spinner-border spinner-border-sm"></span> Processing...';
	doc.disabled=true;
	var observer=new MutationObserver(function(m){
		//alert(m);
		//alert(doc.disabled);
		if(!doc.disabled)
		{
			observer.disconnect();
			doc.innerHTML=globalbtnhtml;
		}
	});
	observer.observe(doc,{attributes:true});
}

setTimeout(function(){
	document.getElementsByTagName("button")[0].onclick=function(e){
		try
		{
		var doc=e.target;
		if(doc.id=="language_selector")
		{return;}
		if(doc.disabled===undefined ||(!doc.disabled))
		{
			addProcesser(doc)
		}
		}catch(errrrr){console.log(errrrr.message);}
		}
},1000);

document.body.onkeypress=function(e){
	try{
		//console.log(e.target);
		if(e.which==13 || e.keyCode==13)
		{
			var doc=document.getElementsByTagName("button")[0];
			if(doc.disabled===undefined || (!doc.disabled))
			{
				doc.dispatchEvent(new Event('click'));
				//addProcesser(doc);
			}
		}
		}
		catch(errrr)
		{
			console.log(errrr.message);
		}
}