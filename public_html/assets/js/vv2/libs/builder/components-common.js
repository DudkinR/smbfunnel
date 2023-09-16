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

https://github.com/givanz/Vvvebjs
*/

bgcolorClasses = ["bg-primary", "bg-secondary", "bg-success", "bg-danger", "bg-warning", "bg-info", "bg-light", "bg-dark", "bg-white"]

bgcolorSelectOptions = 
[{
	value: "Default",
	text: ""
}, 
{
	value: "bg-primary",
	text: "Primary"
}, {
	value: "bg-secondary",
	text: "Secondary"
}, {
	value: "bg-success",
	text: "Success"
}, {
	value: "bg-danger",
	text: "Danger"
}, {
	value: "bg-warning",
	text: "Warning"
}, {
	value: "bg-info",
	text: "Info"
}, {
	value: "bg-light",
	text: "Light"
}, {
	value: "bg-dark",
	text: "Dark"
}, {
	value: "bg-white",
	text: "White"
}];

function changeNodeName(node, newNodeName)
{
	var newNode;
	newNode = document.createElement(newNodeName);
	attributes = node.get(0).attributes;
	
	for (i = 0, len = attributes.length; i < len; i++) {
		newNode.setAttribute(attributes[i].nodeName, attributes[i].nodeValue);
	}

	$(newNode).append($(node).contents());
	$(node).replaceWith(newNode);
	
	return newNode;
}

var base_sort = 100;//start sorting for base component from 100 to allow extended properties to be first
var style_section = 'style';
var advanced_section = 'advanced';

