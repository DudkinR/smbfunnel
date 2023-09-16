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

Vvveb.ComponentsGroup["Bootstrap 5"] = [
  "html/container",
  "html/gridrow",
  "html/button",
  "html/button_link",
  "html/buttongroup",
  "html/buttontoolbar",
  "html/heading",
  "html/image",
  "html/jumbotron",
  "html/alert",
  "html/card",
  "html/cardimage",
  "html/listgroup",
  "html/hr",
  "html/taglabel",
  "html/badge",
  "html/progress",
  "html/navbar",
  "html/breadcrumbs",
  "html/pagination",
  "html/form",
  "html/textinput",
  "html/textareainput",
  "html/selectinput",
  "html/fileinput",
  "html/checkbox",
  "html/radiobutton",
  "html/table",
  "html/paragraph",
  "html/division",
  "html/span",
  "html/collapse_controller",
  "html/collapse_area",
  "html/country_code_selector",
  "html/country_selector",
  "html/link",
  "html/video",
  "html/audio",
  "html/button_bootstrap",
];

var base_sort = 100; //start sorting for base component from 100 to allow extended properties to be first
var style_section = "style";

Vvveb.Components.extend("_base", "html/container", {
  classes: ["container", "container-fluid"],
  image: "icons/container.svg",
  html: '<div class="container" style="min-height:150px;"><div class="m-5">Container</div></div>',
  name: t("Container"),
  properties: [
    {
      name: t("Type"),
      key: "type",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: ["container", "container-fluid"],
      data: {
        options: [
          {
            value: "container",
            text: t("Default"),
          },
          {
            value: "container-fluid",
            text: t("Fluid"),
          },
        ],
      },
    },
    {
      name: t("Background"),
      key: "background",
      htmlAttr: "class",
      validValues: bgcolorClasses,
      inputtype: SelectInput,
      data: {
        options: bgcolorSelectOptions,
      },
    },
    {
      name: t("Background Color"),
      key: "background-color",
      htmlAttr: "style",
      inputtype: ColorInput,
    },
    {
      name: t("Text Color"),
      key: "color",
      htmlAttr: "style",
      inputtype: ColorInput,
    },
  ],
});

