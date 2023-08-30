var request=new ajaxRequest();
var products=new Vue({
	el:"#qfnlproducts",
	mounted:function(){
		this.allproducts=document.getElementById("qfblallproducts").value;
		this.allproducts=this.allproducts.trim();
        this.allproducts=JSON.parse(this.allproducts);
	},
	data: {
	id:0,
	title:"",
	productid:"",
	url:"",
	description:"",
	download_url: '',
	image: '',
	price:0,
	currency:"USD",
	shipping:0,
	subproducts:[],
	opproducts:[],
	tax:0,
	allproducts:[],
    allproductsid:[],
	doupdate:0,
	err:"",
	},
	methods:{
		/*w:function(txt,arr=[]){
		w(txt,arr);
		},*/
		t:function(txt,arr=[]){
			return t(txt,arr);
		},
		searchProduct:function(search)
		{
			//alert(search.target.value);
			var thisvue=this;
			var ob=new OnPageSearch(search.target.value,"#keywordsearchresult");
			ob.do_modify=false;
			ob.url=window.location.href;
			ob.minsearch_len=0;
			ob.cb=function(data)
			{
				//alert(data);
				var elem_ob=Vue.extend({
					template:"<tbody id='keywordsearchresult'>"+data+"</tbody>",
					methods:{
						openEditor:function(id,num=0){thisvue.openEditor(id,num)},
					}
				});
				try
				{
				var elem=new elem_ob().$mount();
				console.log(elem.$el);
				//qfnlproducts
				//keywordsearchresult
				document.getElementById("tableforsearch").replaceChild(elem.$el,document.getElementById("keywordsearchresult"));
				}
				catch(errr){
					console.log(errr.message);
				}
			};
			var data=ob.search();
		},
		container:function()
		{

		var head="<div class='overlay' ><div class='card pnl api-forms'><div class='card-header' style='font-size:16px !important;position:relative;'>"+this.t('Add Product')+"<i id='closetemplate' class='fa fa-times-circle float-right'></i></div><div class='card-body tmpltbdydiv' style='max-height:600px;overflow-y:auto;'>"
		var footer="</div><div class='card-footer'> <strong><span class='text-center' id='usrdatasaveerr' v-html='err'></span></strong><button id='updateuserdetail' class='btn theme-button float-right' v-on:click='saveSetting($event)'>"+this.t('Save')+"</button> </div></div></div>";
		var body="<div class='mb-3'>";
		body +="<div class='input-group input-group-sm mw120 mb-2'><div class='input-group-prepend'><span class='input-group-text'>"+this.t('Product Title')+"</span></div><input type='text' class='form-control form-control-sm' placeholder='"+this.t('Add Product title')+"' v-model='title'></div>";

		body +="<div class='input-group input-group-sm  mw120 mb-2'><div class='input-group-prepend'><span class='input-group-text'>"+this.t('Product Id')+"</span></div><input type='text' class='form-control form-control-sm' placeholder='"+this.t('Add Product Id')+"' v-model='productid'></div>";

		body +="<div class='input-group input-group-sm mw120 mb-2'><div class='input-group-prepend'><span class='input-group-text'>"+this.t('URL')+"</span></div><input type='url' placeholder='"+this.t('Add Product URL')+"' v-model='url' class='form-control form-control-sm'></div>";

		body +="<div class='input-group input-group-sm mw120 mb-2'><div class='input-group-prepend'><span class='input-group-text'>"+this.t('Download URL')+"</span></div><input type='url' placeholder='"+this.t('Add Product Download URL')+"' v-model='download_url' class='form-control form-control-sm'></div>";
		body +="<div class='input-group input-group-sm mw120 mb-2'><div class='input-group-prepend'><span class='input-group-text'>"+this.t('Product Image')+"</span></div><input type='url' placeholder='"+this.t('Add Product Image URL')+"' v-model='image' class='form-control form-control-sm'></div>";

		body +="<div class='input-group input-group-sm mw120 mb-2'><div class='input-group-prepend'><span class='input-group-text'>"+this.t('Price')+"</span></div><input type='number' class='form-control form-control-sm' placeholder='"+this.t('Product Price')+"' v-model='price'></div>";

		body +="<div class='input-group input-group-sm mw120 mb-2'><div class='input-group-prepend'><span class='input-group-text'>"+this.t('Currency')+"</span></div><input type='text' class='form-control form-control-sm' placeholder='"+this.t('Add Currency (Ex: USD)')+"' v-model='currency'></div>";

		body +="<div class='input-group input-group-sm mw120 mb-2'><div class='input-group-prepend'><span class='input-group-text'>"+this.t('Tax (In number)')+"</span></div><input type='number' class='form-control form-control-sm' placeholder='"+this.t('Add Tax')+"' v-model='tax'></div>";

		body +="<div class='input-group input-group-sm mw120 mb-2'><div class='input-group-prepend'><span class='input-group-text'>"+this.t('Shipping Cost')+"</span></div><input type='number' placeholder='"+this.t('Enter Shipping Cost')+"' class='form-control form-control-sm' v-model='shipping'></div>";

		body +="<label style='margin-top:6px;'>"+this.t('Product Description')+"</label><textarea class='form-control' v-model='description' placeholder='"+this.t('Enter Product Description')+"'></textarea>";

        var extras="<div class='alert alert-warning'>"+this.t('No Other Products Available')+"</div>";

            allproducts=this.allproducts;
		    if(allproducts.length>0)
			{
			extras="";
			}
			for(var i=0;i<allproducts.length;i++)
			{
				extras +="<div class='input-group'><div class='input-group-prepend'><span class='input-group-text'><input type='checkbox' value="+allproducts[i].id+"></span></div><p class='form-control'>(#"+allproducts[i].productid+") "+allproducts[i].title+"</p></div>";
			}

	    body +="<button data-bs-toggle='collapse' data-bs-target='.exfdatadiv' class='btn btn-outline-info  btn-block' style='margin-top:5px;'>"+this.t('Add Sub-Products')+"</button><div class='collapse exfdatadiv' id='subproducts'>"+extras+"</div>";


        body +="<button data-bs-toggle='collapse' data-bs-target='#opproducts' class='btn btn-outline-info  btn-block' style='margin-top:5px;'>"+this.t('Add Optional Products')+"</button><div class='collapse' id='opproducts'>"+extras+"</div>";


		body +="</div>";
		return head+body+footer;
},
openEditor:function(data,create=0){
	this.doupdate=0;
	if(create !=1)
	{
    try
    {
	var data=JSON.parse(atob(data));
	this.id=data.id;
	this.title=data.title;
	this.productid=data.productid;
	this.download_url=data.download_url;
	this.image=data.image;
	this.url=data.url;
	this.description=data.description;
	this.price=data.price;
	this.currency=data.currency;
	this.shipping=data.shipping;
	this.tax=data.tax;
	this.subproducts=data.subproducts.split("@brk@");
	this.opproducts=data.opproducts.split("@brk@");
    this.doupdate=data.id;
	}catch(err){console.log(err.message);}
	}
    var allproducts=this.allproducts;

    var temparr=[];
    for(var i=0;i<allproducts.length;i++)
	{
	temparr.push(allproducts[i].id);
	}
    this.allproductsid=temparr;

    var producteditcontainer=producteditContainer();
	var editor=new producteditcontainer().$mount();
	document.getElementsByClassName("productcontainer")[0].appendChild(editor.$el);
	doEscapePopup(function(){document.getElementsByClassName("productcontainer")[0].removeChild(editor.$el);});
	document.getElementById("closetemplate").onclick=function(){
	document.getElementsByClassName("productcontainer")[0].removeChild(editor.$el);
	producteditcontainer="";
	}

	if(create !=1)
	{
	setTimeout(function()
		{
		var chkbxs=document.getElementsByClassName("tmpltbdydiv")[0].querySelectorAll("input[type=checkbox]");
		for(var i=0;i<chkbxs.length;i++)
		{
			if(chkbxs[i].value==data.id)
			{
				chkbxs[i].setAttribute('disabled',true);
			}
		}
		},500);
	}




	thisvue=this;
	var t=setTimeout(function(){
		var sub=thisvue.subproducts;
		var op=thisvue.opproducts;
        var subdoc=document.getElementById("subproducts").querySelectorAll("input[type=checkbox]");

        var opdoc=document.getElementById("opproducts").querySelectorAll("input[type=checkbox]");


	  for(var i=0;i<subdoc.length;i++)
	  {
		if(sub.indexOf(subdoc[i].value)>=0)
		{
			subdoc[i].checked=true;
		}
	  }
	  for(var i=0;i<opdoc.length;i++)
	  {
		if(op.indexOf(opdoc[i].value)>=0)
		{
			opdoc[i].checked=true;
		}
	  }

	},500);

},
	},
});