Vvveb.Components.add("_base", {
    name: t("Element"),
	properties: [{
        key: "element_header",
        inputtype: SectionInput,
        name:false,
        sort:base_sort++,
        data: {header:t("General")},
    }, {
        name: "Id",
        key: "id",
        htmlAttr: "id",
        sort: base_sort++,
        inline:true,
        col:6,
        inputtype: TextInput
    }, {
        name: "Class",
        key: "class",
        htmlAttr: "class",
        sort: base_sort++,
        inline:true,
        col:6,
        inputtype: TextInput
    },
    {
        name: t("Input Name"),
        key: "name",
        htmlAttr: "name",
        sort: base_sort++,
        inline:true,
        col:6,
        inputtype: SelectInput,
        data: (function(options){
            let arr=[]
            try{
                options.forEach((op)=>{
                    arr.push({
                        value: op,
                        text: op
                    });
                });
            }catch(err){console.log(err);}
            return {options:arr};
        })(cf_global_page_inputs),
    },
    {
        name: t("Order Input Value"),
        key: "member_element_inputs_value",
        htmlAttr: "sf-input-data-value",
        sort: base_sort++,
        inline:true,
        col:6,
        inputtype: SelectInput,
        data: (function(options){
            let arr=[]
            try{
                options.forEach((op)=>{
                    let mval = `{member.${op}}`;
                    arr.push({
                        value: mval,
                        text: op
                    });
                });
            }catch(err){console.log(err);}
            return {options:arr};
        })(cf_global_page_inputs),
    },
    {
        name: t("Payment Methods"),
        key: "value",
        htmlAttr: "value",
        sort: base_sort++,
        inline:true,
        col:6,
        inputtype: SelectInput,
        data:{options: CF_GLOBAL_PAYMENT_METHOD}
    },
    {
        name: t("CF-Loop"),
        key: "cf-loop",
        htmlAttr: "do-cf-loop",
        sort: base_sort++,
        inline:true,
        col:6,
        inputtype: SelectInput,
        data:{options: [{value:null,text:t("None")},{value:"members",text:t("Members")},{value:"products",text:t("Products")}]}
    },
    {
        name: t("Product-Loop"),
        key: "Product-loop",
        htmlAttr: "data-allproduct_orderpage",
        sort: base_sort++,
        inline:true,
        col:6,
        inputtype: SelectInput,
        data:{options: [{value:null,text:t("No")},{value:"1",text:t("yes")}]}
    },
    {
        name: t("Disable"),
        key: "vvdodisabled",
        htmlAttr: "disabled",
        sort: base_sort++,
        inline:true,
        col:6,
        inputtype: SelectInput,
        data:{options: [{value:"disabled",text:t("yes")},{value:null,text:t("No")}]}
    },
    {
        name: t("Required"),
        key: "vvisrequired",
        htmlAttr: "required",
        sort: base_sort++,
        inline:true,
        col:6,
        inputtype: SelectInput,
        data:{options: [{value:"required",text:t("yes")},{value:null,text:t("No")}]}
    },
    {
        key: "element_header_linking",
        inputtype: SectionInput,
        name:false,
        sort:base_sort++,
        data: {header:t("Auto Linking")},
    },
  
    {
        name: t("Create Link"),
        key: "vvautolink",
        htmlAttr: "data-autolink",
        sort: base_sort++,
        inline:true,
        col:6,
        inputtype: SelectInput,
        data:{options: [{value:"1",text:t("Yes")},{value:null,text:t("No")}]}
    },
    {
        name: t("Open In"),
        key: "vvautolinktarget",
        htmlAttr: "data-autolink-target",
        sort: base_sort++,
        inline:true,
        col:6,
        inputtype: SelectInput,
        data:{options: [{value:null,text:t("Same Tab")},{value:"_BLANK",text:t("New Tab")}]}
    },
    {
        name: t("URL (Skip if you are making OTO link)"),
        key: "vvautolinkurl",
        htmlAttr: "data-autolink-url",
        sort: base_sort++,
        inline:true,
        col:12,
        inputtype: TextInput,
    },
    {
        name: t("OTO LINK"),
        key: "vvautolinkoto",
        htmlAttr: "data-autolink-oto",
        sort: base_sort++,
        inline: true,
        col: 12,
        inputtype: SelectInput,
        data:{options: [{value:null,text:t("No")},{value:"1",text:t("OTO Product Link")},{value:"2",text:t("OTO Exit Link")},{value:"-1",text:t("OTO Product Removal Link (Checkout Page)")}]}
    },
    {
        name: t("OTO Product ID"),
        key: "vvautolinkotoproduct",
        htmlAttr: "data-autolink-oto-product",
        sort: base_sort++,
        inline:true,
        col:12,
        inputtype: TextInput,
    },
    {
        name: t("Next OTO Link"),
        key: "vvautolinkotonext",
        htmlAttr: "data-autolink-oto-next",
        sort: base_sort++,
        inline:true,
        col:12,
        inputtype: TextInput,
    },
    {
        key: "element_custom_blocks",
        inputtype: SectionInput,
        name: false,
        sort: base_sort++,
        data: {header: t("Custom Blocks")},
    },
    {
        name: t("Create new block or select existing"),
        key: "element_custom_block_select",
        htmlAttr: "cf-data-block-id",
        sort: base_sort++,
        inline:true,
        col:12,
        inputtype: SelectInput,
        data:{options: [...global_custom_blocks_options]}
    },
    {
        name: t("Enter Title"),
        key: "element_custom_block_name",
        sort: base_sort++,
        inline:true,
        col:12,
        inputtype: TextInput,
    },
    {
        name: t("Enter Media"),
        key: "element_custom_block_image",
        sort: base_sort++,
        inline:true,
        col: 6,
        inputtype: TextInput,
    },
    {
        key: "element_custom_block_image_uploader",
        inputtype: ButtonInput,
        name:false,
        sort:base_sort++,
        col: 6,
        data: {text: t("Media")},
        onChange: function(){
            doEditorMediaOpen(
                function(data){
                    $(`[data-key='element_custom_block_image'] input`).val(data);
                }
            );
        },
    },
    {
        key: "element_custom_block_save",
        inputtype: ButtonInput,
        name:false,
        sort:base_sort++,
        col: 6,
        data: {text: t("Save")}
    },
    {
        key: "element_custom_block_delete",
        inputtype: ButtonInput,
        name:false,
        sort:base_sort++,
        col: 6,
        data: {text: t("Delete"), icon: "la la-trash"}
    }
   ],
   onChange: function(node,property,value){
       if(property.key=='element_custom_block_select')
       {
           try
           {
               try
               {
                    let delBtn= document.querySelectorAll(`[data-key="element_custom_block_delete"] button`)[0];
                    if(value===null || value.trim().length===0)
                    {
                        delBtn.style.display= "none";    
                    }
                    else
                    {
                        delBtn.style.display= "block";
                    }
               }catch(err){}
                let block= global_custom_templates[value];
                let name= (block.name !==undefined)? block.name:"";
                let image= (block.image !==undefined)? block.image:"";

                $(`[data-key='element_custom_block_name'] input`).val(name);
                $(`[data-key='element_custom_block_image'] input`).val(image);
           }catch(err){console.log(err);}
       }
       if(property.key=='element_custom_block_save')
       {
           let name= $(`[data-key='element_custom_block_name'] input`).val();
           if(name.trim().length<1)
           {name= "No Name";}
           let id= $(`[data-key='element_custom_block_select'] select`).val();
           if(id===null || id.trim().length===0)
           {
                id= `cf-block-custom@@${Date.now()}`;
           }
           $(node).attr('cf-data-block-id', id);

           let parent= $('<div>');
           parent.append($(node).clone());
           let html= parent.html();

           let image= $(`[data-key='element_custom_block_image'] input`).val();
           image= (image.trim().length>0)? image: "assets/img/template.png";
           let dragHtml= `<img style="max-width: 200px;" src=${image}>`;

           let block= {html, name, dragHtml, image};
           editorDoSetBlock(id, block);
           return node;
       }
       if(property.key==="element_custom_block_delete")
       {
            let id= $(`[data-key='element_custom_block_select'] select`).val();
            if(!(id===null || id.trim().length===0))
            {
                editorDoDelBlock(id);
            }
       }
       if(property.key=="cf-loop")
       {
           let html=$(node).html();
           try{
            $(node).removeAttr('cf-loop');
           }catch(err){console.log(err);}
           
           html=html.replace(/({products}|{members})/ig,"");
           html=html.replace(/({\/products}|{\/members})/ig,"");

           html=html.replace(/{members}/ig,"");
           html=html.replace(/{\/members}/ig,"");

           if(value=="products")
           {
            html =`{products}${html}{/products}`;
           }
           else if(value=="members")
           {
            html =`{members}${html}{/members}`;
           }
           $(node).html(html);
       }
       if(property.key=="vvautolink" || (property.key=="vvautolinkoto" && ['1','2'].indexOf(value)>-1))
       {
           try
           {
                if(value=='1')
                {
                    $(node).css({'cursor':'pointer'});
                    //$(node).attr('onclick',`cfAutoLinkAdder(this)`);
                }
                else if(property.key=="vvautolink")
                {
                    $(node).css({'cursor':''});
                    //$(node).removeAttr('onclick');
                }
           }
           catch(err)
           {console.log(err);}
       }
       if (property.key === "parent_element_set") {
              let newEl = $(`<${value}></${value}>`);
              $(newEl).css({"padding":"6px"});
              let html = node[0].outerHTML;
              newEl.html(html);
              node.replaceWith(newEl);
        }
   }
});    
//Remove Style
Vvveb.Components.extend("_base", "_base", {
    properties: [{
       key: "remove_element_style",
       inputtype: SectionInput,
       name:false,
       sort: base_sort++,
       section: style_section,
       data: {header:t("Remove Element Style")},
   }, {
       name: t("Select Style"),
       key: "remove_style",
       col:12,
       inline:true,
       sort: base_sort++,
       section: style_section,
       inputtype: SelectInput,
       data: {
        options: [{
            value: null,
            text: t("Select Property")
        }, {	
            value: "font-family",
            text: t("Font Family")
        },
        {	
            value: "position",
            text: t("Position")
        }, {	
            value: "margin",
            text: t("Margin")
        }, {	
            value: "margin-top",
            text: t("Margin Top")
        },
        {	
            value: "margin-left",
            text: t("Margin Left")
        }, {	
            value: "margin-right",
            text: t("Margin Right")
        }, {	
            value: "margin-bottom",
            text: t("Margin Bottom")
        }, {	
            value: "padding",
            text: t("Padding")
        }, {	
            value: "padding-left",
            text: t("Padding Left")
        },
        {
            value: "padding-top",
            text: t("Padding Top")
        }, {	
            value: "padding-right",
            text: t("Padding Right")
        }, {	
            value: "padding-bottom",
            text: t("Padding Bottom")
        }, {	
            value: "font-weight",
            text: t("Font Weight")
        }, {	
            value: "font-size",
            text: t("Font Size")
        }, {	
            value: "text-align",
            text: t("Text Align")
        }, {	
            value: "line-height",
            text: t("Line Height")
        }, {	
            value: "letter-spacing",
            text: t("Letter Spacing")
        },
        {	
            value: "float",
            text: t("Float")
        }, {	
            value: "text-decoration",
            text: t("Text Decoration")
        }, {	
            value: "text-decoration-color",
            text: t("Text Decoration Color")
        }, {	
            value: "text-decoration-style",
            text: t("Text Decoration Style")
        }, {	
            value: "width",
            text: t("Width")
        }, {	
            value: "height",
            text: t("Height")
        }, {	
            value: "min-height",
            text: t("Minimum Height")
        }, {	
            value: "max-height",
            text: t("Maximum Height")
        }, {	
            value: "min-width",
            text: t("Minimum Width")
        }, {	
            value: "max-width",
            text: t("Maximum Width")
        }, {	
            value: "border-color",
            text: t("Border Color")
        }, {	
            value: "border-style",
            text: t("Border Style")
        }, {	
            value: "border-width",
            text: t("Border Width")
        }, {	
            value: "border-top-left-radius",
            text: t("Border Top Left Radius")
        }, {	
            value: "border-top-right-radius",
            text: t("Border Top Right Radius")
        }, {	
            value: "border-bottom-right-radius",
            text: t("Border Bottom Right Radius")
        }, {	
            value: "border-bottom-left-radius",
            text: t("Border Bottom Left Radius")
        }, {	
            value: "Top",
            text: t("Top")
        }, {	
            value: "left",
            text: t("Left")
        }, {	
            value: "right",
            text: t("Right")
        }, {	
            value: "bottom",
            text: t("Bottom")
        }, {	
            value: "color",
            text: t("Color")
        },
         {
            value: "background-color",
            text: t("Background Color")
        }
        , {
            value: "background-image",
            text: t("Background Image")
        }],
       },
       onChange: function (node,  type) {

        if( type=='font-family' )
        {
            let attrv = $(node).attr("st-font-family");
            if(attrv  !=null)
            {
                $(node).removeAttr("st-font-family");
                $(node).removeClass(attrv);
            }
        }else{
            if(node[0].tagName=="BODY")
            {
                let bdy = node[0].querySelector("#ssf__custom_body_bimg");
                if(bdy!=null)
                {
                    bdy.remove();
                }
            }else{
                node[0].style.removeProperty(type);
            }
        }
        return node;
      },
   }],
  
});

