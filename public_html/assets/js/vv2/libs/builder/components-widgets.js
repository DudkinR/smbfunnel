/*
Copyright 2017 Ziadin Givan

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

https://github.com/givanz/VvvebJs
*/

Vvveb.ComponentsGroup['Widgets'] = ["widgets/search1-widget","widgets/search2-widget","widgets/googlemaps", "widgets/video", "widgets/chartjs", "widgets/facebookpage", "widgets/paypal", "widgets/instagram", "widgets/twitter","widgets/customcode","widgets/countdowntimer","widgets/counttimetimer","widgets/fontawesome"];

Vvveb.Components.extend("_base", "widgets/search1-widget", {
    name:t("Simple Search"),
    image: "icons/search.svg",
    attributes: ["data-simple-searchbar"],
	html: (function(){
    	return `<div class="search1-wrapper" >
        <form method="get" action=""  autocomplete="off" class="search1-searchBar">
          <input id="search1-searchQueryInput" type="search" name="q" placeholder="Search" />
          <button id="search1-searchQuerySubmit" type="submit" name="search1-searchQuerySubmit">
            <svg style="width:24px;height:24px" viewBox="0 0 24 24"><path fill="#666666" d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />
            </svg>
          </button>
        </form>
	   <style>
	   .search1-wrapper {
        width: 100%;
        max-width: 31.25rem;
        margin: 10px 2px;
      }
      
      .search1-wrapper .label {
        font-size: .625rem;
        font-weight: 400;
        text-transform: uppercase;
        letter-spacing: +1.3px;
        margin-bottom: 1rem;
      }
      
      .search1-wrapper .search1-searchBar {
        width: 100%;
        display: flex;
        flex-direction: row;
        align-items: center;
      }
      
      .search1-wrapper #search1-searchQueryInput {
        width: 100%;
        height: 2.8rem;
        background: #f5f5f5;
        outline: none;
        border: none;
        border-radius: 1.625rem;
        padding: 0 3.5rem 0 1.5rem;
        font-size: 1rem;
      }
      
      .search1-wrapper #search1-searchQuerySubmit {
        width: 3.5rem;
        height: 2.8rem;
        margin-left: -3.5rem;
        background: none;
        border: none;
        outline: none;
      }
      
      .search1-wrapper #search1-searchQuerySubmit:hover {
        cursor: pointer;
      }
	   </style>
		</div>`;
	})(),
});
Vvveb.Components.extend("_base", "widgets/search2-widget", {
    name:t("Animated Search"),
    image: "icons/search.svg",
    attributes: ["data-animated-searchbar"],
	html: (function(){
    	return `<div class="search2-wrapper">
        <form  method="get" action="" class="search2-search">
            <div class="search2-searchInputWrapper">
                <input class="search2-searchInput form-control" type="search" name="q" placeholder='Search here'>
                <button  type="submit" class="search2-searchInputIcon"><i class=" fa fa-search"></i></button>
                </input>
            </div>
            </form>
	   <style>
       .search2-searchInputWrapper {
        position: relative;
        width: 20rem;
        display: flex;
      }
      
      .search2-searchInputWrapper .search2-searchInput {
        width: 20rem;
        height: 2rem !important;
        padding: 0 1rem; 
        border-radius: 2rem;
        transition: transform 0.1s ease-in-out;
      }
      
      .search2-searchInputWrapper ::placeholder {
        color: #a1a1a1;
      }
      
      /* hide the placeholder text on focus */
      .search2-searchInputWrapper :focus::placeholder {
        text-indent: -999px
      }
      
      .search2-searchInputWrapper .search2-searchInput:focus {
        outline: none;
        transform: scale(1.1);
        transition: all 0.1s ease-in-out;
      }
      
      .search2-searchInputWrapper .search2-searchInputIcon {
        position: absolute;
        right: 0.8rem;
        top: 0.2rem;
        background-color: transparent;
        border: 0;
        cursor: pointer;
        transition: all 0.1s ease-in-out;
      }
      .search2-searchInputWrapper .search2-searchInputIcon i {
        color: #a1a1a1;
        transition: all 0.1s ease-in-out;
      }
      
      .search2-search:focus-within > 
      .search2-searchInputWrapper > .search2-searchInputIcon {
        right: 0.2rem;
      }
	   </style>
		</div>`;
	})(),
});
Vvveb.Components.extend("_base", "widgets/fontawesome",{
    name:t("FontAwesome Icon"),
    image: "icons/fontawesome.png",
    attributes: ["data-fontawesome"],
    html: "<i data-fontawesome=1 class='fas fa-user' style='font-size:30px;'></i>",
   properties: [{
        name: t("Icons Link")+": <a href='https://fontawesome.com/icons?d=gallery&m=free' target='_BLANK'>https://fontawesome.com/icons?d=gallery&m=free</a>",
        key: "vvfontawesiconslink",
        inputtype: TextInput
	},
	{
        name:"Icon Classes",
		key: "vvfontawesomeiconclasses",
		htmlAttr: "class",
        inputtype: TextInput
    }
]
});

