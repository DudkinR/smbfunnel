function visualLoader()
{
	this.elem="";
	this.cover="";
	this.havecontainer=0;
	this.loadingText="";
	this.doneText="";
	this.unableText="";
	this.set=function(elem){
		//pass element
		this.elem=elem;
	};
	this.createContainer=function(){
		var div=document.createElement("div");
		if(this.elem.position===undefined)
		{
			this.elem.style.position="relative";
		}
		div.setAttribute("id","visual-loader-bg");
		div.style.height="100%";
		div.style.width="100%";
		div.style.backgroundColor="rgb(0, 0, 0, .8)";
		div.style.top="0%";
		div.style.zIndex="10";
		div.style.position='absolute';
		this.cover=div;
		this.elem.appendChild(div);
		++this.havecontainer;
	};
	this.load=function(txt="")
	{
		if(this.havecontainer<1){this.createContainer();}
		this.cover.innerHTML="";
		var loader=document.createElement("div");
		var loadercontent="<center><img src='assets/img/visual_cog.gif' style='max-width:60%;max-height:60%;'></center>";
		
		if(txt.length>0)
		{
			loadercontent +="<center><p style='font-size:18px;color:white;'>"+txt+"</p></center>";
		}
		loader.style.top="50%";
		loader.style.left="50%";
		loader.style.height="40%";
		loader.style.width="40%";
		loader.style.transform="translate(-50%,-50%)";
		loader.style.position="absolute";
		loader.style.zIndex="10";
		loader.innerHTML=loadercontent;
		
		this.cover.appendChild(loader);
	};
	
	this.done=function(txt="")
	{
		if(this.havecontainer<1){this.createContainer();}
		this.cover.innerHTML="";
		var loader=document.createElement("div");
		var loadercontent="<center><img src='assets/img/visual_done.gif' style='max-width:60%;max-height:60%;'></center>";
		
		if(txt.length>0)
		{
			loadercontent +="<center><p style='font-size:18px;color:white;'>"+txt+"</p></center>";
		}
		loader.style.top="50%";
		loader.style.left="50%";
		loader.style.height="40%";
		loader.style.width="40%";
		loader.style.transform="translate(-50%,-50%)";
		loader.style.position="absolute";
		loader.style.zIndex="10";
		loader.innerHTML=loadercontent;
		
		this.cover.appendChild(loader);
	};
	this.unable=function(txt=""){
		if(this.havecontainer<1){this.createContainer();}
		this.cover.innerHTML="";
		this.cover.style.backgroundColor="rgb(204,0,102,.8)";
		this.cover.innerHTML="<div style='top:50%;left:50%;position:absolute;transform:translate(-50%,-50%);font-size:18px;color:white;'>"+txt+"</div>";
	};
	this.unset=function()
	{
		this.elem.removeChild(this.cover);
		this.havecontainer=0;
	}
	this.addStyle=function(elem,arr)
	{
		if(elem=='current')
		{
			var elemnt=this.elem;
		}
		else{var elemnt=document.querySelectorAll(elem)[0];}
		
		for(var i=0;i<arr.length;i++)
		{
			//alert(arr[i]);
			var currentstyle="";
			try
			{
				var currentstyle=elemnt.getAttribute("style").trim();
				if(currentstyle.length>1){ currentstyle +=";";}
		
			}
			catch(err){console.log(err);}
			currentstyle=currentstyle+arr.join(";");
			currentstyle=currentstyle.replace(';;',';');
			elemnt.setAttribute('style',currentstyle);
		}
	}
}