Vvveb.Components.extend("_base", "_base", {
	 properties: [
     {
        key: "display_header",
        inputtype: SectionInput,
        name:false,
        sort: base_sort++,
		section: style_section,
        data: {header:t("Display")},
    },
    //  {
	// 	//linked styles notice message
	// 	name:"",
	// 	key: "linked_styles_check",
    //     sort: base_sort++,
    //     section: style_section,
    //     inline:false,
    //     col:12,
    //     inputtype: NoticeInput,
    //     data: {
	// 		type:'warning',
	// 		title:'Linked styles',
	// 		text:'This element shares styles with other <a class="linked-elements-hover" href="#"><b class="elements-count">4</b> elements</a>, to apply styles <b>only for this element</b> enter a <b>unique id</b> eg: <i>marketing-heading</i> in in <br/><a class="id-input" href="#content-tab" role="tab" aria-controls="components" aria-selected="false" href="#content-tab">Content > General > Id</a>.<br/><span class="text-muted small"></span>',
	// 	},
	// 	afterInit:function(node, inputElement) {
	// 		var selector = Vvveb.StyleManager.getSelectorForElement(node);
	// 		var elements = $(selector, window.FrameDocument);

	// 		if (elements.length <= 1) {
	// 			inputElement.hide();
	// 		} else {
	// 			$(".elements-count", inputElement).html(elements.length);
	// 			$(".text-muted", inputElement).html(selector);
				
	// 			$(".id-input", inputElement).click(function (){
	// 				$(".content-tab a").each(function() {
	// 					this.click();
	// 				});
					
	// 				setTimeout(function () { $("[name=id]").trigger("focus") }, 700);;
					
	// 			});
				
	// 			$(".linked-elements-hover", inputElement).
	// 			on("mouseenter", function (){
	// 				elements.css("outline","2px dotted blue");
	// 			}).
	// 			on("mouseleave", function (){
	// 				elements.css("outline","");
	// 			});
	// 		}
	// 	},	 
	//  },
      {
        name: t("Display"),
        key: "display",
        htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: SelectInput,
        validValues: ["block", "inline", "inline-block", "none"],
        data: {
            options: [{
                value: "block",
                text: t("Block")
            }, {
                value: "inline",
                text: t("Inline")
            }, {
                value: "inline-block",
                text: t("Inline Block")
            }, {
                value: "flex",
                text: t("Flex")
            }, {
                value: "inline-flex",
                text: t("Inline Flex")
            }, {
                value: "grid",
                text: t("Grid")
            }, {
                value: "inline-grid",
                text: t("Inline grid")
            }, {
                value: "table",
                text: t("Table")
            }, {
                value: "table-row",
                text: t("Table Row")
            }, {
                value: "list-item",
                text: t("List Item")
            }, {
                value: "inherit",
                text: t("Inherit")
            }, {
                value: "initial",
                text: t("Initial")
            }, {
                value: "none",
                text: t("none")
            }]
        }
    }, {
        name: t("Position"),
        key: "position",
        htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: SelectInput,
        validValues: ["static", "fixed", "relative", "absolute"],
        data: {
            options: [{
                value: "static",
                text: t("Static")
            }, {
                value: "fixed",
                text: t("Fixed")
            }, {
                value: "relative",
                text: t("Relative")
            }, {
                value: "absolute",
                text: t("Absolute")
            }
            , {
                value: "sticky",
                text: t("Sticky")
            }
            ]
        }
    }, {
        name: t("Top"),
        key: "top",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        parent:"",
        inputtype: CssUnitInput
	}, {
        name: t("Left"),
        key: "left",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        parent:"",
        inputtype: CssUnitInput
    }, {
        name: t("Bottom"),
        key: "bottom",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        parent:"",
        inputtype: CssUnitInput
	}, {
        name: t("Right"),
        key: "right",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        parent:"",
        inputtype: CssUnitInput
    },{
        name: t("Float"),
        key: "float",
        htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:12,
        inline:true,
        inputtype: RadioButtonInput,
        data: {
			extraclass:"btn-group-sm btn-group-fullwidth",
            options: [{
                value: "none",
                icon:"la la-times",
                //text: "None",
                title: t("None"),
                checked:true,
            }, {
                value: "left",
                //text: "Left",
                title: t("Left"),
                icon:"la la-align-left",
                checked:false,
            }, {
                value: "right",
                //text: "Right",
                title: t("Right"),
                icon:"la la-align-right",
                checked:false,
            }],
         }
	},
     {
        name: t("Opacity"),
        key: "opacity",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:12,
		inline:true,
        parent:"",
        inputtype: RangeInput,
        data:{
			max: 1, //max zoom level
			min:0,
			step:0.1
       },
	},
    {
        name: t("Cursor"),
        key: "cursor",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:12,
		inline:true,
        parent:"",
        inputtype: SelectInput,
        data:{
            options: [
            {
                value: "crosshair",
                text: t("crosshair"),
            },
            {
                value: "default",
                text: t("default"),
            },
            {
                value: "e-resize",
                text: t("e-resize"),
            },
            {
                value: "help",
                text: t("help"),
            },
            {
                value: "move",
                text: t("move"),
            },
            {
                value: "grab",
                text: t("grab"),
            },
            {
                value: "grabbing",
                text: t("grabbing"),
            },
            {
                value: "alias",
                text: t("alias"),
            },
            {
                value: "all-scroll",
                text: t("all-scroll"),
            },
            {
                value: "auto",
                text: t("auto"),
            },
            {
                value: "cell",
                text: t("cell"),
            },
            {
                value: "col-resize",
                text: t("col-resize"),
            },
            {
                value: "auto",
                text: t("auto"),
            },
            {
                value: "progress",
                text: t("progress"),
            },
            {
                value: "not-allowed",
                text: t("not-allowed"),
            },
            {
                value: "context-menu",
                text: t("context-menu"),
            },
            {
                value: "copy",
                text: t("copy"),
            },
            {
                value: "pointer",
                text: t("pointer"),
            },
            {
                value: "zoom-out",
                text: t("zoom-out"),
            },
            {
                value: "zoom-in",
                text: t("zoom-in"),
            },
            ],
       },
	},
     {
        name: t("Background Color"),
        key: "background-color",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
		htmlAttr: "style",
        inputtype: ColorInput,
	}, {
        name: t("Text Color"),
        key: "color",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
		htmlAttr: "style",
        inputtype: ColorInput,
  	}, {
        name: t("Z-index"),
        key: "z-index",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
		htmlAttr: "style",
        inputtype: TextInput,
  	}
    ,
    {
        name: t("White Space"),
        key: "white-space",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
		htmlAttr: "style",
        inputtype: SelectInput,
        data: {
            options: [{
                value: "normal",
                text: t("Normal"),
            }, {
                value: "nowrap",
                text: t("Nowrap"),
            }, {
                value: "pre",
                text: t("Pre"),
            }
            , {
                value: "pre-wrap",
                text: t("Pre Wrap"),
            }
            , {
                value: "pre-line",
                text: t("Pre Line"),
            }
            , {
                value: "break-spaces",
                text: t("Break Spaces"),
            }
            , {
                value: "inherit",
                text: t("Inherit"),
            }
            , {
                value: "initial",
                text: t("Initial"),
            }
            , {
                value: "revert",
                text: t("Revert"),
            }
            , {
                value: "prevert-layerre",
                text: t("Prevert Layerre"),
            }
            , {
                value: "unset",
                text: t("Unset"),
            }
            ],
         }
  	    }
    ]
});    