Vvveb.Components.extend("_base", "widgets/countdowntimer",{
    name:t("Count Down"),
    image: "icons/timer.png",
    attributes:['data-countdown-timer'],
    properties: [{
        name:"Enter Starting Time(UTC)",
        key: "vvcountdownstarttime",
        htmlAttr: "data-countdown-min",
        inputtype: DateTimeInput
    },
    {
        name:"Enter Ending Time(UTC)",
        key: "vvcountdownendtime",
        htmlAttr: "data-countdown-max",
        inputtype: DateTimeInput
    },
    {
        name:"Active",
        key: "vvcountdowninit",
        htmlAttr: "data-start-timer",
		inputtype: SelectInput,
		data:{options: [{value:"true",text:"Yes"},{value:"false",text:"No"}]}
	},
	{
        name:"Text to show if CountDownt not started",
        key: "vvcountdowninittext",
        inputtype: TextareaInput
    },
	{
        name:"Expiration Text",
        key: "vvcountdowninitexptext",
        inputtype: TextareaInput
    }
	],
	onChange: function(node,property,value){
		try{
			if(property.key=="vvcountdowninittext")
			{
				$(node).find(`div[idd="nostart"]`).html(value);
			}
			else if(property.key=="vvcountdowninitexptext")
			{
				$(node).find(`div[idd="exp"]`).html(value);
			}
		}catch(err){console.log(err);}
        //value=value.replace(`<script>`,`<script type="text/javascript">`);
        //$(node).html(value);
    },
    html: (function(){
        let _date_minus=new Date(new Date(Date.now()-10*24*60*60*1000).toUTCString());
    let _date_plus=new Date(new Date(Date.now()+10*24*60*60*1000).toUTCString());
    
    return `<div data-countdown-timer='1' data-countdown-min="${_date_minus.toISOString()}" data-countdown-max="${_date_plus.toISOString()}" data-start-timer="true" style="padding:8px;border:1px solid #0000">
	<div idd="countdowncontainer" style="display:flex;flex-direction:row;flex-wrap:wrap;text-align:center;width:100%;font-size:60px;" onload=\`\`>
		<div idd="nostart" style="display:none;flex-grow:1">Not Started Yet</div>
		<div idd="exp" style="display:none;flex-grow:1">Expired</div>
		<div idd="noexp" style="display:flex;flex-direction:column;justify-content:center;flex-grow:1">
			<div style="font-size:60px" idd="d">10</div>
			<div style="font-size:20px;">Days</div>
		</div>
		<div idd="noexp" style="display:flex;flex-direction:column;justify-content:center;flex-grow:1">
			<div style="font-size:60px" idd="h">10</div>
			<div style="font-size:20px;">Hours</div>
		</div>
		<div idd="noexp" style="display:flex;flex-direction:column;justify-content:center;flex-grow:1">
			<div style="font-size:60px" idd="m">10</div>
			<div style="font-size:20px;">Minutes</div>
		</div>
		<div idd="noexp" style="display:flex;flex-direction:column;justify-content:center;flex-grow:1">
			<div style="font-size:60px" idd="s">10</div>
			<div style="font-size:20px;">Seconds</div>
		</div>
	</div>
	<script type="text/javascript">cfCountDowntimer()</script>
</div>`;})(),
});
Vvveb.Components.extend("_base", "widgets/counttimetimer",{
    name:t("Time Count Down"),
    image: "icons/timer.png",
    attributes:['data-timedown-time'],
    properties: [{
        name:t("Enter Day"),
        key: "vvctimedowndays",
        htmlAttr: "data-timedown-day",
        inputtype: TextInput
    },
    {
        name:t("Enter Hour"),
        key: "vvctimedownhour",
        htmlAttr: "data-timedown-hour",
        inputtype: TextInput
    },
    {
        name:t("Enter Minute"),
        key: "vvctimedownminute",
        htmlAttr: "data-timedown-minute",
        inputtype: TextInput
	},
    {
        name:t("Enter Second"),
        key: "vvctimedownsecond",
        htmlAttr: "data-timedown-second",
		inputtype: SelectInput,
        inputtype: TextInput
	},
    {
        name:t("Reset Timer On Page Refresh"),
        key: "vvctimedownstarton",
        htmlAttr: "data-timedown-start", 
		inputtype: SelectInput,
		data:{options: [{value:"true",text:"Yes"},{value:"false",text:"No"}]}
	},
	],
	onChange: function(node,property,value){
		try{
			if(property.key=="vvctimedowndays")
			{
				$(node).find(`div[idd="d"]`).attr("data-timedown-d",value);
			}
			else if(property.key=="vvctimedownhour")
			{
				$(node).find(`div[idd="h"]`).attr("data-timedown-h",value);
			}
            else if(property.key=="vvctimedownminute")
			{
				$(node).find(`div[idd="m"]`).attr("data-timedown-m",value);
			}
            else if(property.key=="vvctimedownsecond")
			{
				$(node).find(`div[idd="s"]`).attr("data-timedown-s",value);
			}
            else if(property.key=="vvctimedownstarton")
			{
				$(node).attr("data-start-timer",value);
			}
		}catch(err){console.log(err);}
    
    },
    html: (function(){
       
    
    return `<div data-timedown-time='1' data-start-timer="" style="padding:8px;border:1px solid #0000">
	<div idd="countdowncontainer"   style="display:flex;flex-direction:row;flex-wrap:wrap;text-align:center;width:100%;font-size:60px;" onload=\`\`>
		<div style="display:flex;flex-direction:column;justify-content:center;flex-grow:1">
			<div style="font-size:60px" idd="d" data-timedown-d='1'>10</div>
			<div style="font-size:20px;">Days</div>
		</div>
		<div style="display:flex;flex-direction:column;justify-content:center;flex-grow:1">
			<div style="font-size:60px" idd="h" data-timedown-h='1'>10</div>
			<div style="font-size:20px;">Hours</div>
		</div>
		<div  style="display:flex;flex-direction:column;justify-content:center;flex-grow:1">
			<div style="font-size:60px" idd="m"  data-timedown-m='1'>10</div>
			<div style="font-size:20px;">Minutes</div>
		</div>
		<div  style="display:flex;flex-direction:column;justify-content:center;flex-grow:1">
			<div style="font-size:60px" idd="s" data-timedown-s='1'>10</div>
			<div style="font-size:20px;">Seconds</div>
		</div>
	</div>
	<script type="text/javascript">cfTimeDowntimer()</script>
</div>`;})(),
});

