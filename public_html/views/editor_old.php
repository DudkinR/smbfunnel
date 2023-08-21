<html>
<head>
<meta charset="UTF-8">
<title>CloudFunnels Editor</title>

<?php
$required_grapes_js_script='
<script type="text/javascript" src="assets/js/grapes-js/node_modules/grapesjs/dist/grapes.min.js"></script>
<link rel="stylesheet" href="assets/js/grapes-js/node_modules/grapesjs/dist/css/grapes.min.css">
<script type="text/javascript" src="assets/js/grapes-js/node_modules/grapesjs-preset-webpage/dist/grapesjs-preset-webpage.min.js"></script>
<link rel="slylesheet" href="assets/js/grapes-js/grapesjs-preset-webpage.min.css"/>

<!-- Export-->
<script src="assets/js/grapes-js/node_modules/grapesjs-plugin-export/dist/grapesjs-plugin-export.min.js"></script>
<!-- image editor-->
<script src="assets/js/grapes-js/node_modules/grapesjs-tui-image-editor/dist/grapesjs-tui-image-editor.min.js"></script>
<!--tool tip-->
<script src="assets/js/grapes-js/node_modules/grapesjs-tooltip/dist/grapesjs-tooltip.min.js"></script>
<!--custom code-->
<script src="assets/js/grapes-js/node_modules/grapesjs-custom-code/dist/grapesjs-custom-code.min.js"></script>
<!-- lory slider -->
<script src="assets/js/grapes-js/node_modules/grapesjs-lory-slider/dist/grapesjs-lory-slider.min.js"></script>
<!-- tabs -->
<script src="assets/js/grapes-js/node_modules/grapesjs-tabs/dist/grapesjs-tabs.min.js"></script>
<!-- touch screen -->
<script src="assets/js/grapes-js/node_modules/grapesjs-touch/dist/grapesjs-touch.min.js"></script>
<!-- code editor-->
<script src="assets/js/grapes-js/node_modules/a-truenorthtechnology/grapesjs-code-editor/dist/a-truenorthtechnology/grapesjs-code-editor.min.js"></script>
<link rel="stylesheet" href="assets/js/grapes-js/node_modules/a-truenorthtechnology/grapesjs-code-editor/dist/a-truenorthtechnology/css/grapes-code-editor.min.css"/>
<!-- countdown -->
<script src="assets/js/grapes-js/node_modules/grapesjs-component-countdown/dist/grapesjs-component-countdown.min.js"></script>
<!-- CK Editor -->
<script src="assets/js/ckeditor/ckeditor.js"></script>
<script src="assets/js/grapes-js/node_modules/grapesjs-plugin-ckeditor/dist/grapesjs-plugin-ckeditor.min.js"></script>
<!-- CSS Gradient -->
<script src="assets/js/grapes-js/node_modules/grapesjs-style-gradient/dist/grapesjs-style-gradient.min.js"></script>
<!-- Grapes JS Typed -->
<script src="assets/js/grapes-js/node_modules/grapesjs-typed/dist/grapesjs-typed.min.js"></script>
<!-- Grapes Post CSS -->
<script src="assets/js/grapes-js/node_modules/grapesjs-parser-postcss/dist/grapesjs-parser-postcss.min.js"></script>
<!-- grapesjs-blocks-flexbox.min -->
<script src="assets/js/grapes-js/node_modules/grapesjs-blocks-flexbox/dist/grapesjs-blocks-flexbox.min.js"></script>
<!-- Grapes JS parser PostCSS  -->
<script src="assets/js/grapes-js/node_modules/grapesjs-parser-postcss/dist/grapesjs-parser-postcss.min.js"></script>
';

echo  $required_grapes_js_script;
?>

<?php echo $header; ?>
</head>
<body>
<div id="wpqgjs"></div>
<script>
var fid=<?php echo $_GET['fid']; ?>;
		var abtype="<?php echo $_GET['abtype']; ?>";
		var lbl="<?php echo $_GET['lbl']; ?>";
		var categ="<?php echo $_GET['category']; ?>";
		var folder="<?php echo $_GET['folder']; ?>";
		var request=new ajaxRequest();