//Typography
Vvveb.Components.extend("_base", "_base", {
	 properties: [
     {
		key: "typography_header",
		inputtype: SectionInput,
		name:false,
		sort: base_sort++,
		section: style_section,
		data: {header:t("Typography")},
 
	}, {
        name: t("Font size"),
        key: "font-size",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
	}, {
        name: t("Font weight"),
        key: "font-weight",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: SelectInput,
        data: {
			options: [{
				value: "",
				text: t("Default")
			}, {	
				value: "100",
				text: t("Thin")
			}, {
				value: "200",
				text: t("Extra-Light")
			}, {
				value: "300",
				text: t("Light")
			}, {
				value: "400",
				text: t("Normal")
			}, {
				value: "500",
				text: t("Medium")
			}, {
				value: "600",
				text: t("Semi-Bold")
			}, {
				value: "700",
				text: t("Bold")
			}, {
				value: "800",
				text: t("Extra-Bold")
			}, {
				value: "900",
				text: t("Ultra-Bold")
			}],
		}
   }, {
        name: t("Font family"),
        key: "font-family",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:12,
		inline:true,
        inputtype: SelectInput,
        data: {
			options: fontList
		}
	}, {
        name: t("Text align"),
        key: "text-align",
        htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:12,
        inline:true,
        inputtype: RadioButtonInput,
        data: {
			extraclass:"btn-group-sm btn-group-fullwidth",
            options: [{
                value: "",
                icon:"la la-times",
                //text: "None",
                title: t("None"),
                checked:true,
            }, {
                value: "left",
                //text: "Left",
                title: t("Left"),
                icon:"la la-align-left",
                checked:false,
            }, {
                value: "center",
                //text: "Center",
                title: t("Center"),
                icon:"la la-align-center",
                checked:false,
            }, {
                value: "right",
                //text: "Right",
                title: t("Right"),
                icon:"la la-align-right",
                checked:false,
            }, {
                value: "justify",
                //text: "justify",
                title: t("Justify"),
                icon:"la la-align-justify",
                checked:false,
            }],
        },
	}, {
        name: t("Line height"),
        key: "line-height",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
	}, {
        name: t("Letter spacing"),
        key: "letter-spacing",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
	}, {
        name: t("Text decoration"),
        key: "text-decoration-line",
        htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:12,
        inline:true,
        inputtype: RadioButtonInput,
        data: {
			extraclass:"btn-group-sm btn-group-fullwidth",
            options: [{
                value: "none",
                icon:"la la-times",
                //text: "None",
                title: t("None"),
                checked:true,
            }, {
                value: "underline",
                //text: "Left",
                title: t("underline"),
                icon:"la la-long-arrow-alt-down",
                checked:false,
            }, {
                value: "overline",
                //text: "Right",
                title: t("overline"),
                icon:"la la-long-arrow-alt-up",
                checked:false,
            }, {
                value: "line-through",
                //text: "Right",
                title: t("Line Through"),
                icon:"la la-strikethrough",
                checked:false,
            }, {
                value: "underline overline",
                //text: "justify",
                title: t("Underline Overline"),
                icon:"la la-arrows-alt-v",
                checked:false,
            }],
        },
	}, {
        name: t("Decoration Color"),
        key: "text-decoration-color",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
		htmlAttr: "style",
        inputtype: ColorInput,
	}, {
        name: t("Decoration style"),
        key: "text-decoration-style",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: SelectInput,
        data: {
			options: [{
				value: "",
				text: t("Default")
			}, {	
				value: "solid",
				text: t("Solid")
			}, {
				value: "wavy",
				text: t("Wavy")
			}, {
				value: "dotted",
				text: t("Dotted")
			}, {
				value: "dashed",
				text: t("Dashed")
			}, {
				value: "double",
				text: t("Double")
			}],
		}
  }]
})
    