Vvveb.Components.extend("_base", "html/heading", {
  image: "icons/heading.svg",
  name: t("Heading"),
  nodes: ["h1", "h2", "h3", "h4", "h5", "h6"],
  html: "<h1>Heading</h1>",

  properties: [
    {
      name: t("Size"),
      key: "size",
      inputtype: SelectInput,

      onChange: function (node, value) {
        return changeNodeName(node, "h" + value);
      },

      init: function (node) {
        var regex;
        regex = /H(\d)/.exec(node.nodeName);
        if (regex && regex[1]) {
          return regex[1];
        }
        return 1;
      },

      data: {
        options: [
          {
            value: "1",
            text: "1",
          },
          {
            value: "2",
            text: "2",
          },
          {
            value: "3",
            text: "3",
          },
          {
            value: "4",
            text: "4",
          },
          {
            value: "5",
            text: "5",
          },
          {
            value: "6",
            text: "6",
          },
        ],
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/link", {
  nodes: ["a"],
  name: t("Link"),
  html: '<a href="#" class="d-inline-block"><span>Link</span></a>',
  image: "icons/link.svg",
  init: function (node) {
    let paging = ``;
    var pages = Vvveb.allpages;
    let href = $(node).find('a').attr("href");
    for (i in pages) {
      if (pages[i] == href) {
        paging += `<option value="${pages[i]}" selected>${pages[i]}</option>`;
      } else {
        paging += `<option value="${pages[i]}">${pages[i]}</option>`;
      }
    }
    $("#right-panel [data-key=all_pages_hrefs] select").html(paging);
  },
  properties: [
    {
      name: t("URL"),
      key: "href",
      htmlAttr: "href",
      inputtype: LinkInput,
    },
    {
      name: t("Download Link"),
      key: "ddownload",
      htmlAttr: "download",
      inputtype: SelectInput,
      data: {
        options: [
          { value: "1", text: "Yes" },
          { value: null, text: "No" },
        ],
      },
    },
    {
      name: t("Select Page"),
      key: "all_pages_hrefs",
      htmlAttr: "href",
      inputtype: SelectInput,
      data: {
        options: [

        ]
      }
    },
    {
      name: t("Target"),
      key: "target",
      htmlAttr: "target",
      inputtype: SelectInput,
      data: {
        options: [
          { value: null, text: "Same Tab" },
          { value: "_BLANK", text: "New Tab" },
        ],
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/image", {
  nodes: ["img"],
  name: t("Image"),
  html: '<img src="' + Vvveb.baseUrl + 'icons/image.svg" class="img-fluid">',
  /*
    afterDrop: function (node)
  {
    node.attr("src", '');
    return node;
  },*/
  resizable: true, //show select box resize handlers
  image: "icons/image.svg",
  properties: [
    {
      name: t("Image"),
      key: "src",
      htmlAttr: "src",
      inputtype: ImageInput,
    },
    {
      name: t("&nbsp;&nbsp;"),
      key: "cfMediaImg",
      inputtype: ButtonInput,
      data: { text: t("Import Media") },
      onChange: function (node) {
        doEditorMediaOpen(function (data) {
          $("#thumb-Image").attr("src", data);
          $("#input-Image").val(data);
          $(node).attr("src", data);
        });
        return node;
      },
    },
    {
      name: t("Width"),
      key: "width",
      htmlAttr: "width",
      inputtype: TextInput,
    },
    {
      name: t("Height"),
      key: "height",
      htmlAttr: "height",
      inputtype: TextInput,
    },
    {
      name: t("Alt"),
      key: "alt",
      htmlAttr: "alt",
      inputtype: TextInput,
    },
    {
      name: t("srcset"),
      key: "srcset",
      htmlAttr: "srcset",
      inputtype: TextInput,
    },
  ],
});
Vvveb.Components.add("html/hr", {
  image: "icons/hr.svg",
  nodes: ["hr"],
  name: t("Horizontal Rule"),
  html: "<hr>",
});
Vvveb.Components.extend("_base", "html/label", {
  name: t("Label"),
  nodes: ["label"],
  html: '<label for="">Label</label>',
  properties: [
    {
      name: t("For id"),
      htmlAttr: "for",
      key: "for",
      inputtype: TextInput,
    },
  ],
});
Vvveb.Components.extend("_base", "html/button", {
  nodes: ["button"],
  name: t("Button"),
  image: "icons/button.svg",
  html: '<button type="button" class="btn btn-primary">Click here</button>',
  properties: [
    {
      name: t("Action Type"),
      key: "type",
      htmlAttr: "type",
      inputtype: SelectInput,
      validValues: ["submit", "reset", "button"],
      data: {
        options: [
          {
            value: "button",
            text: t("button"),
          },
          {
            value: "reset",
            text: t("reset"),
          },
          {
            value: "submit",
            text: t("submit"),
          },
        ],
      },
    },
    {
      name: t("Type"),
      key: "type",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: [
        "btn btn-default",
        "btn btn-primary",
        "btn btn-info",
        "btn btn-success",
        "btn btn-warning",
        "btn btn-info",
        "btn btn-light",
        "btn btn-dark",
        "btn btn-outline-primary",
        "btn btn-outline-info",
        "btn btn-outline-success",
        "btn btn-outline-warning",
        "btn btn-outline-info",
        "btn btn-outline-light",
        "btn btn-outline-dark",
        "btn btn-link",
      ],
      data: {
        options: [
          {
            value: "btn btn-default",
            text: t("Default"),
          },
          {
            value: "btn btn-primary",
            text: t("Primary"),
          },
          {
            value: "btn btn btn-info",
            text: t("Info"),
          },
          {
            value: "btn btn-success",
            text: t("Success"),
          },
          {
            value: "btn btn-warning",
            text: t("Warning"),
          },
          {
            value: "btn btn-info",
            text: t("Info"),
          },
          {
            value: "btn btn-light",
            text: t("Light"),
          },
          {
            value: "btn btn-dark",
            text: t("Dark"),
          },
          {
            value: "btn btn-outline-primary",
            text: t("Primary outline"),
          },
          {
            value: "btn btn-outline-info",
            text: t("Info outline"),
          },
          {
            value: "btn btn-outline-success",
            text: t("Success outline"),
          },
          {
            value: "btn btn-outline-warning",
            text: t("Warning outline"),
          },
          {
            value: "btn btn-outline-info",
            text: t("Info outline"),
          },
          {
            value: "btn btn-outline-light",
            text: t("Light outline"),
          },
          {
            value: "btn btn-outline-dark",
            text: t("Dark outline"),
          },
          {
            value: "btn btn-link",
            text: t("Link"),
          },
        ],
      },
    },
    {
      name: t("Size"),
      key: "size",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: ["btn-lg", "btn-sm"],
      data: {
        options: [
          {
            value: "",
            text: t("Default"),
          },
          {
            value: "btn-lg",
            text: t("Large"),
          },
          {
            value: "btn-sm",
            text: t("Small"),
          },
        ],
      },
    },
    {
      name: t("Disabled"),
      key: "disabled",
      htmlAttr: "disabled",
      inputtype: ToggleInput,
      validValues: ["disabled"],
      data: {
        on: "disabled",
        off: null,
      },
    },
  ],
});

Vvveb.Components.extend("_base", "html/button_link", {
  classes: ["cst_btn"],
  name: t("Link Button"),
  image: "icons/button.svg",
  html: '<a class="btn btn-primary cst_btn">Click Here</a>',
  properties: [
    {
      name: t("Link To"),
      key: "href",
      htmlAttr: "href",
      inputtype: LinkInput,
    },
    {
      name: t("Type"),
      key: "type",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: [
        "btn btn-default",
        "btn btn-primary",
        "btn btn-info",
        "btn btn-success",
        "btn btn-warning",
        "btn btn-info",
        "btn btn-light",
        "btn btn-dark",
        "btn btn-outline-primary",
        "btn btn-outline-info",
        "btn btn-outline-success",
        "btn btn-outline-warning",
        "btn btn-outline-info",
        "btn btn-outline-light",
        "btn btn-outline-dark",
        "btn btn-link",
      ],
      data: {
        options: [
          {
            value: "btn btn-default",
            text: t("Default"),
          },
          {
            value: "btn btn-primary",
            text: t("Primary"),
          },
          {
            value: "btn btn btn-info",
            text: t("Info"),
          },
          {
            value: "btn btn-success",
            text: t("Success"),
          },
          {
            value: "btn btn-warning",
            text: t("Warning"),
          },
          {
            value: "btn btn-info",
            text: t("Info"),
          },
          {
            value: "btn btn-light",
            text: t("Light"),
          },
          {
            value: "btn btn-dark",
            text: t("Dark"),
          },
          {
            value: "btn btn-outline-primary",
            text: t("Primary outline"),
          },
          {
            value: "btn btn-outline-info",
            text: t("Info outline"),
          },
          {
            value: "btn btn-outline-success",
            text: t("Success outline"),
          },
          {
            value: "btn btn-outline-warning",
            text: t("Warning outline"),
          },
          {
            value: "btn btn-outline-info",
            text: t("Info outline"),
          },
          {
            value: "btn btn-outline-light",
            text: t("Light outline"),
          },
          {
            value: "btn btn-outline-dark",
            text: t("Dark outline"),
          },
          {
            value: "btn btn-link",
            text: t("Link"),
          },
        ],
      },
    },
    {
      name: t("Size"),
      key: "size",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: ["btn-lg", "btn-sm"],
      data: {
        options: [
          {
            value: "",
            text: t("Default"),
          },
          {
            value: "btn-lg",
            text: t("Large"),
          },
          {
            value: "btn-sm",
            text: t("Small"),
          },
        ],
      },
    },
    {
      name: t("Download link"),
      key: "ddownload",
      htmlAttr: "download",
      inputtype: SelectInput,
      data: {
        options: [
          { value: "1", text: "Yes" },
          { value: null, text: "No" },
        ],
      },
    },
    {
      name: t("Target"),
      key: "target",
      htmlAttr: "target",
      inputtype: SelectInput,
      data: {
        options: [
          { value: null, text: "Same Tab" },
          { value: "_BLANK", text: "New Tab" },
        ],
      },
    },
    {
      name: t("Disabled"),
      key: "disabled",
      htmlAttr: "disabled",
      inputtype: ToggleInput,
      validValues: ["disabled"],
      data: {
        on: "disabled",
        off: null,
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/buttongroup", {
  classes: ["btn-group"],
  name: t("Button Group"),
  image: "icons/button_group.svg",
  html: '<div class="btn-group" role="group" aria-label="Basic example"><button type="button" class="btn btn-secondary">Left</button><button type="button" class="btn btn-secondary">Middle</button> <button type="button" class="btn btn-secondary">Right</button></div>',
  properties: [
    {
      name: t("Size"),
      key: "size",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: ["btn-group-lg", "btn-group-sm"],
      data: {
        options: [
          {
            value: "",
            text: t("Default"),
          },
          {
            value: "btn-group-lg",
            text: t("Large"),
          },
          {
            value: "btn-group-sm",
            text: t("Small"),
          },
        ],
      },
    },
    {
      name: t("Alignment"),
      key: "alignment",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: ["btn-group", "btn-group-vertical"],
      data: {
        options: [
          {
            value: "",
            text: t("Default"),
          },
          {
            value: "btn-group",
            text: t("Horizontal"),
          },
          {
            value: "btn-group-vertical",
            text: t("Vertical"),
          },
        ],
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/buttontoolbar", {
  classes: ["btn-toolbar"],
  name: t("Button Toolbar"),
  image: "icons/button_toolbar.svg",
  html: '<div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">\
		  <div class="btn-group me-2" role="group" aria-label="First group">\
			<button type="button" class="btn btn-secondary">1</button>\
			<button type="button" class="btn btn-secondary">2</button>\
			<button type="button" class="btn btn-secondary">3</button>\
			<button type="button" class="btn btn-secondary">4</button>\
		  </div>\
		  <div class="btn-group me-2" role="group" aria-label="Second group">\
			<button type="button" class="btn btn-secondary">5</button>\
			<button type="button" class="btn btn-secondary">6</button>\
			<button type="button" class="btn btn-secondary">7</button>\
		  </div>\
		  <div class="btn-group" role="group" aria-label="Third group">\
			<button type="button" class="btn btn-secondary">8</button>\
		  </div>\
		</div>',
});
Vvveb.Components.extend("_base", "html/alert", {
  classes: ["alert"],
  name: t("Alert"),
  image: "icons/alert.svg",
  html: '<div class="alert alert-warning alert-dismissible fade show" role="alert">\
		  <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">\
			<span aria-hidden="true">&times;</span>\
		  </button>\
		  <strong>Holy guacamole!</strong> You should check in on some of those fields below.\
		</div>',
  properties: [
    {
      name: t("Type"),
      key: "type",
      htmlAttr: "class",
      validValues: [
        "alert-primary",
        "alert-secondary",
        "alert-success",
        "alert-danger",
        "alert-warning",
        "alert-info",
        "alert-light",
        "alert-dark",
      ],
      inputtype: SelectInput,
      data: {
        options: [
          {
            value: "alert-primary",
            text: t("Default"),
          },
          {
            value: "alert-secondary",
            text: t("Secondary"),
          },
          {
            value: "alert-success",
            text: t("Success"),
          },
          {
            value: "alert-danger",
            text: t("Danger"),
          },
          {
            value: "alert-warning",
            text: t("Warning"),
          },
          {
            value: "alert-info",
            text: t("Info"),
          },
          {
            value: "alert-light",
            text: t("Light"),
          },
          {
            value: "alert-dark",
            text: t("Dark"),
          },
        ],
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/badge", {
  classes: ["badge"],
  image: "icons/badge.svg",
  name: t("Badge"),
  html: '<span class="badge badge-primary">Primary badge</span>',
  properties: [
    {
      name: t("Color"),
      key: "color",
      htmlAttr: "class",
      validValues: [
        "badge-primary",
        "badge-secondary",
        "badge-success",
        "badge-danger",
        "badge-warning",
        "badge-info",
        "badge-light",
        "badge-dark",
      ],
      inputtype: SelectInput,
      data: {
        options: [
          {
            value: "",
            text: t("Default"),
          },
          {
            value: "badge-primary",
            text: t("Primary"),
          },
          {
            value: "badge-secondary",
            text: t("Secondary"),
          },
          {
            value: "badge-success",
            text: t("Success"),
          },
          {
            value: "badge-warning",
            text: t("Warning"),
          },
          {
            value: "badge-danger",
            text: t("Danger"),
          },
          {
            value: "badge-info",
            text: t("Info"),
          },
          {
            value: "badge-light",
            text: t("Light"),
          },
          {
            value: "badge-dark",
            text: t("Dark"),
          },
        ],
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/card", {
  classes: ["card"],
  image: "icons/panel.svg",
  name: t("Card"),
  html: `<div class="card">
    <div class="card-header" style="font-size:20px">Card</div>
    <div class="card-body">\
      <h4 class="card-title">Card title</h4>\
      <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card\'s content.</p>\
      <a href="#" class="btn btn-primary">Go somewhere</a>\
    </div>\
    <div class="card-footer">Here some footer text</div>
  </div>`,
});
Vvveb.Components.extend("_base", "html/cardimage", {
  classes: ["card"],
  image: "icons/panel.svg",
  name: t("Card With Image"),
  html:
    '<div class="card">\
		  <img class="card-img-top" src="' +
    Vvveb.baseUrl +
    'icons/image.svg" alt="Card image cap" width="128" height="128">\
		  <div class="card-body">\
			<h4 class="card-title">Card title</h4>\
			<p class="card-text">Some quick example text to build on the card title and make up the bulk of the card\'s content.</p>\
			<a href="#" class="btn btn-primary">Go somewhere</a>\
		  </div>\
		</div>',
});
Vvveb.Components.extend("_base", "html/listgroup", {
  name: t("List Group"),
  image: "icons/list_group.svg",
  classes: ["list-group"],
  html: '<ul class="list-group">\n  <li class="list-group-item">\n    <span class="badge">14</span>\n    Cras justo odio\n  </li>\n  <li class="list-group-item">\n    <span class="badge">2</span>\n    Dapibus ac facilisis in\n  </li>\n  <li class="list-group-item">\n    <span class="badge">1</span>\n    Morbi leo risus\n  </li>\n</ul>',
});
Vvveb.Components.extend("_base", "html/listitem", {
  name: t("List Item"),
  classes: ["list-group-item"],
  html: '<li class="list-group-item"><span class="badge">14</span> Cras justo odio</li>',
});
Vvveb.Components.extend("_base", "html/breadcrumbs", {
  classes: ["breadcrumb"],
  name: t("Breadcrumbs"),
  image: "icons/breadcrumbs.svg",
  html: '<ol class="breadcrumb">\
		  <li class="breadcrumb-item active"><a href="#">Home</a></li>\
		  <li class="breadcrumb-item active"><a href="#">Library</a></li>\
		  <li class="breadcrumb-item active">Data 3</li>\
		</ol>',
});
Vvveb.Components.extend("_base", "html/breadcrumbitem", {
  classes: ["breadcrumb-item"],
  name: t("Breadcrumb Item"),
  html: '<li class="breadcrumb-item"><a href="#">Library</a></li>',
  properties: [
    {
      name: t("Active"),
      key: "active",
      htmlAttr: "class",
      validValues: ["", "active"],
      inputtype: ToggleInput,
      data: {
        on: "active",
        off: "",
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/pagination", {
  classes: ["pagination"],
  name: t("Pagination"),
  image: "icons/pagination.svg",
  html: '<nav aria-label="Page navigation example">\
	  <ul class="pagination">\
		<li class="page-item"><a class="page-link" href="#">Previous</a></li>\
		<li class="page-item"><a class="page-link" href="#">1</a></li>\
		<li class="page-item"><a class="page-link" href="#">2</a></li>\
		<li class="page-item"><a class="page-link" href="#">3</a></li>\
		<li class="page-item"><a class="page-link" href="#">Next</a></li>\
	  </ul>\
	</nav>',

  properties: [
    {
      name: t("Size"),
      key: "size",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: ["btn-lg", "btn-sm"],
      data: {
        options: [
          {
            value: "",
            text: t("Default"),
          },
          {
            value: "btn-lg",
            text: t("Large"),
          },
          {
            value: "btn-sm",
            text: t("Small"),
          },
        ],
      },
    },
    {
      name: t("Alignment"),
      key: "alignment",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: ["justify-content-center", "justify-content-end"],
      data: {
        options: [
          {
            value: "",
            text: t("Default"),
          },
          {
            value: "justify-content-center",
            text: t("Center"),
          },
          {
            value: "justify-content-end",
            text: t("Right"),
          },
        ],
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/pageitem", {
  classes: ["page-item"],
  html: '<li class="page-item"><a class="page-link" href="#">1</a></li>',
  name: t("Pagination Item"),
  properties: [
    {
      name: t("Link To"),
      key: "href",
      htmlAttr: "href",
      child: ".page-link",
      inputtype: TextInput,
    },
    {
      name: t("Disabled"),
      key: "disabled",
      htmlAttr: "class",
      validValues: ["disabled"],
      inputtype: ToggleInput,
      data: {
        on: "disabled",
        off: "",
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/progress", {
  classes: ["progress"],
  name: t("Progress Bar"),
  image: "icons/progressbar.svg",
  html: '<div class="progress"><div class="progress-bar w-25"></div></div>',
  properties: [
    {
      name: t("Background"),
      key: "background",
      htmlAttr: "class",
      validValues: bgcolorClasses,
      inputtype: SelectInput,
      data: {
        options: bgcolorSelectOptions,
      },
    },
    {
      name: t("Progress"),
      key: "background",
      child: ".progress-bar",
      htmlAttr: "class",
      validValues: ["", "w-25", "w-50", "w-75", "w-100"],
      inputtype: SelectInput,
      data: {
        options: [
          {
            value: "",
            text: t("None"),
          },
          {
            value: "w-25",
            text: "25%",
          },
          {
            value: "w-50",
            text: "50%",
          },
          {
            value: "w-75",
            text: "75%",
          },
          {
            value: "w-100",
            text: "100%",
          },
        ],
      },
    },
    {
      name: t("Progress background"),
      key: "background",
      child: ".progress-bar",
      htmlAttr: "class",
      validValues: bgcolorClasses,
      inputtype: SelectInput,
      data: {
        options: bgcolorSelectOptions,
      },
    },
    {
      name: t("Striped"),
      key: "striped",
      child: ".progress-bar",
      htmlAttr: "class",
      validValues: ["", "progress-bar-striped"],
      inputtype: ToggleInput,
      data: {
        on: "progress-bar-striped",
        off: "",
      },
    },
    {
      name: t("Animated"),
      key: "animated",
      child: ".progress-bar",
      htmlAttr: "class",
      validValues: ["", "progress-bar-animated"],
      inputtype: ToggleInput,
      data: {
        on: "progress-bar-animated",
        off: "",
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/jumbotron", {
  classes: ["jumbotron"],
  image: "icons/jumbotron.svg",
  name: t("Jumbotron"),
  html: '<div class="jumbotron">\
		  <h1 class="display-3">Hello, world!</h1>\
		  <p class="lead">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>\
		  <hr class="my-4">\
		  <p>It uses utility classes for typography and spacing to space content out within the larger container.</p>\
		  <p class="lead">\
			<a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a>\
		  </p>\
		</div>',
});
Vvveb.Components.extend("_base", "html/navbar", {
  classes: ["navbar"],
  image: "icons/navbar.svg",
  name: t("Nav Bar"),
  html: '<nav class="navbar navbar-expand-lg navbar-light bg-light">\
		  <a class="navbar-brand" href="#">Navbar</a>\
		  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">\
			<span class="navbar-toggler-icon"></span>\
		  </button>\
		\
		  <div class="collapse navbar-collapse" id="navbarSupportedContent">\
			<ul class="navbar-nav me-auto">\
			  <li class="nav-item active">\
				<a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>\
			  </li>\
			  <li class="nav-item">\
				<a class="nav-link" href="#">Link</a>\
			  </li>\
			  <li class="nav-item">\
				<a class="nav-link disabled" href="#">Disabled</a>\
			  </li>\
			</ul>\
			<form class="form-inline my-2 my-lg-0">\
			  <input class="form-control me-sm-2" type="text" placeholder="Search" aria-label="Search">\
			  <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>\
			</form>\
		  </div>\
		</nav>',

  properties: [
    {
      name: t("Color theme"),
      key: "color",
      htmlAttr: "class",
      validValues: ["navbar-light", "navbar-dark"],
      inputtype: SelectInput,
      data: {
        options: [
          {
            value: "",
            text: t("Default"),
          },
          {
            value: "navbar-light",
            text: t("Light"),
          },
          {
            value: "navbar-dark",
            text: t("Dark"),
          },
        ],
      },
    },
    {
      name: t("Background color"),
      key: "background",
      htmlAttr: "class",
      validValues: bgcolorClasses,
      inputtype: SelectInput,
      data: {
        options: bgcolorSelectOptions,
      },
    },
    {
      name: t("Placement"),
      key: "placement",
      htmlAttr: "class",
      validValues: ["fixed-top", "fixed-bottom", "sticky-top"],
      inputtype: SelectInput,
      data: {
        options: [
          {
            value: "",
            text: t("Default"),
          },
          {
            value: "fixed-top",
            text: t("Fixed Top"),
          },
          {
            value: "fixed-bottom",
            text: t("Fixed Bottom"),
          },
          {
            value: "sticky-top",
            text: t("Sticky top"),
          },
        ],
      },
    },
  ],
});

Vvveb.Components.extend("_base", "html/form", {
  nodes: ["form"],
  image: "icons/form.svg",
  name: t("Form"),
  html: `<form action="" method="POST"><div class="mb-3">
    <label for="email">Email address:</label>
    <input type="email" class="form-control" id="email">
  </div>
  <div class="mb-3">
    <label for="pwd">Password:</label>
    <input type="password" class="form-control" id="pwd">
  </div>
  <div class="checkbox">
    <label><input type="checkbox"> Remember me</label>
  </div>
  <button type="submit" class="btn btn-default">Submit</button></form>`,
  properties: [
    {
      name: t("Style"),
      key: "style",
      htmlAttr: "class",
      validValues: ["", "form-search", "form-inline", "form-horizontal"],
      inputtype: SelectInput,
      data: {
        options: [
          {
            value: "",
            text: t("Default"),
          },
          {
            value: "form-search",
            text: t("Search"),
          },
          {
            value: "form-inline",
            text: t("Inline"),
          },
          {
            value: "form-horizontal",
            text: t("Horizontal"),
          },
        ],
      },
    },
    {
      name: t("Action"),
      key: "action",
      htmlAttr: "action",
      inputtype: TextInput,
    },
    {
      name: t("Method"),
      key: "method",
      htmlAttr: "method",
      inputtype: TextInput,
    },
  ],
});

Vvveb.Components.extend("_base", "html/textinput", {
  name: t("Input"),
  nodes: ["input"],
  //attributes: {"type":"text"},
  image: "icons/text_input.svg",
  html: '<div class="mb-3"><label>Text</label><input type="text" class="form-control"></div></div>',
  properties: [
    {
      name: t("Value"),
      key: "value",
      htmlAttr: "value",
      inputtype: TextInput,
    },
    {
      name: t("Type"),
      key: "type",
      htmlAttr: "type",
      inputtype: SelectInput,
      data: {
        options: [
          {
            value: "text",
            text: t("text"),
          },
          {
            value: "button",
            text: t("button"),
          },
          {
            value: "checkbox",
            text: t("checkbox"),
          },
          {
            value: "color",
            text: t("color"),
          },
          {
            value: "date",
            text: t("date"),
          },
          {
            value: "datetime-local",
            text: t("datetime-local"),
          },
          {
            value: "email",
            text: t("email"),
          },
          {
            value: "file",
            text: t("file"),
          },
          {
            value: "hidden",
            text: t("hidden"),
          },
          {
            value: "image",
            text: t("image"),
          },
          {
            value: "month",
            text: t("month"),
          },
          {
            value: "number",
            text: t("number"),
          },
          {
            value: "password",
            text: t("password"),
          },
          {
            value: "radio",
            text: t("radio"),
          },
          {
            value: "range",
            text: t("range"),
          },
          {
            value: "reset",
            text: t("reset"),
          },
          {
            value: "search",
            text: t("search"),
          },
          {
            value: "submit",
            text: t("submit"),
          },
          {
            value: "tel",
            text: t("tel"),
          },
          {
            value: "text",
            text: t("text"),
          },
          {
            value: "time",
            text: t("time"),
          },
          {
            value: "url",
            text: t("url"),
          },
          {
            value: "week",
            text: t("week"),
          },
        ],
      },
    },
    {
      name: t("Placeholder"),
      key: "placeholder",
      htmlAttr: "placeholder",
      inputtype: TextInput,
    }, // {
    //     name: "Disabled",
    //     key: "disabled",
    //     htmlAttr: "disabled",
    // 	col:6,
    //     inputtype: CheckboxInput,
    // },{
    //     name: "Required",
    //     key: "required",
    //     htmlAttr: "required",
    // 	col:6,
    //     inputtype: CheckboxInput,
    // },
  ],
});
Vvveb.Components.extend("_base", "html/selectinput", {
  nodes: ["select"],
  name: t("Select Input"),
  image: "icons/select_input.svg",
  html: '<div class="mb-3"><label>Choose an option </label><select class="form-control form-select"><option value="value1">Text 1</option><option value="value2">Text 2</option><option value="value3">Text 3</option></select></div>',

  beforeInit: function (node) {
    properties = [];
    var i = 0;

    $(node)
      .find("option")
      .each(function () {
        data = { value: this.value, text: this.text };

        i++;
        properties.push({
          name: "Option " + i,
          key: "option" + i,
          //index: i - 1,
          optionNode: this,
          inputtype: TextValueInput,
          data: data,
          onChange: function (node, value, input) {
            option = $(this.optionNode);

            //if remove button is clicked remove option and render row properties
            if (input.nodeName == "BUTTON") {
              option.remove();
              Vvveb.Components.render("html/selectinput");
              return node;
            }

            if (input.name == "value") option.attr("value", value);
            else if (input.name == "text") option.text(value);

            return node;
          },
        });
      });

    //remove all option properties
    this.properties = this.properties.filter(function (item) {
      return item.key.indexOf("option") === -1;
    });

    //add remaining properties to generated column properties
    properties.push(this.properties[0]);

    this.properties = properties;
    return node;
  },

  properties: [
    {
      name: t("Option"),
      key: "option1",
      inputtype: TextValueInput,
    },
    {
      name: t("Option"),
      key: "option2",
      inputtype: TextValueInput,
    },
    {
      name: "",
      key: "addChild",
      inputtype: ButtonInput,
      data: { text: "Add option", icon: "la-plus" },
      onChange: function (node) {
        $(node).append('<option value="value">Text</option>');

        //render component properties again to include the new column inputs
        Vvveb.Components.render("html/selectinput");

        return node;
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/textareainput", {
  name: t("Text Area"),
  image: "icons/text_area.svg",
  html: '<div class="mb-3"><label>Your response:</label><textarea class="form-control"></textarea></div>',
});
Vvveb.Components.extend("_base", "html/radiobutton", {
  name: t("Radio Button"),
  attributes: { type: "radio" },
  image: "icons/radio.svg",
  html: '<label class="radio"><input type="radio"> Radio</label>',
  properties: [
    {
      name: t("Value"),
      key: "value",
      htmlAttr: "value",
      inputtype: TextInput,
    },
    {
      name: t("Type"),
      key: "type",
      htmlAttr: "type",
      inputtype: SelectInput,
      data: {
        options: [

          {
            value: "checkbox",
            text: t("checkbox"),
          },
          {
            value: "radio",
            text: t("radio"),
          },

        ],
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/checkbox", {
  name: t("Checkbox"),
  attributes: { type: "checkbox" },
  image: "icons/checkbox.svg",
  html: '<label class="checkbox"><input type="checkbox"> Checkbox</label>',
  properties: [
    {
      name: t("Value"),
      key: "value",
      htmlAttr: "value",
      inputtype: TextInput,
    },
    {
      name: t("Type"),
      key: "type",
      htmlAttr: "type",
      inputtype: SelectInput,
      data: {
        options: [

          {
            value: "checkbox",
            text: t("checkbox"),
          },
          {
            value: "radio",
            text: t("radio"),
          },

        ],
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/fileinput", {
  name: t("Input group"),
  attributes: { type: "file" },
  image: "icons/text_input.svg",
  html: '<div class="mb-3">\
			  <input type="file" class="form-control">\
			</div>',
});
Vvveb.Components.extend("_base", "html/table", {
  nodes: ["table"],
  classes: ["table"],
  image: "icons/table.svg",
  name: t("Table"),
  html: '<table class="table">\
		  <thead>\
			<tr>\
			  <th>#</th>\
			  <th>First Name</th>\
			  <th>Last Name</th>\
			  <th>Username</th>\
			</tr>\
		  </thead>\
		  <tbody>\
			<tr>\
			  <th scope="row">1</th>\
			  <td>Mark</td>\
			  <td>Otto</td>\
			  <td>@mdo</td>\
			</tr>\
			<tr>\
			  <th scope="row">2</th>\
			  <td>Jacob</td>\
			  <td>Thornton</td>\
			  <td>@fat</td>\
			</tr>\
			<tr>\
			  <th scope="row">3</th>\
			  <td>Larry</td>\
			  <td>the Bird</td>\
			  <td>@twitter</td>\
			</tr>\
		  </tbody>\
		</table>',
  properties: [
    {
      name: t("Type"),
      key: "type",
      htmlAttr: "class",
      validValues: [
        "table-primary",
        "table-secondary",
        "table-success",
        "table-danger",
        "table-warning",
        "table-info",
        "table-light",
        "table-dark",
        "table-white",
      ],
      inputtype: SelectInput,
      data: {
        options: [
          {
            value: "Default",
            text: "",
          },
          {
            value: "table-primary",
            text: t("Primary"),
          },
          {
            value: "table-secondary",
            text: t("Secondary"),
          },
          {
            value: "table-success",
            text: t("Success"),
          },
          {
            value: "table-danger",
            text: t("Danger"),
          },
          {
            value: "table-warning",
            text: t("Warning"),
          },
          {
            value: "table-info",
            text: t("Info"),
          },
          {
            value: "table-light",
            text: t("Light"),
          },
          {
            value: "table-dark",
            text: t("Dark"),
          },
          {
            value: "table-white",
            text: t("White"),
          },
        ],
      },
    },
    {
      name: t("Responsive"),
      key: "responsive",
      htmlAttr: "class",
      validValues: ["table-responsive"],
      inputtype: ToggleInput,
      data: {
        on: "table-responsive",
        off: "",
      },
    },
    {
      name: t("Small"),
      key: "small",
      htmlAttr: "class",
      validValues: ["table-sm"],
      inputtype: ToggleInput,
      data: {
        on: "table-sm",
        off: "",
      },
    },
    {
      name: t("Hover"),
      key: "hover",
      htmlAttr: "class",
      validValues: ["table-hover"],
      inputtype: ToggleInput,
      data: {
        on: "table-hover",
        off: "",
      },
    },
    {
      name: t("Bordered"),
      key: "bordered",
      htmlAttr: "class",
      validValues: ["table-bordered"],
      inputtype: ToggleInput,
      data: {
        on: "table-bordered",
        off: "",
      },
    },
    {
      name: t("Striped"),
      key: "striped",
      htmlAttr: "class",
      validValues: ["table-striped"],
      inputtype: ToggleInput,
      data: {
        on: "table-striped",
        off: "",
      },
    },
    {
      name: t("Inverse"),
      key: "inverse",
      htmlAttr: "class",
      validValues: ["table-inverse"],
      inputtype: ToggleInput,
      data: {
        on: "table-inverse",
        off: "",
      },
    },
    {
      name: t("Head options"),
      key: "head",
      htmlAttr: "class",
      child: "thead",
      inputtype: SelectInput,
      validValues: ["", "thead-inverse", "thead-default"],
      data: {
        options: [
          {
            value: "",
            text: "None",
          },
          {
            value: "thead-default",
            text: t("Default"),
          },
          {
            value: "thead-inverse",
            text: t("Inverse"),
          },
        ],
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/tablerow", {
  nodes: ["tr"],
  name: t("Table Row"),
  html: "<tr><td>Cell 1</td><td>Cell 2</td><td>Cell 3</td></tr>",
  properties: [
    {
      name: t("Type"),
      key: "type",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: ["", "success", "danger", "warning", "active"],
      data: {
        options: [
          {
            value: "",
            text: t("Default"),
          },
          {
            value: "success",
            text: t("Success"),
          },
          {
            value: "error",
            text: t("Error"),
          },
          {
            value: "warning",
            text: t("Warning"),
          },
          {
            value: "active",
            text: t("Active"),
          },
        ],
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/tablecell", {
  nodes: ["td"],
  name: t("Table Cell"),
  html: "<td>Cell</td>",
});
Vvveb.Components.extend("_base", "html/tableheadercell", {
  nodes: ["th"],
  name: t("Table Header Cell"),
  html: "<th>Head</th>",
});
Vvveb.Components.extend("_base", "html/tablehead", {
  nodes: ["thead"],
  name: t("Table Head"),
  html: "<thead><tr><th>Head 1</th><th>Head 2</th><th>Head 3</th></tr></thead>",
  properties: [
    {
      name: t("Type"),
      key: "type",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: ["", "success", "danger", "warning", "info"],
      data: {
        options: [
          {
            value: "",
            text: t("Default"),
          },
          {
            value: "success",
            text: t("Success"),
          },
          {
            value: "anger",
            text: t("Error"),
          },
          {
            value: "warning",
            text: t("Warning"),
          },
          {
            value: "info",
            text: t("Info"),
          },
        ],
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/tablebody", {
  nodes: ["tbody"],
  name: t("Table Body"),
  html: "<tbody><tr><td>Cell 1</td><td>Cell 2</td><td>Cell 3</td></tr></tbody>",
});

Vvveb.Components.extend("_base", "html/gridcolumn", {
  name: t("Grid Column"),
  image: "icons/grid_row.svg",
  classesRegex: ["col-"],
  html: '<div class="col-sm-4"><h3>col-sm-4</h3></div>',
  properties: [
    {
      name: t("Column"),
      key: "column",
      inputtype: GridInput,
      data: { hide_remove: true },

      beforeInit: function (node) {
        _class = $(node).attr("class");

        var reg = /col-([^-\$ ]*)?-?(\d+)/g;
        var match;

        while ((match = reg.exec(_class)) != null) {
          this.data["col" + (match[1] != undefined ? "_" + match[1] : "")] =
            match[2];
        }
      },

      onChange: function (node, value, input) {
        var _class = node.attr("class");

        //remove previous breakpoint column size
        _class = _class.replace(new RegExp(input.name + "-\\d+?"), "");
        //add new column size
        if (value) _class += " " + input.name + "-" + value;
        node.attr("class", _class);

        return node;
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/gridrow", {
  name: t("Grid Row"),
  image: "icons/grid_row.svg",
  classes: ["row"],
  html: '<div class="row"><div class="col-sm-4"><h3>col-sm-4</h3></div><div class="col-sm-4 col-5"><h3>col-sm-4</h3></div><div class="col-sm-4"><h3>col-sm-4</h3></div></div>',
  children: [
    {
      name: "html/gridcolumn",
      classesRegex: ["col-"],
    },
  ],
  beforeInit: function (node) {
    properties = [];
    var i = 0;
    var j = 0;

    $(node)
      .find('[class*="col-"]')
      .each(function () {
        _class = $(this).attr("class");

        var reg = /col-([^-\$ ]*)?-?(\d+)/g;
        var match;
        var data = {};

        while ((match = reg.exec(_class)) != null) {
          data["col" + (match[1] != undefined ? "_" + match[1] : "")] =
            match[2];
        }

        i++;
        properties.push({
          name: "Column " + i,
          key: "column" + i,
          //index: i - 1,
          columnNode: this,
          col: 12,
          inline: true,
          inputtype: GridInput,
          data: data,
          onChange: function (node, value, input) {
            //column = $('[class*="col-"]:eq(' + this.index + ')', node);
            var column = $(this.columnNode);

            //if remove button is clicked remove column and render row properties
            if (input.nodeName == "BUTTON") {
              column.remove();
              Vvveb.Components.render("html/gridrow");
              return node;
            }

            //if select input then change column class
            _class = column.attr("class");

            //remove previous breakpoint column size
            _class = _class.replace(new RegExp(input.name + "-\\d+?"), "");
            //add new column size
            if (value) _class += " " + input.name + "-" + value;
            column.attr("class", _class);

            //console.log(this, node, value, input, input.name);

            return node;
          },
        });
      });

    //remove all column properties
    this.properties = this.properties.filter(function (item) {
      return item.key.indexOf("column") === -1;
    });

    //add remaining properties to generated column properties
    properties.push(this.properties[0]);

    this.properties = properties;
    return node;
  },

  properties: [
    {
      name: t("Column"),
      key: "column1",
      inputtype: GridInput,
    },
    {
      name: t("Column"),
      key: "column1",
      inline: true,
      col: 12,
      inputtype: GridInput,
    },
    {
      name: "",
      key: "addChild",
      inputtype: ButtonInput,
      data: { text: t("Add column"), icon: "la la-plus" },
      onChange: function (node) {
        $(node).append('<div class="col-3">Col-3</div>');

        //render component properties again to include the new column inputs
        Vvveb.Components.render("html/gridrow");

        return node;
      },
    },
  ],
});

Vvveb.Components.extend("_base", "html/paragraph", {
  nodes: ["p"],
  name: t("Paragraph"),
  image: "icons/paragraph.svg",
  html: "<p>Lorem ipsum</p>",
  properties: [
    {
      name: t("Text align"),
      key: "text-align",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: ["", "text-left", "text-center", "text-right"],
      inputtype: RadioButtonInput,
      data: {
        extraclass: "btn-group-sm btn-group-fullwidth",
        options: [
          {
            value: "",
            icon: "la la-times",
            //text: "None",
            title: t("None"),
            checked: true,
          },
          {
            value: "text-left",
            //text: "Left",
            title: t("Left"),
            icon: "la la-align-left",
            checked: false,
          },
          {
            value: "text-center",
            //text: "Center",
            title: t("Center"),
            icon: "la la-align-center",
            checked: false,
          },
          {
            value: "text-right",
            //text: "Right",
            title: t("Right"),
            icon: "la la-align-right",
            checked: false,
          },
        ],
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/division", {
  nodes: ["div"],
  name: t("Division"),
  image: "icons/jumbotron.svg",
  html: "<div>Lorem ipsum</div>",
  properties: [
    {
      name: t("Text align"),
      key: "text-align",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: ["", "text-left", "text-center", "text-right"],
      inputtype: RadioButtonInput,
      data: {
        extraclass: "btn-group-sm btn-group-fullwidth",
        options: [
          {
            value: "",
            icon: "la la-times",
            //text: "None",
            title: t("None"),
            checked: true,
          },
          {
            value: "text-left",
            //text: "Left",
            title: t("Left"),
            icon: "la la-align-left",
            checked: false,
          },
          {
            value: "text-center",
            //text: "Center",
            title: t("Center"),
            icon: "la la-align-center",
            checked: false,
          },
          {
            value: "text-right",
            //text: "Right",
            title: t("Right"),
            icon: "la la-align-right",
            checked: false,
          },
        ],
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/span", {
  nodes: ["span"],
  name: t("Span"),
  image: "icons/variables/type.png",
  html: "<span>Lorem ipsum</span>",
  properties: [
    {
      name: t("Text align"),
      key: "text-align",
      htmlAttr: "class",
      inputtype: SelectInput,
      validValues: ["", "text-left", "text-center", "text-right"],
      inputtype: RadioButtonInput,
      data: {
        extraclass: "btn-group-sm btn-group-fullwidth",
        options: [
          {
            value: "",
            icon: "la la-times",
            //text: "None",
            title: t("None"),
            checked: true,
          },
          {
            value: "text-left",
            //text: "Left",
            title: t("Left"),
            icon: "la la-align-left",
            checked: false,
          },
          {
            value: "text-center",
            //text: "Center",
            title: t("Center"),
            icon: "la la-align-center",
            checked: false,
          },
          {
            value: "text-right",
            //text: "Right",
            title: t("Right"),
            icon: "la la-align-right",
            checked: false,
          },
        ],
      },
    },
  ],
  onChange: function (node, property, value) {
    if (property == "text-align") {
      node.removeClass("text-left text-center text-right");
    }

  },
});

Vvveb.Components.extend("_base", "html/collapse_controller", {
  name: t("Collapse Controller"),
  attributes: [`data-bs-toggle-collapse-controller`],
  image: "icons/variables/collapse-controller.png",
  html: /*html*/ `<div data-bs-toggle-collapse-controller data-bs-target="#" aria-expanded="false" data-el-type="div" style="padding: 4px; cursor: pointer;">Collapse header</div>`,
  properties: [
    {
      name: t("Element type"),
      key: "collapse_target_eltype",
      htmlAttr: "data-el-type",
      inputtype: SelectInput,
      data: {
        options: CF_GLOBAL_ELEMENT_TYPES,
      },
    },
    {
      name: t("Target"),
      key: "collapse_target_key",
      htmlAttr: "data-bs-target",
      inputtype: TextInput,
    },
    {
      name: t("Area expanded"),
      key: "should_aria_stay_expandable",
      htmlAttr: "aria-expanded",
      inputtype: SelectInput,
      data: {
        options: [
          { value: "true", text: "True" },
          { value: "false", text: "False" },
        ],
      },
    },
  ],
  onChange: function (node, property, type) {
    if (property.key === "collapse_target_eltype") {
      let newEl = $(`<${type}></${type}>`);
      $.each(node[0].attributes, (index, attribute) => {
        let key = attribute.name;
        let val = attribute.value;
        if (key === "href" && type !== "a") {
          return;
        }
        if (type === "a" && key === "data-bs-target") {
          newEl.attr("href", val);
        }
        newEl.attr(key, val);
      });

      let html = $(node[0]).html();
      newEl.html(html);
      node.replaceWith(newEl);
      //console.log(">> HTML", html);
      //return node;
    }
  },
});

Vvveb.Components.extend("_base", "html/collapse_area", {
  name: t("Collapse Area"),
  attributes: [`data-bs-toggle-collapse-area`],
  image: "icons/variables/collapse-area.png",
  html: /*html*/ `<div data-bs-toggle-collapse-area data-el-type="div" data_keep_area_visible="false" style="padding: 8px;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</div>`,
  properties: [
    {
      name: t("Element type"),
      key: "collapse_target_eltype",
      htmlAttr: "data-el-type",
      inputtype: SelectInput,
      data: {
        options: CF_GLOBAL_ELEMENT_TYPES,
      },
    },
    {
      name: t("Area Id"),
      key: "collapse_area_area_id",
      htmlAttr: "id",
      inputtype: TextInput,
    },
    {
      name: t("Keep visible"),
      key: "should_aria_stay_expandable",
      htmlAttr: "data_keep_area_visible",
      inputtype: SelectInput,
      data: {
        options: [
          { value: "true", text: "Yes" },
          { value: "false", text: "No" },
        ],
      },
    },
  ],
  onChange: function (node, property, type) {
    if (property.key === "collapse_target_eltype") {
      let newEl = $(`<${type}></${type}>`);
      $.each(node[0].attributes, (index, attribute) => {
        let key = attribute.name;
        let val = attribute.value;
        newEl.attr(key, val);
      });

      let html = $(node[0]).html();
      newEl.html(html);
      node.replaceWith(newEl);
    }
  },
});
Vvveb.Components.extend("_base", "html/audio", {
  name: t("Audio"),
  attributes: ["data-component-audio"],
  dragHtml: '<img  width="100" height="100" src="' + Vvveb.baseUrl + 'icons/audio.svg">',
  image: "icons/audio.svg",
  html: `<div data-component-audio autoplay='1' controls='1' loop='1' style="min-height:50px;width:328px;position:relative;padding:6px;" data-audio-url="${Vvveb.baseUrl}icons/audio.mp3" data-audio-height="auto" data-audio-width="328px"><audio   playsinline loop autoplay controls  controlsList="nodownload" style="width:100%;" src="${Vvveb.baseUrl}icons/audio.mp3"></audio></div>`,


  url: '', //html5 video src
  autoplay: false,
  loop: false,
  muted: false,
  // resizable: true, //show select box resize handlers
  init: function (node) {
    let audio = jQuery('audio', node);
    let src = audio.attr("src");
    this.url = src;
    this.autoplay = $(node).attr("autoplay");
    this.controls = $(node).attr("controls");
    this.loop = $(node).attr("loop");

    $("#right-panel input[name=data-audio-url]").val(this.url);
  },

  onChange: function (node, property, value) {
    let srcs = $(node).find('audio').attr("src");

    if (property.key == "data-audio-url" && (value != 'undefined' || value != '')) {
      this.url = value;

    } else {
      if (this.url != 'undefined') {
        this.url = srcs;
      }
    }

    let max_height = "50px";
    let max_width = "328px";

    try {

      max_height = $(`input[name="vvebaudio_maxheight"]`).val();
      max_width = $(`input[name="vvebaudio_maxwidth"]`).val();
      if (property.key == "autoplay" && value == true) {
        this.autoplay = true;

      } else {
        this.autoplay = true;
      }
      if (property.key == "muted" && value == true) {
        this.muted = true;
      }
      else {
        this.muted = true;
      }
      if (property.key == "loop" && value == true) {
        this.loop = true;
      }
      else {
        this.loop = true;
      }

    }
    catch (err) {
      console.log(err);
    }
    this[property.key] = value;
    let proptype = $(node).prop("tagName");
    if (proptype != undefined) {
      let newnode = '';
      proptype = proptype.toLowerCase();
      if ((["audio"].indexOf(proptype) > -1) || $(node).attr("data-component-audio") !== undefined) {
        let muted = (this.muted ? ' muted="1" ' : '');
        let autoplay = (this.autoplay ? ' autoplay="1" ' : '');
        let loop = (this.loop ? ' loop="1" ' : '');
        $("#right-panel [data-key=url]").show();
        newnode = $(`<div data-component-audio  ${muted} ${autoplay} ${loop} controls="1"  style="min-height:50px;width:328px;position:relative;padding:6px;" data-audio-url="${Vvveb.baseUrl}icons/audio.mp3" data-audio-height="auto" data-audio-width="328px"><audio  controls  ${muted} ${autoplay} ${loop}  controlslist="nodownload"   style="width: 100%;" src="${this.url}" ></audio></div>`);

        try {
          $(newnode).css("height", max_height);
          $(newnode).attr("data-audio-height", max_height);
          $(newnode).css("width", max_width);
          $(newnode).attr("data-audio-width", max_width);
        }
        catch (err) {
          console.log(err);
        }
        node.replaceWith(newnode);
        return node;
      }
    }
    return node;
  },

  properties: [

    {
      name: t("Height (Value in 'px')"),
      key: "vvebaudio_maxheight",
      htmlAttr: "data-audio-height",
      inputtype: TextInput,
    },
    {
      name: t("Width (Value in 'px' or '%')"),
      key: "vvebaudio_maxwidth",
      htmlAttr: "data-audio-width",
      inputtype: TextInput,
    },
    {
      name: t("URL"),
      key: "data-audio-url",
      htmlAttr: "url",
      inputtype: TextInput,
    },
    {
      name: "Autoplay",
      key: "autoplay",
      htmlAttr: "autoplay",
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
Vvveb.Components.extend("_base", "html/video", {
  nodes: ["video"],
  name: t("Video"),
  html: `<video width="320" height="240" playsinline loop autoplay controls  controlsList="nodownload" src="${Vvveb.baseUrl}icons/video.mp4"></video>`,
  dragHtml:
    '<img  width="320" height="240" src="' +
    Vvveb.baseUrl +
    'icons/video.svg">',
  image: "icons/video.svg",
  resizable: true, //show select box resize handlers
  init: function (node) {
    let url = '';
    let poster = '';
    url = node.src;
    poster = node.poster;
    $("#right-panel input[name=cfMediaVideoSrc]").val(url);
    $("#right-panel input[name=poster]").val(poster);
  },
  properties: [
    {
      name: t("Src"),
      key: "cfMediaVideoSrc",
      htmlAttr: "src",
      inputtype: LinkInput,
    },
    {
      name: t("&nbsp;&nbsp;"),
      key: "cfMediaVideo",
      inputtype: ButtonInput,
      data: { text: t("Import Media") },
      onChange: function (node) {
        doEditorMediaOpen(function (data) {
          $("input[name=cfMediaVideoSrc]").val(data);
          $(node).attr("src", data);
        });
        return node;
      },
    },
    {
      name: t("Width"),
      key: "width",
      htmlAttr: "width",
      inputtype: TextInput,
    },
    {
      name: t("Height"),
      key: "height",
      htmlAttr: "height",
      inputtype: TextInput,
    },
    {
      name: t("Muted"),
      key: "muted",
      htmlAttr: "muted",
      inputtype: CheckboxInput,
    },
    {
      name: t("Loop"),
      key: "loop",
      htmlAttr: "loop",
      inputtype: CheckboxInput,
    },
    {
      name: t("Autoplay"),
      key: "autoplay",
      htmlAttr: "autoplay",
      inputtype: CheckboxInput,
    },
    {
      name: t("Plays inline"),
      key: "playsinline",
      htmlAttr: "playsinline",
      inputtype: CheckboxInput,
    },
    {
      name: t("Controls"),
      key: "controls",
      htmlAttr: "controls",
      inputtype: CheckboxInput,
    },
    {
      name: t("Poster"),
      key: "poster",
      htmlAttr: "poster",
      inputtype: TextInput,
    },
    {
      name: t("&nbsp;&nbsp;"),
      key: "cfMediaPosterVideo",
      inputtype: ButtonInput,
      data: { text: t("Import Media") },
      onChange: function (node) {
        doEditorMediaOpen(function (data) {
          $("input[name=poster]").val(data);
          $(node).attr("poster", data);
        });
        return node;
      },
    },
    {
      name: t("Prevent Download"),
      key: "controlsList",
      htmlAttr: "controlsList",
      inputtype: SelectInput,
      data: {
        options: [
          {
            value: "",
            text: t("No"),
          },
          {
            value: "nodownload",
            text: t("Yes"),
          },
        ],
      },
    },
  ],
});


Vvveb.Components.extend("_base", "html/country_code_selector", {
  name: t("Country code selector"),
  image: "icons/variables/conuntry-code.png",
  attribute: `data-default-country-code`,
  html: `<div class="mb-3">
      <label>Select country</label>
      <select class="form-control form-control form-select" id="country" name="country" sf-input-data-value="{member.country}">
        <option class="form-control" value="">select country</option>
        <option value="AF">Afghanistan</option>
        <option value="AX">Aland Islands</option>
        <option value="AL">Albania</option>
        <option value="DZ">Algeria</option>
        <option value="AS">American Samoa</option>
        <option value="AD">Andorra</option>
        <option value="AO">Angola</option>
        <option value="AI">Anguilla</option>
        <option value="AQ">Antarctica</option>
        <option value="AG">Antigua and Barbuda</option>
        <option value="AR">Argentina</option>
        <option value="AM">Armenia</option>
        <option value="AW">Aruba</option>
        <option value="AU">Australia</option>
        <option value="AT">Austria</option>
        <option value="AZ">Azerbaijan</option>
        <option value="BS">Bahamas</option>
        <option value="BH">Bahrain</option>
        <option value="BD">Bangladesh</option>
        <option value="BB">Barbados</option>
        <option value="BY">Belarus</option>
        <option value="BE">Belgium</option>
        <option value="BZ">Belize</option>
        <option value="BJ">Benin</option>
        <option value="BM">Bermuda</option>
        <option value="BT">Bhutan</option>
        <option value="BO">Bolivia</option>
        <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
        <option value="BA">Bosnia and Herzegovina</option>
        <option value="BW">Botswana</option>
        <option value="BV">Bouvet Island</option>
        <option value="BR">Brazil</option>
        <option value="IO">British Indian Ocean Territory</option>
        <option value="BN">Brunei Darussalam</option>
        <option value="BG">Bulgaria</option>
        <option value="BF">Burkina Faso</option>
        <option value="BI">Burundi</option>
        <option value="KH">Cambodia</option>
        <option value="CM">Cameroon</option>
        <option value="CA">Canada</option>
        <option value="CV">Cape Verde</option>
        <option value="KY">Cayman Islands</option>
        <option value="CF">Central African Republic</option>
        <option value="TD">Chad</option>
        <option value="CL">Chile</option>
        <option value="CN">China</option>
        <option value="CX">Christmas Island</option>
        <option value="CC">Cocos (Keeling) Islands</option>
        <option value="CO">Colombia</option>
        <option value="KM">Comoros</option>
        <option value="CG">Congo</option>
        <option value="CD">Congo, Democratic Republic of the Congo</option>
        <option value="CK">Cook Islands</option>
        <option value="CR">Costa Rica</option>
        <option value="CI">Cote D'Ivoire</option>
        <option value="HR">Croatia</option>
        <option value="CU">Cuba</option>
        <option value="CW">Curacao</option>
        <option value="CY">Cyprus</option>
        <option value="CZ">Czech Republic</option>
        <option value="DK">Denmark</option>
        <option value="DJ">Djibouti</option>
        <option value="DM">Dominica</option>
        <option value="DO">Dominican Republic</option>
        <option value="EC">Ecuador</option>
        <option value="EG">Egypt</option>
        <option value="SV">El Salvador</option>
        <option value="GQ">Equatorial Guinea</option>
        <option value="ER">Eritrea</option>
        <option value="EE">Estonia</option>
        <option value="ET">Ethiopia</option>
        <option value="FK">Falkland Islands (Malvinas)</option>
        <option value="FO">Faroe Islands</option>
        <option value="FJ">Fiji</option>
        <option value="FI">Finland</option>
        <option value="FR">France</option>
        <option value="GF">French Guiana</option>
        <option value="PF">French Polynesia</option>
        <option value="TF">French Southern Territories</option>
        <option value="GA">Gabon</option>
        <option value="GM">Gambia</option>
        <option value="GE">Georgia</option>
        <option value="DE">Germany</option>
        <option value="GH">Ghana</option>
        <option value="GI">Gibraltar</option>
        <option value="GR">Greece</option>
        <option value="GL">Greenland</option>
        <option value="GD">Grenada</option>
        <option value="GP">Guadeloupe</option>
        <option value="GU">Guam</option>
        <option value="GT">Guatemala</option>
        <option value="GG">Guernsey</option>
        <option value="GN">Guinea</option>
        <option value="GW">Guinea-Bissau</option>
        <option value="GY">Guyana</option>
        <option value="HT">Haiti</option>
        <option value="HM">Heard Island and Mcdonald Islands</option>
        <option value="VA">Holy See (Vatican City State)</option>
        <option value="HN">Honduras</option>
        <option value="HK">Hong Kong</option>
        <option value="HU">Hungary</option>
        <option value="IS">Iceland</option>
        <option value="IN">India</option>
        <option value="ID">Indonesia</option>
        <option value="IR">Iran, Islamic Republic of</option>
        <option value="IQ">Iraq</option>
        <option value="IE">Ireland</option>
        <option value="IM">Isle of Man</option>
        <option value="IL">Israel</option>
        <option value="IT">Italy</option>
        <option value="JM">Jamaica</option>
        <option value="JP">Japan</option>
        <option value="JE">Jersey</option>
        <option value="JO">Jordan</option>
        <option value="KZ">Kazakhstan</option>
        <option value="KE">Kenya</option>
        <option value="KI">Kiribati</option>
        <option value="KP">Korea, Democratic People's Republic of</option>
        <option value="KR">Korea, Republic of</option>
        <option value="XK">Kosovo</option>
        <option value="KW">Kuwait</option>
        <option value="KG">Kyrgyzstan</option>
        <option value="LA">Lao People's Democratic Republic</option>
        <option value="LV">Latvia</option>
        <option value="LB">Lebanon</option>
        <option value="LS">Lesotho</option>
        <option value="LR">Liberia</option>
        <option value="LY">Libyan Arab Jamahiriya</option>
        <option value="LI">Liechtenstein</option>
        <option value="LT">Lithuania</option>
        <option value="LU">Luxembourg</option>
        <option value="MO">Macao</option>
        <option value="MK">Macedonia, the Former Yugoslav Republic of</option>
        <option value="MG">Madagascar</option>
        <option value="MW">Malawi</option>
        <option value="MY">Malaysia</option>
        <option value="MV">Maldives</option>
        <option value="ML">Mali</option>
        <option value="MT">Malta</option>
        <option value="MH">Marshall Islands</option>
        <option value="MQ">Martinique</option>
        <option value="MR">Mauritania</option>
        <option value="MU">Mauritius</option>
        <option value="YT">Mayotte</option>
        <option value="MX">Mexico</option>
        <option value="FM">Micronesia, Federated States of</option>
        <option value="MD">Moldova, Republic of</option>
        <option value="MC">Monaco</option>
        <option value="MN">Mongolia</option>
        <option value="ME">Montenegro</option>
        <option value="MS">Montserrat</option>
        <option value="MA">Morocco</option>
        <option value="MZ">Mozambique</option>
        <option value="MM">Myanmar</option>
        <option value="NA">Namibia</option>
        <option value="NR">Nauru</option>
        <option value="NP">Nepal</option>
        <option value="NL">Netherlands</option>
        <option value="AN">Netherlands Antilles</option>
        <option value="NC">New Caledonia</option>
        <option value="NZ">New Zealand</option>
        <option value="NI">Nicaragua</option>
        <option value="NE">Niger</option>
        <option value="NG">Nigeria</option>
        <option value="NU">Niue</option>
        <option value="NF">Norfolk Island</option>
        <option value="MP">Northern Mariana Islands</option>
        <option value="NO">Norway</option>
        <option value="OM">Oman</option>
        <option value="PK">Pakistan</option>
        <option value="PW">Palau</option>
        <option value="PS">Palestinian Territory, Occupied</option>
        <option value="PA">Panama</option>
        <option value="PG">Papua New Guinea</option>
        <option value="PY">Paraguay</option>
        <option value="PE">Peru</option>
        <option value="PH">Philippines</option>
        <option value="PN">Pitcairn</option>
        <option value="PL">Poland</option>
        <option value="PT">Portugal</option>
        <option value="PR">Puerto Rico</option>
        <option value="QA">Qatar</option>
        <option value="RE">Reunion</option>
        <option value="RO">Romania</option>
        <option value="RU">Russian Federation</option>
        <option value="RW">Rwanda</option>
        <option value="BL">Saint Barthelemy</option>
        <option value="SH">Saint Helena</option>
        <option value="KN">Saint Kitts and Nevis</option>
        <option value="LC">Saint Lucia</option>
        <option value="MF">Saint Martin</option>
        <option value="PM">Saint Pierre and Miquelon</option>
        <option value="VC">Saint Vincent and the Grenadines</option>
        <option value="WS">Samoa</option>
        <option value="SM">San Marino</option>
        <option value="ST">Sao Tome and Principe</option>
        <option value="SA">Saudi Arabia</option>
        <option value="SN">Senegal</option>
        <option value="RS">Serbia</option>
        <option value="CS">Serbia and Montenegro</option>
        <option value="SC">Seychelles</option>
        <option value="SL">Sierra Leone</option>
        <option value="SG">Singapore</option>
        <option value="SX">Sint Maarten</option>
        <option value="SK">Slovakia</option>
        <option value="SI">Slovenia</option>
        <option value="SB">Solomon Islands</option>
        <option value="SO">Somalia</option>
        <option value="ZA">South Africa</option>
        <option value="GS">South Georgia and the South Sandwich Islands</option>
        <option value="SS">South Sudan</option>
        <option value="ES">Spain</option>
        <option value="LK">Sri Lanka</option>
        <option value="SD">Sudan</option>
        <option value="SR">Suriname</option>
        <option value="SJ">Svalbard and Jan Mayen</option>
        <option value="SZ">Swaziland</option>
        <option value="SE">Sweden</option>
        <option value="CH">Switzerland</option>
        <option value="SY">Syrian Arab Republic</option>
        <option value="TW">Taiwan, Province of China</option>
        <option value="TJ">Tajikistan</option>
        <option value="TZ">Tanzania, United Republic of</option>
        <option value="TH">Thailand</option>
        <option value="TL">Timor-Leste</option>
        <option value="TG">Togo</option>
        <option value="TK">Tokelau</option>
        <option value="TO">Tonga</option>
        <option value="TT">Trinidad and Tobago</option>
        <option value="TN">Tunisia</option>
        <option value="TR">Turkey</option>
        <option value="TM">Turkmenistan</option>
        <option value="TC">Turks and Caicos Islands</option>
        <option value="TV">Tuvalu</option>
        <option value="UG">Uganda</option>
        <option value="UA">Ukraine</option>
        <option value="AE">United Arab Emirates</option>
        <option value="GB">United Kingdom</option>
        <option value="US">United States</option>
        <option value="UM">United States Minor Outlying Islands</option>
        <option value="UY">Uruguay</option>
        <option value="UZ">Uzbekistan</option>
        <option value="VU">Vanuatu</option>
        <option value="VE">Venezuela</option>
        <option value="VN">Viet Nam</option>
        <option value="VG">Virgin Islands, British</option>
        <option value="VI">Virgin Islands, U.s.</option>
        <option value="WF">Wallis and Futuna</option>
        <option value="EH">Western Sahara</option>
        <option value="YE">Yemen</option>
        <option value="ZM">Zambia</option>
        <option value="ZW">Zimbabwe</option>
      </select>
    </div>`,
});


Vvveb.Components.extend("_base", "_base", {
  properties: [
    {
      name: t("Font family"),
      key: "font-family",
      htmlAttr: "style",
      sort: base_sort++,
      col: 6,
      inline: true,
      inputtype: SelectInput,
      data: {
        options: [
          {
            value: "",
            text: t("extended"),
          },
          {
            value: "Ggoogle ",
            text: t("google"),
          },
        ],
      },
    },
  ],
});