Vvveb.Components.extend("_base", "widgets/customcode", {
    name:t("Custom Code"),
    image: "icons/customcode.png",
    attributes: ["data-custom-code"],
    html: `<span data-custom-code>Your Custom code will be replaced here</span>`,
    properties: [{
        name:"Enter Code",
        key: "vvcustcode",
        inputtype: TextareaInput
    }],
    onChange: function(node,property,value){
		try
		{
			let doc=document.createElement("div");
			doc.innerHTML=value;
			$(node).html(doc.innerHTML);
		}catch(err){console.log(err);}
    }
});


Vvveb.Components.extend("_base", "widgets/googlemaps", {
    name: t("Google Maps"),
    attributes: ["data-component-maps"],
    image: "icons/map.svg",
    dragHtml: '<img src="' + Vvveb.baseUrl + 'icons/maps.png">',
    html: '<div data-component-maps style="min-height:240px;min-width:240px;position:relative"><iframe frameborder="0" src="https://maps.google.com/maps?&z=1&t=q&output=embed" width="100" height="100" style="width:100%;height:100%;left:0px;pointer-events:none"></iframe></div>',
    resizable:true,//show select box resize handlers
    resizeMode:"css",
    
    
    //url parameters
    z:3, //zoom
    q:'Paris',//location
    t: 'q', //map type q = roadmap, w = satellite
    
    onChange: function (node, property, value)
    {
		map_iframe = jQuery('iframe', node);
		
		this[property.key] = value;
		
		mapurl = 'https://maps.google.com/maps?&q=' + this.q + '&z=' + this.z + '&t=' + this.t + '&output=embed';
		
		map_iframe.attr("src",mapurl);
		
		return node;
	},

    properties: [{
        name: "Address",
        key: "q",
        inputtype: TextInput
    }, 
	{
        name: "Map type",
        key: "t",
        inputtype: SelectInput,
        data:{
			options: [{
                value: "q",
                text: "Roadmap"
            }, {
                value: "w",
                text: "Satellite"
            }]
       },
    },
    {
        name: "Zoom",
        key: "z",
        inputtype: RangeInput,
        data:{
			max: 20, //max zoom level
			min:1,
			step:1
       },
	}]
});