//Size
Vvveb.Components.extend("_base", "_base", {
	 properties: [{
		key: "size_header",
		inputtype: SectionInput,
		name:false,
		sort: base_sort++,
		section: style_section,
		data: {header:t("Size"), expanded:false},
	}, {
        name: t("Width"),
        key: "width",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
	}, {
        name: t("Height"),
        key: "height",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
	}, {
        name: t("Min Width"),
        key: "min-width",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
	}, {
        name: t("Min Height"),
        key: "min-height",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
	}, {
        name: t("Max Width"),
        key: "max-width",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
	}, {
        name: t("Max Height"),
        key: "max-height",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
    }]
});

//Margin
Vvveb.Components.extend("_base", "_base", {
	 properties: [{
		key: "margins_header",
		inputtype: SectionInput,
		name:false,
		sort: base_sort++,
		section: style_section,
		data: {header:t("Margin"), expanded:false},
	}, {
        name: t("Top"),
        key: "margin-top",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
	}, {
        name: t("Right"),
        key: "margin-right",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
    }, {
        name: t("Bottom"),
        key: "margin-bottom",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
    }, {
        name: t("Left"),
        key: "margin-left",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
    }]
});

//Padding
Vvveb.Components.extend("_base", "_base", {
	 properties: [{
		key: "paddings_header",
		inputtype: SectionInput,
		name:false,
		sort: base_sort++,
		section: style_section,
		data: {header:t("Padding"), expanded:false},
	}, {
        name: t("Top"),
        key: "padding-top",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
	}, {
        name: t("Right"),
        key: "padding-right",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
    }, {
        name: t("Bottom"),
        key: "padding-bottom",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
    }, {
        name: t("Left"),
        key: "padding-left",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
    }]
});


