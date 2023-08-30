
		function cfLoadTinyMceEditor(selector_name)
		{
		    tinymce.init({
			selector : selector_name,
			language: cf_tinymce_lang,
			convert_urls : false,
			height: 465,
			plugins: 'image,link,code',
			toolbar: 'undo redo | link image | code | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help | cfMedia  ',

			setup: function(editor){
			editor.ui.registry.addButton('cfMedia', {
			text: 'Import Media',
			onAction: function () {
				openMedia(false, true);
				let _this_editor= editor;
				global_cf_media_export_callback=function(cntnt){
				_this_editor.insertContent(cntnt);
				};
			}
			});
		},
		content_css: [
			'//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
			'//www.tiny.cloud/css/codepen.min.css'
		],
		// enable title field in the Image dialog
		image_title: true,
			images_upload_url : 'req.php',
			automatic_uploads : false,

			images_upload_handler : function(blobInfo, success, failure) {
			var xhr, formData;

			xhr = new XMLHttpRequest();
			xhr.withCredentials = false;
			xhr.open('POST', 'req.php');

			xhr.onload = function() {
				var json;

				if (xhr.status != 200) {
				failure('HTTP Error: ' + xhr.status);
				return;
				}
			
				json = JSON.parse(xhr.responseText.trim());
				
				if (!json || typeof json.location != 'string') {
				failure('Invalid JSON: ' + xhr.responseText);
				return;
				}

				success(json.location);
			};

			formData = new FormData();
			formData.append('tinymceimgupload',1);
			formData.append('file', blobInfo.blob(), blobInfo.filename());

			xhr.send(formData);
			},
		});

		}