Vvveb.Components.extend("_base", "widgets/video", {
    name: t("Youtube, Vimeo or Web Page"),
    attributes: ["data-component-video"],
    image: "icons/video.svg",
    dragHtml: '<img src="' + Vvveb.baseUrl + 'icons/video.svg" width="100" height="100">', //use image for drag and swap with iframe on drop for drag performance
    html: '<div data-component-video data-video-provider="y" autoplay="1" controls="1"  style="min-height:240px;min-width:240px;position:relative;padding:6px;" data-video-url="" data-video-id="-stFvGmg1A8" data-video-height="auto" data-video-width="100%"><div style="overflow:hidden;position:relative;height:0;padding-bottom:56.25%;"><iframe frameborder="0" src="https://www.youtube.com/embed/-stFvGmg1A8" style="width:100%;height:100%;position:absolute;left:0px;" allow="autoplay"></iframe></div></div>',
    
    
    //url parameters set with onChange
    t:'y',//video type
    video_id:'',//video id
    autoplay: false,
    controls: false,
    loop: false,
    muted: false,

	init: function (node)
	{
		iframe = jQuery('iframe', node);
		video = jQuery('video', node);
		
		$("#right-panel [data-key=url]").hide();
		
        this.t = $(node).attr("data-video-provider"); 
        this.autoplay = $(node).attr("autoplay"); 
        this.controls = $(node).attr("controls"); 
		if (video.length) 
		{
			this.url = video.src;
		} else if (iframe.length) //vimeo or youtube
		{
			src = iframe.attr("src");
			if (src && src.indexOf("youtube") != "-1" )//youtube
			{
				this.video_id = src.match(/youtube.com\/embed\/([^$\?]*)/)[1];
			} else if (src && src.indexOf("vimeo")  != "-1" )//youtube
			{
				this.video_id = src.match(/vimeo.com\/video\/([^$\?]*)/)[1];
			}
            else
			{
				this.video_id = src;
			}
		} 
		$("#right-panel input[name=video_id]").val(this.video_id);
		$("#right-panel input[name=url]").val(this.url);
		$("#right-panel div[data-key=t] select").val(this.t);
	},
	
	onChange: function (node, property, value)
	{
        // alert("onchange")
		// console.log("vid");
		let max_height="240px";
		let max_width="240px";
		try
		{

			max_height=$(`input[name="vvebvideo_maxheight"]`).val();
			max_width=$(`input[name="vvebvideo_maxwidth"]`).val();
		}
		catch(err)
		{
			console.log(err);
		}

		this[property.key] = value;
        let proptype=$(node).prop("tagName");
		proptype=proptype.toLowerCase();
		if((["iframe","video"].indexOf(proptype)>-1) || $(node).attr("data-component-video") !==undefined)
		{
			switch (this.t)
			{
				case 'y':
					$("#right-panel [data-key=video_id]").show();
					$("#right-panel [data-key=url]").hide();
                    $(node).attr("data-video-id",this.video_id);
                    $(node).attr("data-video-provider",'y');
                    let viframe = $(node).find("iframe");
                    $(viframe).attr("src",`https://www.youtube.com/embed/${ this.video_id}?autoplay=${(this.autoplay)?1:0}&controls=${(this.controls)?1:0}&loop=${(this.loop)?1:0}&mute=${(this.muted)?1:0}`);
				break;
				case 'v':
					$("#right-panel [data-key=video_id]").show();
					$("#right-panel [data-key=url]").hide();
                    $(node).attr("data-video-id",this.video_id);
                    $(node).attr("data-video-provider",'v');
                    let vmiframe = $(node).find("iframe");
                    $(vmiframe).attr("src",`https://player.vimeo.com/video/${ this.video_id}?autoplay=${(this.autoplay)?1:0}&controls=${(this.controls)?1:0}&loop=${(this.loop)?1:0}&mute=${(this.muted)?1:0}`);
				break;
                case 'h':
					$("#right-panel [data-key=video_id]").show();
					$("#right-panel [data-key=url]").hide();
                    $(node).attr("data-video-id",this.video_id);
                    $(node).attr("data-video-provider",'h');
                    let htmlframe = $(node).find("iframe");
                    $(htmlframe).attr("src",`${this.video_id}`);
				break;
			}
			try
			{
                $(node).css("height",max_height);
                $(node).attr("data-video-height",max_height);
                $(node).css("width",max_width);
                $(node).attr("data-video-width",max_width);
			}
			catch(err)
			{
				console.log(err);
			}
			return node;
		}
		return node;
	},	
	
    properties: [{
        name: t("Provider"),
        key: "t",
        inputtype: SelectInput,
        data:{
			options: [{
                text: "Youtube",
                value: "y"
            }, {
                text: "Vimeo",
                value: "v"
            },
            {
                text: "Web Page",
                value: "h"
            },
        ]
       },
	 },	       
     {
        name: t("Video id or URL"),
        key: "video_id",
        inputtype: TextInput,
    },
    {
        name: t("Height (Value in 'px' or '%')"),
		key: "vvebvideo_maxheight",
		htmlAttr: "data-video-height",
        inputtype: TextInput,
	},
	{
        name: t("Width (Value in 'px' or '%')"),
		key: "vvebvideo_maxwidth",
		htmlAttr: "data-video-width",
        inputtype: TextInput,
	},
    {
        name: "Autoplay",
        key: "autoplay",
        htmlAttr: "autoplay",
        inputtype: CheckboxInput
    },{
        name: t("Controls"),
        key: "controls",
        htmlAttr: "controls",
        inputtype: CheckboxInput
    },
    {
        name: t("Mute"),
        key: "muted",
        htmlAttr: "muted",
        inputtype: CheckboxInput
    },
    {
        name: t("Loop"),
        key: "loop",
        htmlAttr: "loop",
        inputtype: CheckboxInput
    }]
});