//Border
Vvveb.Components.extend("_base", "_base", {
	 properties: [{
		key: "border_header",
		inputtype: SectionInput,
		name:false,
		sort: base_sort++,
		section: style_section,
		data: {header:t("Border"), expanded:false},
	 }, {        
        name: t("Style"),
        key: "border-style",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:12,
		inline:true,
        inputtype: SelectInput,
        data: {
			options: [{
				value: "",
				text: t("Default")
			}, {	
				value: "solid",
				text: t("Solid")
			}, {
				value: "dotted",
				text: t("Dotted")
			}, {
				value: "dashed",
				text: t("Dashed")
			}],
		}
	}, {
        name: t("Width"),
        key: "border-width",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
   	}, {
        name: t("Color"),
        key: "border-color",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
		htmlAttr: "style",
        inputtype: ColorInput,
	}]
});    



//Border radius
Vvveb.Components.extend("_base", "_base", {
	 properties: [{
		key: "border_radius_header",
		inputtype: SectionInput,
		name:false,
		sort: base_sort++,
		section: style_section,
		data: {header:t("Border radius"), expanded:false},
	}, {
        name: t("Top Left"),
        key: "border-top-left-radius",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
	}, {
        name: t("Top Right"),
        key: "border-top-right-radius",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
    }, {
        name: t("Bottom Left"),
        key: "border-bottom-left-radius",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
    }, {
        name: t("Bottom Right"),
        key: "border-bottom-right-radius",
		htmlAttr: "style",
        sort: base_sort++,
		section: style_section,
        col:6,
		inline:true,
        inputtype: CssUnitInput
    }]
});