</script>
<script>
const editor=grapesjs.init({
	container: '#wpqgjs',
	commands: {
        defaults: [
          window['@truenorthtechnology/grapesjs-code-editor'].codeCommandFactory(),
        ],
      },
  //allowScripts:1,
	assetManager: {
// Upload endpoint, set `false` to disable upload, default `false`
upload: 'req.php',
storeOnChange : true,
storeAfterUpload : true,
params:{imgstore:1,upload_location:"<?php echo str_replace('@folder@',$_GET['folder'],$content['img_dir']); ?>",img_base_url:"<?php echo str_replace('@folder@',$_GET['folder'],$content['img_url']);?>"},
// The name used in POST to pass uploaded files, default: `'files'`
uploadName: 'files',
},
  plugins: ['gjs-preset-webpage','grapesjs-tui-image-editor','grapesjs-tooltip','grapesjs-custom-code','grapesjs-lory-slider','grapesjs-tabs','grapesjs-touch','gjs-component-countdown','grapesjs-plugin-export',/*'gjs-plugin-ckeditor',*/'grapesjs-parser-postcss','grapesjs-typed','grapesjs-blocks-flexbox','grapesjs-parser-postcss'],
      pluginsOpts: {
     'gjs-component-countdown':{
      'dateInputType': 'datetime-local'
    },
		'grapesjs-lory-slider': {
            sliderBlock: {
              category: 'Extra'
            }
          },
          'grapesjs-tabs': {
            tabsBlock: {
              category: 'Extra'
            }
          },
          'grapesjs-typed': {
            block: {
              category: 'Extra',
              content: {
                type: 'typed',
                'type-speed': 40,
                strings: [
                  'Text row one',
                  'Text row two',
                  'Text row three',
                ],
              }
            }
          },
		  'gjs-preset-webpage': {
            modalImportTitle: 'Import Template',
            modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Paste here your HTML/CSS and click Import</div>',
            modalImportContent: function(editor) {
              return editor.getHtml() + '<style>'+editor.getCss()+'</style>'
            },
            filestackOpts: null, //{ key: 'AYmqZc2e8RLGLE7TGkX3Hz' },
            aviaryOpts: false,
            blocksBasicOpts: { flexGrid: 1 },
            customStyleManager: [{
              name: 'General',
              buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom'],
              properties:[{
                  name: 'Alignment',
                  property: 'float',
                  type: 'radio',
                  defaults: 'none',
                  list: [
                    { value: 'none', className: 'fa fa-times'},
                    { value: 'left', className: 'fa fa-align-left'},
                    { value: 'right', className: 'fa fa-align-right'}
                  ],
                },
                { property: 'position', type: 'select'}
              ],
            },{
                name: 'Dimension',
                open: false,
                buildProps: ['width', 'flex-width', 'height', 'max-width', 'min-height', 'margin', 'padding'],
                properties: [{
                  id: 'flex-width',
                  type: 'integer',
                  name: 'Width',
                  units: ['px', '%'],
                  property: 'flex-basis',
                  toRequire: 1,
                },{
                  property: 'margin',
                  properties:[
                    { name: 'Top', property: 'margin-top'},
                    { name: 'Right', property: 'margin-right'},
                    { name: 'Bottom', property: 'margin-bottom'},
                    { name: 'Left', property: 'margin-left'}
                  ],
                },{
                  property  : 'padding',
                  properties:[
                    { name: 'Top', property: 'padding-top'},
                    { name: 'Right', property: 'padding-right'},
                    { name: 'Bottom', property: 'padding-bottom'},
                    { name: 'Left', property: 'padding-left'}
                  ],
                }],
              },{
                name: 'Typography',
                open: false,
                buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow'],
                properties:[
                  { name: 'Font', property: 'font-family'},
                  { name: 'Weight', property: 'font-weight'},
                  { name:  'Font color', property: 'color'},
                  {
                    property: 'text-align',
                    type: 'radio',
                    defaults: 'left',
                    list: [
                      { value : 'left',  name : 'Left',    className: 'fa fa-align-left'},
                      { value : 'center',  name : 'Center',  className: 'fa fa-align-center' },
                      { value : 'right',   name : 'Right',   className: 'fa fa-align-right'},
                      { value : 'justify', name : 'Justify',   className: 'fa fa-align-justify'}
                    ],
                  },{
                    property: 'text-decoration',
                    type: 'radio',
                    defaults: 'none',
                    list: [
                      { value: 'none', name: 'None', className: 'fa fa-times'},
                      { value: 'underline', name: 'underline', className: 'fa fa-underline' },
                      { value: 'line-through', name: 'Line-through', className: 'fa fa-strikethrough'}
                    ],
                  },{
                    property: 'text-shadow',
                    properties: [
                      { name: 'X position', property: 'text-shadow-h'},
                      { name: 'Y position', property: 'text-shadow-v'},
                      { name: 'Blur', property: 'text-shadow-blur'},
                      { name: 'Color', property: 'text-shadow-color'}
                    ],
                }],
              },{
                name: 'Decorations',
                open: false,
                buildProps: ['opacity', 'background-color', 'border-radius', 'border', 'box-shadow', 'background'],
                properties: [{
                  type: 'slider',
                  property: 'opacity',
                  defaults: 1,
                  step: 0.01,
                  max: 1,
                  min:0,
                },{
                  property: 'border-radius',
                  properties  : [
                    { name: 'Top', property: 'border-top-left-radius'},
                    { name: 'Right', property: 'border-top-right-radius'},
                    { name: 'Bottom', property: 'border-bottom-left-radius'},
                    { name: 'Left', property: 'border-bottom-right-radius'}
                  ],
                },{
                  property: 'box-shadow',
                  properties: [
                    { name: 'X position', property: 'box-shadow-h'},
                    { name: 'Y position', property: 'box-shadow-v'},
                    { name: 'Blur', property: 'box-shadow-blur'},
                    { name: 'Spread', property: 'box-shadow-spread'},
                    { name: 'Color', property: 'box-shadow-color'},
                    { name: 'Shadow type', property: 'box-shadow-type'}
                  ],
                },{
                  property: 'background',
                  properties: [
                    { name: 'Image', property: 'background-image'},
                    { name: 'Repeat', property:   'background-repeat'},
                    { name: 'Position', property: 'background-position'},
                    { name: 'Attachment', property: 'background-attachment'},
                    { name: 'Size', property: 'background-size'}
                  ],
                },],
              },{
                name: 'Extra',
                open: false,
                buildProps: ['transition', 'perspective', 'transform'],
                properties: [{
                  property: 'transition',
                  properties:[
                    { name: 'Property', property: 'transition-property'},
                    { name: 'Duration', property: 'transition-duration'},
                    { name: 'Easing', property: 'transition-timing-function'}
                  ],
                },{
                  property: 'transform',
                  properties:[
                    { name: 'Rotate X', property: 'transform-rotate-x'},
                    { name: 'Rotate Y', property: 'transform-rotate-y'},
                    { name: 'Rotate Z', property: 'transform-rotate-z'},
                    { name: 'Scale X', property: 'transform-scale-x'},
                    { name: 'Scale Y', property: 'transform-scale-y'},
                    { name: 'Scale Z', property: 'transform-scale-z'}
                  ],
                }]
              },{
                name: 'Flex',
                open: false,
                properties: [{
                  name: 'Flex Container',
                  property: 'display',
                  type: 'select',
                  defaults: 'block',
                  list: [
                    { value: 'block', name: 'Disable'},
                    { value: 'flex', name: 'Enable'}
                  ],
                },{
                  name: 'Flex Parent',
                  property: 'label-parent-flex',
                  type: 'integer',
                },{
                  name      : 'Direction',
                  property  : 'flex-direction',
                  type    : 'radio',
                  defaults  : 'row',
                  list    : [{
                            value   : 'row',
                            name    : 'Row',
                            title   : 'Row',
                          },{
                            value   : 'row-reverse',
                            name    : 'Row reverse',
                            title   : 'Row reverse',
                          },{
                            value   : 'column',
                            name    : 'Column',
                            title   : 'Column',
                          },{
                            value   : 'column-reverse',
                            name    : 'Column reverse',
                            title   : 'Column reverse',
                          }],
                },{
                  name      : 'Justify',
                  property  : 'justify-content',
                  type    : 'radio',
                  defaults  : 'flex-start',
                  list    : [{
                            value   : 'flex-start',
                            title   : 'Start',
                          },{
                            value   : 'flex-end',
                            title    : 'End',
                          },{
                            value   : 'space-between',
                            title    : 'Space between',
                          },{
                            value   : 'space-around',
                            title    : 'Space around',
                          },{
                            value   : 'center',
                            title    : 'Center',
                          }],
                },{
                  name      : 'Align',
                  property  : 'align-items',
                  type    : 'radio',
                  defaults  : 'center',
                  list    : [{
                            value   : 'flex-start',
                            title    : 'Start',
                          },{
                            value   : 'flex-end',
                            title    : 'End',
                          },{
                            value   : 'stretch',
                            title    : 'Stretch',
                          },{
                            value   : 'center',
                            title    : 'Center',
                          }],
                },{
                  name: 'Flex Children',
                  property: 'label-parent-flex',
                  type: 'integer',
                },{
                  name:     'Order',
                  property:   'order',
                  type:     'integer',
                  defaults :  0,
                  min: 0
                },{
                  name    : 'Flex',
                  property  : 'flex',
                  type    : 'composite',
                  properties  : [{
                          name:     'Grow',
                          property:   'flex-grow',
                          type:     'integer',
                          defaults :  0,
                          min: 0
                        },{
                          name:     'Shrink',
                          property:   'flex-shrink',
                          type:     'integer',
                          defaults :  0,
                          min: 0
                        },{
                          name:     'Basis',
                          property:   'flex-basis',
                          type:     'integer',
                          units:    ['px','%',''],
                          unit: '',
                          defaults :  'auto',
                        }],
                },{
                  name      : 'Align',
                  property  : 'align-self',
                  type      : 'radio',
                  defaults  : 'auto',
                  list    : [{
                            value   : 'auto',
                            name    : 'Auto',
                          },{
                            value   : 'flex-start',
                            title    : 'Start',
                          },{
                            value   : 'flex-end',
                            title    : 'End',
                          },{
                            value   : 'stretch',
                            title    : 'Stretch',
                          },{
                            value   : 'center',
                            title    : 'Center',
                          }],
                }]
              }
            ],
          },
      }
	
});
//editor.getModel().set('dmode', 'absolute');
editor.Panels.addButton('views', {
    id: 'open-code',
    className: 'fa fa-file-code-o',
    command: 'open-code',
    attributes: {
      title: 'Edit Code'
    }
  });