Vvveb.Components.extend("_base", "widgets/facebookcomments", {
    name: t("Facebook Comments"),
    attributes: ["data-component-facebookcomments"],
    image: "icons/facebook.svg",
    dragHtml: '<img src="' + Vvveb.baseUrl + 'icons/facebook.svg">',
    html: '<div  data-component-facebookcomments><script>(function(d, s, id) {\
			  var js, fjs = d.getElementsByTagName(s)[0];\
			  if (d.getElementById(id)) return;\
			  js = d.createElement(s); js.id = id;\
			  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6&appId=";\
			  fjs.parentNode.insertBefore(js, fjs);\
			}(document, \'script\', \'facebook-jssdk\'));</script>\
			<div class="fb-comments" \
			data-href="' + window.location.href + '" \
			data-numposts="5" \
			data-colorscheme="light" \
			data-mobile="" \
			data-order-by="social" \
			data-width="100%" \
			></div></div>',
    properties: [{
        name: "Href",
        key: "business",
        htmlAttr: "data-href",
        child:".fb-comments",
        inputtype: TextInput
    },{		
        name: "Item name",
        key: "item_name",
        htmlAttr: "data-numposts",
        child:".fb-comments",
        inputtype: TextInput
    },{		
        name: "Color scheme",
        key: "colorscheme",
        htmlAttr: "data-colorscheme",
        child:".fb-comments",
        inputtype: TextInput
    },{		
        name: "Order by",
        key: "order-by",
        htmlAttr: "data-order-by",
        child:".fb-comments",
        inputtype: TextInput
    },{		
        name: "Currency code",
        key: "width",
        htmlAttr: "data-width",
        child:".fb-comments",
        inputtype: TextInput
	}]
});

