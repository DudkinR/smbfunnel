var request=new ajaxRequest();
var createlist= new Vue({
el:"#createlist",
	data:{
		quick_list_name:"",err:"",click_subs_name:"",click_subs_email:"",
		updatelistname:"",listid:"",updaterecord:"",searchemail:"",pagecount:"",
	},
	 mounted:function(){
		 try
		 {
		 if(document.getElementById("click-list-name").value !==undefined)
		 {
			 this.quick_list_name=document.getElementById("click-list-name").value;
		 }
		}catch(err){}
  },
	methods:{
	/*w:function(txt,arr=[]){
		w(txt,arr);
	},*/
	t:function(txt,arr=[]){
			return t(txt,arr);
	},
	createlists:function(){
		if (this.quick_list_name.length>0) {
		var thisvue=this;
		this.err="";
		var data={"createlist":1,"listname":this.quick_list_name};
		request.postRequestCb('req.php',data,function(result){
			if(result.trim() != '0')
				{
					window.location='index.php?page=createlist&listid='+result;

				}
			    else
				{
					thisvue.err="";
				}
		});
		}
		else{
			this.err="<p><center>"+this.t("Please enter valid list name.")+"</center></p>";
		}
	},
	savesubs:function(){
		var thisvue=this;
		this.listid = document.getElementById("click-listid").value;
		this.updatelistname = document.getElementById("click-list-name").value;
		this.click_subs_name = document.getElementById("click_subs_name").value;
		this.click_subs_email = document.getElementById("click_subs_email").value;
		this.pagecount = document.getElementById("clickpagecount").value;
		this.err="";
		var emlpattern=/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

		var updated_title=0;
		if(this.quick_list_name !==this.updatelistname)
		{
			++updated_title;
			var name_change_data ={"createlist":4,"listid":this.listid,"updated_listname":this.updatelistname};
			request.postRequestCb('req.php',name_change_data,function(data){
			});
		}

		if (this.click_subs_name.length<1) {
			this.err = "<p><center>"+this.t("Please enter valid name.")+"</center></p>";
		}
		else if(! emlpattern.test(this.click_subs_email))
		{
			this.err=`<p><center>${this.t("Invalid Email Format")} ${(updated_title)? " ("+this.t("List name updated successfuly")+")":""}</center></p>`;
			return;
		}
		else if(this.err.length<1){
			this.updated_title=0;
			this.updaterecord = document.getElementById("updaterecord").value;
		
			var data ={"createlist":2,"listid":this.listid,"username":this.click_subs_name,"email":this.click_subs_email,"updateuser":this.updaterecord,"pagecount":this.pagecount};

			// alert(JSON.stringify(data));
			// return;
			this.err = "<p><center><span style='color: #1f57ca !important;'>"+this.t("Loading...")+"</span></center></p>";
			request.postRequestCb('req.php',data,function(result){
			if(result.trim() !== '0')
				{
					location.reload();
				}
			    else
				{
					thisvue.err="<p><center>"+this.t("Record Already Exists")+"</center></p>";
				}
		});
		}
		if(updated_title>0)
		{
			thisvue.err="<center><p style='color:green !important;'>"+this.t("List Name Changed Successfuly.")+"</p></center>";
		}
	},
	searchemaillist:function(){
		this.searchemail = document.getElementById("searchpeople").value;
		this.listid = document.getElementById("searchlistid").value;
		// alert(this.searchemail);
		this.err="";
		var data={"createlist":3,"giventext":this.searchemail,"listid":this.listid};
		request.postRequestCb('req.php',data,function(result){
			// alert(result);
			if(result.trim() != '0')
				{
					$("#loadinglistqml").html(result);

				}
			    else
				{
					this.err="";
				}
		});
	},
},
});