//devices-c
let javascript_to_use="";
editor.Panels.addButton('devices-c',[{
	id:'save',
	label:'Save Template',
	command:function(editor){
		//alert(this.label);this.label="Ok";
		var html=editor.getHtml();
		html.replace('<link rel="stylesheet" href="<?php echo get_option('install_url'); ?>/assets/fontawesome/css/all.css"//>','');
		var css=editor.getCss();
		var js=editor.getJs();
	
		var formdata=new FormData(document.createElement("form"));
    formdata.append('savetemplate',1);
    formdata.append('reqfrom_editor',1);
		formdata.append('funnel_id',request.escHTML(fid));
		formdata.append('type',request.escHTML(abtype));
		formdata.append('lbl',request.escHTML(lbl));
		formdata.append('category',request.escHTML(categ));
		formdata.append('folder',request.escHTML(folder));
		formdata.append('html',request.escHTML(html));
		formdata.append('css',request.escHTML(css));
    formdata.append('js',request.escHTML(javascript_to_use));
    console.log(javascript_to_use);
		formdata.append("cfhttp",1);
		//console.log(formdata);
		
    var srvr=new XMLHttpRequest();

    var config_alert=document.createElement("div");
		config_alert.setAttribute("style","top:20%;left:50%;transform:translate(-50%,-50%);position:fixed;background-color:white;padding:10px;z-index:10;border-radius:2px;");
		config_alert.innerHTML="<h3><img src='assets/img/visual_cog.gif' style='max-width:100px;margin-left:8px;'>Saving Changes...</h3>";
		document.body.appendChild(config_alert);

		srvr.onreadystatechange=function(){
      if(this.readyState==4 && this.status==200)
      {
        setTimeout(function(){
        request.postRequestCb("req.php",{take_funnel_screenshot:1,funnel:fid,page:folder,abtype:abtype,lavel:lbl,category:categ},(res)=>{
					//console.log(res);
					//console.log("scr taken");
          //resolve(1);

          document.body.removeChild(config_alert);
          
          var savealert=document.createElement("div");
		      savealert.setAttribute("style","top:20%;left:50%;transform:translate(-50%,-50%);position:fixed;background-color:white;padding:10px;z-index:10;border-radius:2px;");
		      savealert.innerHTML="<h3><img src='assets/img/visual_done.gif?id="+Math.random()+"' style='max-width:100px;margin-left:8px;'>Saved Changes</h3>";
		      document.body.appendChild(savealert);
          setTimeout(function(){document.body.removeChild(savealert);},5000);

				});},2000);
      }
    };
		srvr.open("POST","req.php",true);
		srvr.send(formdata);

		request.setCookie('templateedited_'+fid+'_'+lbl,lbl,30);
		
	}
	}]);