Vvveb.Components.extend("_base", "widgets/instagram", {
    name: t("Instagram"),
    attributes: ["data-component-instagram"],
    image: "icons/instagram.svg",
    drophtml: '<img src="' + Vvveb.baseUrl + 'icons/instagram.png">',
    html: '<div align=center data-component-instagram>\
			<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="https://www.instagram.com/p/tsxp1hhQTG/" data-instgrm-version="8" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:658px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:8px;"> <div style=" background:#F8F8F8; line-height:0; margin-top:40px; padding:50% 0; text-align:center; width:100%;"> <div style=" background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAsCAMAAAApWqozAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAMUExURczMzPf399fX1+bm5mzY9AMAAADiSURBVDjLvZXbEsMgCES5/P8/t9FuRVCRmU73JWlzosgSIIZURCjo/ad+EQJJB4Hv8BFt+IDpQoCx1wjOSBFhh2XssxEIYn3ulI/6MNReE07UIWJEv8UEOWDS88LY97kqyTliJKKtuYBbruAyVh5wOHiXmpi5we58Ek028czwyuQdLKPG1Bkb4NnM+VeAnfHqn1k4+GPT6uGQcvu2h2OVuIf/gWUFyy8OWEpdyZSa3aVCqpVoVvzZZ2VTnn2wU8qzVjDDetO90GSy9mVLqtgYSy231MxrY6I2gGqjrTY0L8fxCxfCBbhWrsYYAAAAAElFTkSuQmCC); display:block; height:44px; margin:0 auto -44px; position:relative; top:-22px; width:44px;"></div></div> <p style=" margin:8px 0 0 0; padding:0 4px;"> <a href="https://www.instagram.com/p/tsxp1hhQTG/" style=" color:#000; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none; word-wrap:break-word;" target="_blank">Text</a></p> <p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;">A post shared by <a href="https://www.instagram.com/instagram/" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px;" target="_blank"> Instagram</a> (@instagram) on <time style=" font-family:Arial,sans-serif; font-size:14px; line-height:17px;" datetime="-">-</time></p></div></blockquote>\
			<script async defer src="//www.instagram.com/embed.js"></script>\
		</div>',
    properties: [{
        name: "Widget id",
        key: "instgrm-permalink",
        htmlAttr: "data-instgrm-permalink",
        child: ".instagram-media",
        inputtype: TextInput
    }],
});