//Background image
Vvveb.Components.extend("_base", "_base", {
	 properties: [{
		key: "background_image_header",
		inputtype: SectionInput,
		name:false,
		sort: base_sort++,
		section: style_section,
		data: {header:t("Background Image"), expanded:false},
	 },{
        name: t("Image"),
        key: "Image",
        sort: base_sort++,
		section: style_section,
		//htmlAttr: "style",
        inputtype: ImageInput,
        
        init: function(node) {
            var image = $(node).css("background-image").replace(/^url\(['"]?(.+)['"]?\)/, '$1');
            image = image.replace('"', '');
			return image;
        },

		onChange: function(node, value) {
            $(node).css('background-image', `url('${value}')`);
			return node;
		}        

   	}, 
       {
        name: t("&nbsp;&nbsp;"),
        key: "bgcfMediaBgImg",
        inputtype: ButtonInput,
        data: {text: t("Import Media")},
        sort: base_sort++,
        onChange: function(node)
        {
            doEditorMediaOpen(
                function(data){
                    $("#thumb-Image").attr("src",data);
                    $("#input-Image").val(data);
                    $(node).css('background-image', `url('${data}')`);
                }
            );
            return node;
        }
    },
       {
        name: t("Repeat"),
        key: "background-repeat",
        sort: base_sort++,
		section: style_section,
		htmlAttr: "style",
        inputtype: SelectInput,
        data: {
			options: [{
				value: "",
				text: t("Default")
			}, {	
				value: "repeat-x",
				text: t("repeat-x")
			}, {
				value: "repeat-y",
				text: t("repeat-y")
			}, {
				value: "no-repeat",
				text: t("no-repeat")
			}],
		}
   	}, {
        name: t("Size"),
        key: "background-size",
        sort: base_sort++,
		section: style_section,
		htmlAttr: "style",
        inputtype: SelectInput,
        data: {
			options: [{
				value: "",
				text: t("Default")
			}, {	
				value: "contain",
				text: t("contain")
			}, {
				value: "cover",
				text: t("cover")
			}],
		}
   	}, {
        name: t("Position x"),
        key: "background-position-x",
        sort: base_sort++,
		section: style_section,
		htmlAttr: "style",
        col:6,
		inline:true,
		inputtype: SelectInput,
        data: {
			options: [{
				value: "",
				text: t("Default")
			}, {	
				value: "center",
				text: t("center")
			}, {	
				value: "right",
				text: t("right")
			}, {
				value: "left",
				text: t("left")
			}],
		}
   	}, {
        name: t("Position y"),
        key: "background-position-y",
        sort: base_sort++,
		section: style_section,
		htmlAttr: "style",
        col:6,
		inline:true,
		inputtype: SelectInput,
        data: {
			options: [{
				value: "",
				text: t("Default")
			}, {	
				value: "center",
				text: t("center")
			}, {	
				value: "top",
				text: t("top")
			}, {
				value: "bottom",
				text: t("bottom")
			}],
		}
    }]
});    

//Add Element
var AddParentElement = {
    properties: [{
       key: "add_parent_element",
       inputtype: SectionInput,
       name:false,
       sort: base_sort++,
       section: advanced_section,
       data: {header:t("Add Parent Element")},
   }, {
       name: t("Select Parent Element"),
       key: "parent_element_set",
       col:12,
       inline:true,
       sort: base_sort++,
       section: advanced_section,
       inputtype: SelectInput,
       data: {
        options:CF_GLOBAL_ELEMENT_TYPES
       }
   }],
};

//Device visibility
var ComponentDeviceVisibility = {
	 properties: [{
		key: "visibility_header",
		inputtype: SectionInput,
		name:false,
		sort: base_sort++,
		section: advanced_section,
		data: {header:t("Hide based on device screen width")},
	}, {
        name: t("Extra small devices"),
        key: "hidexs",
        col:6,
		inline:true,
		sort: base_sort++,
		section: advanced_section,
        htmlAttr: "class",
        validValues: ["d-xs-none"],
        inputtype: ToggleInput,
        data: {
            on: "d-xs-none",
            off: ""
        }
	}, {
        name: t("Small devices"),
        key: "hidesm",
        col:6,
		inline:true,
		sort: base_sort++,
		section: advanced_section,
        htmlAttr: "class",
        validValues: ["d-sm-none"],
        inputtype: ToggleInput,
        data: {
            on: "d-sm-none",
            off: ""
        }
	}, {
        name: t("Medium devices"),
        key: "hidemd",
        col:6,
		inline:true,
		sort: base_sort++,
		section: advanced_section,
        htmlAttr: "class",
        validValues: ["d-md-none"],
        inputtype: ToggleInput,
        data: {
            on: "d-md-none",
            off: ""
        }
	}, {
        name: t("Large devices"),
        key: "hidelg",
        col:6,
		inline:true,
		sort: base_sort++,
		section: advanced_section,
        htmlAttr: "class",
        validValues: ["d-lg-none"],
        inputtype: ToggleInput,
        data: {
            on: "d-lg-none",
            off: ""
        }
	}, {
        name: t("Xl devices"),
        key: "hidexl",
        col:6,
		inline:true,
		sort: base_sort++,
		section: advanced_section,
        htmlAttr: "class",
        validValues: ["d-xl-none"],
        inputtype: ToggleInput,
        data: {
            on: "d-xl-none",
            off: ""
        }
	}, {
        name: t("Xxl devices"),
        key: "hidexxl",
        col:6,
		inline:true,
		sort: base_sort++,
		section: advanced_section,
        htmlAttr: "class",
        validValues: ["d-xxl-none"],
        inputtype: ToggleInput,
        data: {
            on: "d-xxl-none",
            off: ""
        }
    }]
};

Vvveb.Components.extend("_base", "_base", AddParentElement);
Vvveb.Components.extend("_base", "_base", ComponentDeviceVisibility);