<?php
/*
if(strlen($content['html'])>0)
{
echo 'editor.setComponents(atob("'.base64_encode($content['html']).'"));';
}
if(strlen($content['css'])>0)
{
echo 'editor.setStyle(atob("'.base64_encode($content['css']).'"));';
}*/
?>
try
{

    var startup_alert=document.createElement("div");
		startup_alert.setAttribute("style","top:20%;left:50%;transform:translate(-50%,-50%);position:fixed;background-color:white;padding:10px;z-index:10;border-radius:2px;");
		startup_alert.innerHTML="<h3><img src='assets/img/visual_cog.gif' style='max-width:100px;margin-left:8px;'>Loading Template...</h3>";
		document.body.appendChild(startup_alert);

request.postRequestCb("req.php",{"loadalltemplatedata":1,"fid":fid,"abtype":abtype,"lbl":lbl},function(data){
			try
			{
      document.body.removeChild(startup_alert);  
      editor.CssComposer.getAll().reset();
			var contentdatajsn=JSON.parse(data.trim());
      let new_doc=document.createElement("div");
      new_doc.innerHTML=contentdatajsn.editor_html;
      
      let additional_css="";

      try
      {
        new_doc.querySelectorAll("style").forEach((tmpstyle)=>{
          additional_css +=tmpstyle.innerHTML;
          new_doc.removeChild(tmpstyle);
        });

      }
      catch(err){ console.log(err); }

      /*try{
        new_doc.querySelectorAll("script").forEach(temp_script=>{
          new_doc.removeChild(temp_script);
        });
      }catch(err){  console.log(err); }*/

      editor.setComponents(new_doc.innerHTML);

      //editor.setStyle(contentdatajsn.css+additional_css);
      editor.addComponents(`<style>${contentdatajsn.css+additional_css}</style>`);
      javascript_to_use=contentdatajsn.js;

			//editor.setJs(contentdatajsn.js);
			//alert(contentdatajsn.js);
			}catch(errrr){console.log(errrr.message);console.log(data);}
		});
}catch(errrr){alert(errrr.message);}		
		