Vvveb.Components.extend("_base", "widgets/twitter", {
    name: t("Twitter"),
    attributes: ["data-component-twitter"],
    image: "icons/twitter.svg",
    dragHtml: '<img src="' + Vvveb.baseUrl + 'icons/twitter.svg">',
    html: '<div data-component-twitter><a class="twitter-timeline" data-dnt="true" data-chrome="nofooter noborders noscrollbar noheader transparent" href="https://twitter.com/twitterapi" href="https://twitter.com/twitterapi" data-widget-id="243046062967885824" ></a>\
			<script>window.twttr = (function(d, s, id) {\
			  var js, fjs = d.getElementsByTagName(s)[0],\
				t = window.twttr || {};\
			  if (d.getElementById(id)) return t;\
			  js = d.createElement(s);\
			  js.id = id;\
			  js.src = "https://platform.twitter.com/widgets.js";\
			  fjs.parentNode.insertBefore(js, fjs);\
			  t._e = [];\
			  t.ready = function(f) {\
				t._e.push(f);\
			  };\
			  return t;\
			}(document, "script", "twitter-wjs"));</script></div>',
    properties: [{
        name: "Widget id",
        key: "widget-id",
        htmlAttr: "data-widget-id",
        child: " > a, > iframe",
        inputtype: TextInput
    }],
});

Vvveb.Components.extend("_base", "widgets/paypal", {
    name: t("Paypal"),
    attributes: ["data-component-paypal"],
    image: "icons/paypal.svg",
    html: '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" data-component-paypal>\
\
				<!-- Identify your business so that you can collect the payments. -->\
				<input type="hidden" name="business"\
					value="givanz@yahoo.com">\
\
				<!-- Specify a Donate button. -->\
				<input type="hidden" name="cmd" value="_donations">\
\
				<!-- Specify details about the contribution -->\
				<input type="hidden" name="item_name" value="VvvebJs">\
				<input type="hidden" name="item_number" value="Support">\
				<input type="hidden" name="currency_code" value="USD">\
\
				<!-- Display the payment button. -->\
				<input type="image" name="submit"\
				src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif"\
				alt="Donate">\
				<img alt="" width="1" height="1"\
				src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" >\
\
			</form>',
    properties: [{
        name: "Email",
        key: "business",
        htmlAttr: "value",
        child:"input[name='business']",
        inputtype: TextInput
    },{		
        name: "Item name",
        key: "item_name",
        htmlAttr: "value",
        child:"input[name='item_name']",
        inputtype: TextInput
    },{		
        name: "Item number",
        key: "item_number",
        htmlAttr: "value",
        child:"input[name='item_number']",
        inputtype: TextInput
    },{		
        name: "Currency code",
        key: "currency_code",
        htmlAttr: "value",
        child:"input[name='currency_code']",
        inputtype: TextInput
	}],
});
    
Vvveb.Components.extend("_base", "widgets/facebookpage", {
    name: t("Facebook Page Plugin"),
    attributes: ["data-component-facebookpage"],
    image: "icons/facebook.svg",
    dropHtml: '<img src="' + Vvveb.baseUrl + 'icons/facebook.png">',
	html: '<div data-component-facebookpage><div class="fb-page" data-href="https://www.facebook.com/facebook" data-appId="100526183620976" data-tabs="timeline" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/facebook" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/facebook">Facebook</a></blockquote></div>\
			<div id="fb-root"></div>\
			<script>(function(d, s, id) {\
			  var appId = document.getElementsByClassName("fb-page")[0].dataset.appid;\
			  var js, fjs = d.getElementsByTagName(s)[0];\
			  js.nonce = "5YzD1JaS";\
			  js.crossorigin = "anonymous";\
			  js = d.createElement(s); js.id = id;\
			  js.src = \'https://connect.facebook.net/en_EN/sdk.js#xfbml=1&version=v3.0&appId=\' + appId + \'&autoLogAppEvents=1\';\
			  fjs.parentNode.insertBefore(js, fjs);\
			}(document, \'script\', \'facebook-jssdk\'));</script></div>',

    properties: [{
        name: "Small header",
        key: "small-header",
        htmlAttr: "data-small-header",
        child:".fb-page",
        inputtype: TextInput
    },{		
        name: "Adapt container width",
        key: "adapt-container-width",
        htmlAttr: "data-adapt-container-width",
        child:".fb-page",
        inputtype: TextInput
    },{		
        name: "Hide cover",
        key: "hide-cover",
        htmlAttr: "data-hide-cover",
        child:".fb-page",
        inputtype: TextInput
    },{		
        name: "Show facepile",
        key: "show-facepile",
        htmlAttr: "data-show-facepile",
        child:".fb-page",
        inputtype: TextInput
    },{		
        name: "App Id",
        key: "appid",
        htmlAttr: "data-appId",
        child:".fb-page",
        inputtype: TextInput
	}],
   onChange: function(node, input, value, component) {
	   //console.log(component.html);
	   //console.log(this.html);
	   
	   var newElement = $(this.html);
	   newElement.find(".fb-page").attr(input.htmlAttr, value);
	   
	   console.log(node.parent());
	   console.log(node.parent().html());


	   $("[data-fbcssmodules]", Vvveb.Builder.frameHead).remove();
	   $("[data-fbcssmodules]", Vvveb.Builder.frameBody).remove();
	   $("script[src^='https://connect.facebook.net']", Vvveb.Builder.frameHead).remove();


	   node.parent().html(newElement.html());

	   console.log(newElement);


	   console.log(newElement.html());

	   return newElement;
	}	
});
    