function producteditContainer()
{
return Vue.extend({
	template:products.container(),
	data:function(){return products.$data;},
	methods:{
		/*w:function(txt,arr=[]){
		w(txt,arr);
		},*/
		t:function(txt,arr=[]){
			return t(txt,arr);
		},
		init:function(){},
		saveSetting:function(e)
			{
		e.target.disabled=true;
        thisvue=this;
	    var subdoc=document.getElementById("subproducts").querySelectorAll("input[type=checkbox]");

        var opdoc=document.getElementById("opproducts").querySelectorAll("input[type=checkbox]");
		var subarr=[];var oparr=[];

		for(var i=0;i<subdoc.length;i++)
		{
		if(subdoc[i].checked)
		{
			subarr.push(subdoc[i].value);
		}
		}
		for(var i=0;i<opdoc.length;i++)
		{
		if(opdoc[i].checked)
		{
			opdoc[i].checked=true;
			oparr.push(opdoc[i].value);
			if(subarr.indexOf(opdoc[i].value)>=0)
			{
				thisvue.err="<span style='color:#800040;'>"+thisvue.t('Products used in sub-products can not use as optional product.')+"</span>";
				e.target.disabled=false;
				return;
				break;
			}
		}
		}
		var sentdata={"createsaveproduct":1,"download_url":this.download_url,"image":this.image,"productid":this.productid,"title":this.title,"description":this.description,"url":this.url,"price":this.price,"currency":this.currency,"sheeping":this.shipping,"subproducts":subarr.join("@brk@"),"opproducts":oparr.join("@brk@"),"tax":this.tax,"doupdate":this.doupdate};
		request.postRequestCb('req.php',sentdata,function(data){
			e.target.disabled=false;
			if(data.trim()==1)
			{
				thisvue.err="<font color='green'>"+thisvue.t('Saved Successfully')+"</font>";
				location.reload();
			}
			else
			{
				thisvue.err="<span style='color:#800040;'>"+thisvue.t(data.trim())+"</span>";
			}
		});
			}

	}
});
}