var domComps = editor.DomComponents;
var dType = domComps.getType('default');

console.log(domComps);

var dModel = dType.model;
var dView = dType.view;
<?php
$inparray=array('input');
 ?>
domComps.addType('input', {
    model: dModel.extend({
      defaults: Object.assign({}, dModel.prototype.defaults, {
        traits: [
          // strings are automatically converted to text types
		   {
            type: 'select',
            label: 'name',
            name: 'name',
            options: [
             <?php
			for($i=0;$i<count($content['input_names']);$i++)
			{
				echo "{value: '".$content['input_names'][$i]."', name: '".$content['input_names'][$i]."'},";
			}
			?>
            ]
          },
          'placeholder',
          {
            type: 'select',
            label: 'Type',
            name: 'type',
            options: [
              {value: 'text', name: 'Text'},
              {value: 'email', name: 'Email'},
              {value: 'password', name: 'Password'},
              {value: 'number', name: 'Number'},
			  {value: 'radio', name: 'Radio'},
			  {value: 'checkbox', name: 'Check Box'},
			  {value: 'textarea', name: 'Text Area'},
            ]
          },
          {
            type: 'text',
            label: 'Id',
            name: 'id',
          },		  
		  {
            type: 'checkbox',
            label: 'Required',
            name: 'required',
          }
		 ],
      }),
    }, {
      isComponent: function(el) {
        if(el.tagName == 'INPUT'){
          return {type: 'input',name: el.name};
        }
      },
    }),

    view: dView,
});

var defaultType = editor.DomComponents.getType("default");
var _initialize = defaultType.model.prototype.initialize;
defaultType.model.prototype.initialize = function() {
	_initialize.apply(this, arguments);

	this.get("traits").add({
		type: "select",
		label: "CF-Loop",
		name: "cf-loop",
		options:[{value:'members',name:'Members'},{value:'products',name:'Products'}],
	});
};

// The upload is started
editor.on('asset:upload:start', () => {
  //startAnimation();
});
editor.on('asset:upload:end', () => {
  //endAnimation();
});
editor.on('asset:upload:error', (err) => {
	alert(err);
});

editor.on('asset:upload:response', (response) => {
	if(response !=0)
	{
	editor.AssetManager.add(response);
	}
});
</script>

<style>
.someClass
{
	padding:10px;
	background-color:green;
}
</style>
</body>
</html>