Vvveb.Components.extend("_base", "widgets/chartjs", {
    name: t("Chart.js"),
    attributes: ["data-component-chartjs"],
    image: "icons/chart.svg",
	dragHtml: '<img src="' + Vvveb.baseUrl + 'icons/chart.svg">',
    html: '<div data-component-chartjs class="chartjs" data-chart=\'{\
			"type": "line",\
			"data": {\
				"labels": ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],\
				"datasets": [{\
					"data": [12, 19, 3, 5, 2, 3],\
					"fill": false,\
					"borderColor":"rgba(255, 99, 132, 0.2)"\
				}, {\
					"fill": false,\
					"data": [3, 15, 7, 4, 19, 12],\
					"borderColor": "rgba(54, 162, 235, 0.2)"\
				}]\
			}}\' style="min-height:240px;min-width:240px;width:100%;height:100%;position:relative">\
			  <canvas></canvas>\
			</div>',
	chartjs: null,
	ctx: null,
	node: null,

	config: {
	},		

	dragStart: function (node)
	{
		//check if chartjs is included and if not add it when drag starts to allow the script to load
		body = Vvveb.Builder.frameBody;
		
		if ($("#chartjs-script", body).length == 0)
		{
			$(body).append('<script id="chartjs-script" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>');
			$(body).append('<script>\
				$(document).ready(function() {\
					$(".chartjs").each(function () {\
						ctx = $("canvas", this).get(0).getContext("2d");\
						config = JSON.parse(this.dataset.chart);\
						chartjs = new Chart(ctx, config);\
					});\
				\});\
			  </script>');
		}
		
		return node;
	},
	

	drawChart: function ()
	{
		if (this.chartjs != null) this.chartjs.destroy();
		this.node.dataset.chart = JSON.stringify(this.config);
		
		config = Object.assign({}, this.config);//avoid passing by reference to avoid chartjs to fill the object
		this.chartjs = new Chart(this.ctx, config);
	},
	
	init: function (node)
	{
		this.node = node;
		this.ctx = $("canvas", node).get(0).getContext("2d");
		this.config = JSON.parse(node.dataset.chart);
		this.drawChart();

		return node;
	},
  
  
	beforeInit: function (node)
	{
		return node;
	},
    
    properties: [
	{
        name: "Type",
        key: "type",
        inputtype: SelectInput,
        data:{
			options: [{
                text: "Line",
                value: "line"
            }, {
                text: "Bar",
                value: "bar"
            }, {
                text: "Pie",
                value: "pie"
            }, {
                text: "Doughnut",
                value: "doughnut"
            }, {
                text: "Polar Area",
                value: "polarArea"
            }, {
                text: "Bubble",
                value: "bubble"
            }, {
                text: "Scatter",
                value: "scatter"
            },{
                text: "Radar",
                value: "radar"
            }]
       },
		init: function(node) {
			return JSON.parse(node.dataset.chart).type;
		},
       onChange: function(node, value, input, component) {
		   component.config.type = value;
		   component.drawChart();
		   
		   return node;
		}
	 }]
});
