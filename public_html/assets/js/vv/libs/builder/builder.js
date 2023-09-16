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

// Simple JavaScript Templating
// John Resig - https://johnresig.com/ - MIT Licensed
(function () {
  var cache = {};
  var startTag = "{%";
  var endTag = "%}";
  var re1 = new RegExp(`((^|${endTag})[^\t]*)'`, "g");
  var re2 = new RegExp(`\t=(.*?)${endTag}`, "g");

  this.tmpl = function tmpl(str, data) {
    // Figure out if we're getting a template, or if we need to
    // load the template - and be sure to cache the result.
    var fn = /^[-a-zA-Z0-9]+$/.test(str)
      ? (cache[str] =
          cache[str] || tmpl(document.getElementById(str).innerHTML))
      : // Generate a reusable function that will serve as a template
        // generator (and which will be cached).
        new Function(
          "obj",
          "var p=[],print=function(){p.push.apply(p,arguments);};" +
            // Introduce the data as local variables using with(){}
            "with(obj){p.push('" +
            // Convert the template into pure JavaScript
            str
              .replace(/[\r\t\n]/g, " ")
              .split(startTag)
              .join("\t")
              .replace(re1, "$1\r")
              .replace(re2, "',$1,'")
              .split("\t")
              .join("');")
              .split(endTag)
              .join("p.push('")
              .split("\r")
              .join("\\'") +
            "');}return p.join('');"
        );
    // Provide some basic currying to the user
    return data ? fn(data) : fn;
  };
})();

var delay = (function () {
  var timer = 0;
  return function (callback, ms) {
    clearTimeout(timer);
    timer = setTimeout(callback, ms);
  };
})();

function isElement(obj) {
  return (
    typeof obj === "object" &&
    obj.nodeType === 1 &&
    typeof obj.style === "object" &&
    typeof obj.ownerDocument === "object" /* && obj.tagName != "BODY"*/
  );
}

if (Vvveb === undefined) var Vvveb = {};

Vvveb.defaultComponent = "_base";
Vvveb.preservePropertySections = true;
//icon = use component icon when dragging | html = use component html to create draggable element
Vvveb.dragIcon = "icon";
//if empty the html of the component is used to view dropping in real time but for large elements it can jump around for this you can set a html placeholder with this option
Vvveb.dragHtml =
  '<div style="background:limegreen;;width:100%;height:3px;border:1px solid limegreen;box-shadow:0px 0px 2px 1px rgba(0,0,0,0.14);"></div>';

Vvveb.baseUrl = document.currentScript
  ? document.currentScript.src.replace(/[^\/]*?\.js$/, "")
  : "";
Vvveb.imgBaseUrl = Vvveb.baseUrl;

Vvveb.ComponentsGroup = {};
Vvveb.SectionsGroup = {};
Vvveb.BlocksGroup = {};

Vvveb.Components = {
  _components: {},

  _nodesLookup: {},

  _attributesLookup: {},

  _classesLookup: {},

  _classesRegexLookup: {},

  componentPropertiesElement: "#right-panel .component-properties",

  componentPropertiesDefaultSection: "content",

  get: function (type) {
    return this._components[type];
  },

  updateProperty: function (type, key, value) {
    let properties = this._components[type]["properties"];
    for (property in properties) {
      if (key == properties[property]["key"]) {
        return (this._components[type]["properties"][property] = Object.assign(
          properties[property],
          value
        ));
      }
    }
  },

  getProperty: function (type, key) {
    let properties = this._components[type]["properties"];
    for (property in properties) {
      if (key == properties[property]["key"]) {
        return properties[property];
      }
    }
  },

  add: function (type, data) {
    data.type = type;

    this._components[type] = data;

    if (data.nodes) {
      for (var i in data.nodes) {
        this._nodesLookup[data.nodes[i]] = data;
      }
    }

    if (data.attributes) {
      if (data.attributes.constructor === Array) {
        for (var i in data.attributes) {
          this._attributesLookup[data.attributes[i]] = data;
        }
      } else {
        for (var i in data.attributes) {
          if (typeof this._attributesLookup[i] === "undefined") {
            this._attributesLookup[i] = {};
          }

          if (
            typeof this._attributesLookup[i][data.attributes[i]] === "undefined"
          ) {
            this._attributesLookup[i][data.attributes[i]] = {};
          }

          this._attributesLookup[i][data.attributes[i]] = data;
        }
      }
    }

    if (data.classes) {
      for (var i in data.classes) {
        this._classesLookup[data.classes[i]] = data;
      }
    }

    if (data.classesRegex) {
      for (var i in data.classesRegex) {
        this._classesRegexLookup[data.classesRegex[i]] = data;
      }
    }
  },

  extend: function (inheritType, type, data) {
    var newData = data;

    if ((inheritData = this._components[inheritType])) {
      newData = $.extend(true, {}, inheritData, data);
      newData.properties = $.merge(
        $.merge([], inheritData.properties ? inheritData.properties : []),
        data.properties ? data.properties : []
      );
    }

    //sort by order
    newData.properties.sort(function (a, b) {
      if (typeof a.sort === "undefined") a.sort = 0;
      if (typeof b.sort === "undefined") b.sort = 0;

      if (a.sort < b.sort) return -1;
      if (a.sort > b.sort) return 1;
      return 0;
    });
    /*		 
		  var output = array.reduce(function(o, cur) {
  
			// Get the index of the key-value pair.
			var occurs = o.reduce(function(n, item, i) {
			  return (item.key === cur.key) ? i : n;
			}, -1);
  
			// If the name is found,
			if (occurs >= 0) {
  
			  // append the current value to its list of values.
			  o[occurs].value = o[occurs].value.concat(cur.value);
  
			// Otherwise,
			} else {
  
			  // add the current item to o (but make sure the value is an array).
			  var obj = {name: cur.key, value: [cur.value]};
			  o = o.concat([obj]);
			}
  
			return o;
		  }, newData.properties);		 
  */

    this.add(type, newData);
  },

  matchNode: function (node) {
    var component = {};

    if (!node || !node.tagName) return false;

    if (node.attributes && node.attributes.length) {
      //search for attributes
      for (var i in node.attributes) {
        if (node.attributes[i]) {
          attr = node.attributes[i].name;
          value = node.attributes[i].value;

          if (attr in this._attributesLookup) {
            component = this._attributesLookup[attr];

            //currently we check that is not a component by looking at name attribute
            //if we have a collection of objects it means that attribute value must be checked
            if (typeof component["name"] === "undefined") {
              if (value in component) {
                return component[value];
              }
            } else return component;
          }
        }
      }

      for (var i in node.attributes) {
        attr = node.attributes[i].name;
        value = node.attributes[i].value;

        //check for node classes
        if (attr == "class") {
          classes = value.split(" ");

          for (j in classes) {
            if (classes[j] in this._classesLookup)
              return this._classesLookup[classes[j]];
          }

          for (regex in this._classesRegexLookup) {
            regexObj = new RegExp(regex);
            if (regexObj.exec(value)) {
              return this._classesRegexLookup[regex];
            }
          }
        }
      }
    }

    tagName = node.tagName.toLowerCase();
    if (tagName in this._nodesLookup) return this._nodesLookup[tagName];

    return false;
    //return false;
  },

  render: function (type) {
    var component = this._components[type];

    var componentsPanel = $(this.componentPropertiesElement);
    var defaultSection = this.componentPropertiesDefaultSection;
    var componentsPanelSections = {};

    $(this.componentPropertiesElement + " .tab-pane").each(function () {
      var sectionName = this.dataset.section;
      componentsPanelSections[sectionName] = $(this);
      $('.section[data-section!="default"]', this).remove();
      $('label[for!="header_default"]', this).remove();
      $('input[id!="header_default"]', this).remove();
    });

    var section = componentsPanelSections[defaultSection].find(
      '.section[data-section="default"]'
    );

    if (!(Vvveb.preservePropertySections && section.length)) {
      componentsPanelSections[defaultSection].html("").append(
        tmpl("vvveb-input-sectioninput", {
          key: "default",
          header: component.name,
        })
      );
      section = componentsPanelSections[defaultSection].find(".section");
    }

    componentsPanelSections[defaultSection]
      .find('[data-header="default"] span')
      .html(component.name);
    section.html("");

    if (component.beforeInit)
      component.beforeInit(Vvveb.Builder.selectedEl.get(0));

    var element;

    var fn = function (component, property) {
      return property.input.on(
        "propertyChange",
        function (event, value, input) {
          var element = Vvveb.Builder.selectedEl;

          if (property.child) element = element.find(property.child);
          if (property.parent) element = element.parent(property.parent);

          if (property.onChange) {
            element = property.onChange(element, value, input, component);
          } /* else */
          if (property.htmlAttr) {
            oldValue = element.attr(property.htmlAttr);

            if (property.htmlAttr == "class" && property.validValues) {
              element.removeClass(property.validValues.join(" "));
              element = element.addClass(value);
            } else if (property.htmlAttr == "style") {
              oldStyle = $("#vvvebjs-styles", window.FrameDocument).html();
              element = Vvveb.StyleManager.setStyle(
                element,
                property.key,
                value
              );
            } else if (property.htmlAttr == "innerHTML") {
              element = Vvveb.ContentManager.setHtml(element, value);
            } else {
              //if value is empty then remove attribute useful for attributes without values like disabled
              if (value) {
                element = element.attr(property.htmlAttr, value);
              } else {
                element = element.removeAttr(property.htmlAttr);
              }
            }

            if (property.htmlAttr == "style") {
              mutation = {
                type: "style",
                target: element.get(0),
                attributeName: property.htmlAttr,
                oldValue: oldStyle,
                newValue: $("#vvvebjs-styles", window.FrameDocument).html(),
              };

              Vvveb.Undo.addMutation(mutation);
            } else {
              Vvveb.Undo.addMutation({
                type: "attributes",
                target: element.get(0),
                attributeName: property.htmlAttr,
                oldValue: oldValue,
                newValue: element.attr(property.htmlAttr),
              });
            }
          }

          if (component.onChange) {
            element = component.onChange(element, property, value, input);
          }

          if (!property.child || !property.parent)
            Vvveb.Builder.selectNode(element);

          return element;
        }
      );
    };

    var nodeElement = Vvveb.Builder.selectedEl;

    for (var i in component.properties) {
      var property = component.properties[i];
      var element = nodeElement;

      if (property.beforeInit) property.beforeInit(element.get(0));

      if (property.child) element = element.find(property.child);

      if (property.data) {
        property.data["key"] = property.key;
      } else {
        property.data = { key: property.key };
      }

      if (typeof property.group === "undefined") property.group = null;

      property.input = property.inputtype.init(property.data);

      if (property.init) {
        property.inputtype.setValue(property.init(element.get(0)));
      } else if (property.htmlAttr) {
        if (property.htmlAttr == "style") {
          //value = element.css(property.key);//jquery css returns computed style
          var value = Vvveb.StyleManager.getStyle(element, property.key); //getStyle returns declared style
        } else if (property.htmlAttr == "innerHTML") {
          var value = Vvveb.ContentManager.getHtml(element);
        } else {
          var value = element.attr(property.htmlAttr);
        }

        //if attribute is class check if one of valid values is included as class to set the select
        if (value && property.htmlAttr == "class" && property.validValues) {
          value = value.split(" ").filter(function (el) {
            return property.validValues.indexOf(el) != -1;
          });
        }

        if (!value && property.defaultValue) {
          value = property.defaultValue;
        }
        property.inputtype.setValue(value);
      }

      fn(component, property);

      var propertySection = defaultSection;
      if (property.section) {
        propertySection = property.section;
      }

      if (property.inputtype == SectionInput) {
        section = componentsPanelSections[propertySection].find(
          '.section[data-section="' + property.key + '"]'
        );

        if (Vvveb.preservePropertySections && section.length) {
          section.html("");
        } else {
          componentsPanelSections[propertySection].append(property.input);
          section = componentsPanelSections[propertySection].find(
            '.section[data-section="' + property.key + '"]'
          );
        }
      } else {
        var row = $(tmpl("vvveb-property", property));
        row.find(".input").append(property.input);
        section.append(row);
      }

      if (property.inputtype.afterInit) {
        property.inputtype.afterInit(property.input);
      }

      if (property.afterInit) {
        property.afterInit(element.get(0), property.input);
      }
    }

    if (component.init) component.init(Vvveb.Builder.selectedEl.get(0));
  },
};

Vvveb.Blocks = {
  _blocks: {},

  get: function (type) {
    return this._blocks[type];
  },

  add: function (type, data) {
    data.type = type;
    this._blocks[type] = data;
  },
};

Vvveb.Sections = {
  _sections: {},

  get: function (type) {
    return this._sections[type];
  },

  add: function (type, data) {
    data.type = type;
    this._sections[type] = data;
  },
};

Vvveb.WysiwygEditor = {
  isActive: false,
  oldValue: "",
  doc: false,

  editorSetStyle: function (tag, style = {}, toggle = false) {
    let iframeWindow = Vvveb.Builder.iframe.contentWindow;
    let selection = iframeWindow.getSelection();
    let element = this.element;
    let range;

    if (!tag) {
      tag = "span";
    }

    if (selection.rangeCount > 0) {
      //check if the whole text is inside an existing node to use the node directly
      if (
        (selection.baseNode &&
          selection.baseNode.nextSibling == null &&
          selection.baseNode.previousSibling == null &&
          selection.anchorOffset == 0 &&
          selection.focusOffset == selection.baseNode.length) ||
        selection.anchorOffset == selection.focusOffset
      ) {
        element = selection.baseNode.parentNode;
      } else {
        element = document.createElement(tag);
        range = selection.getRangeAt(0);
        range.surroundContents(element);
        range.selectNodeContents(element.childNodes[0], 0);
      }
    }

    if (element && style) {
      for (name in style) {
        if (!style[name] || (toggle && element.style.getPropertyValue(name))) {
            element.style.removeProperty(name);
        } else {
          if( name == "font-family" )
          {
            setAllFontFamily( $(element), style[name] );
          }
          else{
            element.style.setProperty(name, style[name]);
          }
        }
      }
    }

    //if edited text is an empty span remove the span
    if (
      element.tagName == "SPAN" &&
      element.style.length == 0 &&
      element.attributes.length <= 1
    ) {
      let textNode = iframeWindow.document.createTextNode(element.innerText);
      element.replaceWith(textNode);
      element = textNode;

      range = iframeWindow.document.createRange();
      range.selectNodeContents(element);
      selection.removeAllRanges();
      selection.addRange(range);
    }

    return element;
  },

  init: function (doc) {
    this.doc = doc;
    let self = this;

    $("#bold-btn").on("click", function (e) {
      //doc.execCommand('bold',false,null);
      //self.editorSetStyle("b", {"font-weight" : "bold"}, true);
      self.editorSetStyle(false, { "font-weight": "bold" }, true);
      e.preventDefault();
      return false;
    });

    $("#italic-btn").on("click", function (e) {
      //doc.execCommand('italic',false,null);
      //self.editorSetStyle("i", {"font-style" : "italic"}, true);
      self.editorSetStyle(false, { "font-style": "italic" }, true);
      e.preventDefault();
      return false;
    });

    $("#underline-btn").on("click", function (e) {
      //doc.execCommand('underline',false,null);
      //self.editorSetStyle("u", {"text-decoration" : "underline"}, true);
      self.editorSetStyle(false, { "text-decoration": "underline" }, true);
      e.preventDefault();
      return false;
    });

    $("#strike-btn").on("click", function (e) {
      //doc.execCommand('strikeThrough',false,null);
      //self.editorSetStyle("strike",  {"text-decoration" : "line-through"}, true);
      self.editorSetStyle(false, { "text-decoration": "line-through" }, true);
      e.preventDefault();
      return false;
    });

    $("#link-btn").on("click", function (e) {
      //doc.execCommand('createLink',false,"#");
      self.editorSetStyle("a");
      e.preventDefault();
      return false;
    });

    $("#fore-color").on("change", function (e) {
      //doc.execCommand('foreColor',false,this.value);
      self.editorSetStyle(false, { color: this.value });
      e.preventDefault();
      return false;
    });

    $("#back-color").on("change", function (e) {
      //doc.execCommand('hiliteColor',false,this.value);
      self.editorSetStyle(false, { "background-color": this.value });
      e.preventDefault();
      return false;
    });

    $("#font-size").on("change", function (e) {
      //doc.execCommand('fontSize',false,this.value);
      self.editorSetStyle(false, { "font-size": this.value });
      e.preventDefault();
      return false;
    });

    let sizes = "<option value=''>Default</option>";
    for (i = 1; i <= 128; i++) {
      sizes += "<option value='" + i + "px'>" + i + "</option>";
    }
    $("#font-size").html(sizes);

    $("#font-family").on("change", function (e) {
      let option = this.options[this.selectedIndex];
      let element = self.editorSetStyle(false, { "font-family": this.value });
      Vvveb.FontsManager.addFont(option.dataset.provider, this.value, element);
      //doc.execCommand('fontName',false,this.value);
      e.preventDefault();
      return false;
    });

    $("#justify-btn a").on("click", function (e) {
      //var command = "justify" + this.dataset.value;
      //doc.execCommand(command,false,"#");
      self.editorSetStyle(false, { "text-align": this.dataset.value });
      e.preventDefault();
      return false;
    });
  },

  undo: function (element) {
    this.doc.execCommand("undo", false, null);
  },

  redo: function (element) {
    this.doc.execCommand("redo", false, null);
  },

  edit: function (element,self) {
    var offset = element.offset();
  
    element.attr({ contenteditable: true, spellcheckker: false });

    // console.log();
    if (  self.frameDoc.scrollTop() <= 16 ) {
      $("#wysiwyg-editor").css({"display":"flex","flex-direction":"row","width":"550px"});
    } else {
      $("#wysiwyg-editor").css({"display":"flex","flex-direction":"column","width":"auto"});
    }

    

    this.element = element;
    this.isActive = true;
    this.oldValue = element.html();

    $("#font-familly").val(getComputedStyle(element[0])["font-family"]);

    if ( self.frameDoc.width() - ( offset.left - self.frameDoc.scrollLeft() - self.selectPadding ) <= 320 ) {
      $("#wysiwyg-editor").css({ left: -312 });
    } else {
      $("#wysiwyg-editor").css({ left: -1 });
    }
    
  },

  destroy: function (element) {
    element.removeAttr("contenteditable spellcheckker");
    $("#wysiwyg-editor").hide();
    this.isActive = false;

    node = this.element.get(0);
    Vvveb.Undo.addMutation({
      type: "characterData",
      target: node,
      oldValue: this.oldValue,
      newValue: node.innerHTML,
    });
  },
};

Vvveb.Builder = {
  component: {},
  dragMoveMutation: false,
  isPreview: false,
  runJsOnSetHtml: false,
  designerMode: false,
  highlightEnabled: false,
  selectPadding: 0,
  leftPanelWidth: 275,
  ignoreClasses: ["clearfix"],

  init: function (url, callback) {
    var self = this;

    self.loadControlGroups();
    self.loadBlockGroups();
    self.loadSectionGroups();

    self.selectedEl = null;
    self.highlightEl = null;
    self.initCallback = callback;

    self.documentFrame = $("#iframe-wrapper > iframe");
    self.canvas = $("#canvas");

    self._loadIframe(url);

    self._initDragdrop();

    self._initBox();

    self.dragElement = null;

    self.highlightEnabled = true;

    self.leftPanelWidth = $("#left-panel").width();

    self.adjustListsHeight();
  },

  /* controls */
  loadControlGroups: function () {
    var componentsList = $(".components-list");
    componentsList.empty();
    var item = {},
      component = {};
    var count = 0;

    componentsList.each(function () {
      var list = $(this);
      var type = this.dataset.type;
      count++;

      for (group in Vvveb.ComponentsGroup) {
        list.append(
          `<li class="header" data-section="${group}"  data-search="">
					  <label class="header" for="${type}_comphead_${group}${count}">
						  ${group}<div class="header-arrow"></div>
					  </label>
					  <input class="header_check" type="checkbox" checked="true" id="${type}_comphead_${group}${count}">
					  <ol></ol>
				  </li>`
        );

        //list.append('<li class="header clearfix" data-section="' + group + '"  data-search=""><label class="header" for="' + type + '_comphead_' + group + count + '">' + group + '  <div class="header-arrow"></div>\
        //				   </label><input class="header_check" type="checkbox" checked="true" id="' + type + '_comphead_' + group + count + '">  <ol></ol></li>');

        var componentsSubList = list.find(
          'li[data-section="' + group + '"]  ol'
        );

        components = Vvveb.ComponentsGroup[group];

        for (i in components) {
          componentType = components[i];
          component = Vvveb.Components.get(componentType);

          if (component) {
            item =
              $(`<li data-section="${group}" data-drag-type="component" data-type="${componentType}" data-search="${component.name.toLowerCase()}">
							  <a href="#">${component.name}</a>
						  </li>`);
            //item = $('<li data-section="' + group + '" data-drag-type=component data-type="' + componentType + '" data-search="' + component.name.toLowerCase() + '"><a href="#">' + component.name + "</a></li>");

            if (component.image) {
              item.css({
                backgroundImage:
                  "url(" + Vvveb.imgBaseUrl + component.image + ")",
                backgroundRepeat: "no-repeat",
              });
            }

            componentsSubList.append(item);
          }
        }
      }
    });
  },

  loadSectionGroups: function () {
    var sectionsList = $(".sections-list");
    sectionsList.empty();
    var item = {};

    sectionsList.each(function () {
      var list = $(this);
      var type = this.dataset.type;

      for (group in Vvveb.SectionsGroup) {
        list.append(
          `<li class="header" data-section="${group}"  data-search="">
					  <label class="header" for="${type}_sectionhead_${group}">
						  ${group}<div class="header-arrow"></div>
					  </label>
					  <input class="header_check" type="checkbox" checked="true" id="${type}_sectionhead_${group}">
					  <ol></ol>
				  </li>`
        );

        var sectionsSubList = list.find('li[data-section="' + group + '"]  ol');
        sections = Vvveb.SectionsGroup[group];

        for (i in sections) {
          sectionType = sections[i];
          section = Vvveb.Sections.get(sectionType);

          if (section) {
            item =
              $(`<li data-section="${group}" data-type="${sectionType}" data-search="${section.name.toLowerCase()}">
									  <a class="name" href="#">${section.name}</a>
									  <a class="add-section-btn" href="" title="Add section"><i class="la la-plus"></i></a>
									  <img class="preview" src="" loading="lazy">
								  </li>`);

            if (section.image) {
              var image =
                (section.image.indexOf("/") == -1 ? Vvveb.imgBaseUrl : "") +
                section.image;

              item
                .css({
                  //backgroundImage: "url(" + image + ")",
                  backgroundRepeat: "no-repeat",
                })
                .find("img")
                .attr("src", image);
            }

            sectionsSubList.append(item);
          }
        }
      }
    });
  },

  loadBlockGroups: function () {
    var blocksList = $(".blocks-list");
    blocksList.empty();
    var item = {};

    blocksList.each(function () {
      var list = $(this);
      var type = this.dataset.type;

      for (group in Vvveb.BlocksGroup) {
        list.append(
          `<li class="header" data-section="${group}"  data-search="">
					  <label class="header" for="${type}_blockhead_${group}">
						  ${group}<div class="header-arrow"></div>
					  </label>
					  <input class="header_check" type="checkbox" checked="true" id="${type}_blockhead_${group}">
					  <ol></ol>
				  </li>`
        );

        var blocksSubList = list.find('li[data-section="' + group + '"]  ol');
        blocks = Vvveb.BlocksGroup[group];

        for (i in blocks) {
          blockType = blocks[i];
          block = Vvveb.Blocks.get(blockType);

          if (block) {
            item =
              $(`<li data-section="${group}" data-drag-type="block" data-type="${blockType}" data-search="${block.name.toLowerCase()}">
									  <a class="name" href="#">${block.name}</a>
									  <img class="preview" src="" loading="lazy">
								  </li>`);

            if (block.image) {
              var image =
                (block.image.indexOf("/") == -1 ? Vvveb.imgBaseUrl : "") +
                block.image;

              item
                .css({
                  backgroundImage: "url(" + image + ")",
                  backgroundRepeat: "no-repeat",
                })
                .find("img")
                .attr("src", image);
            }

            blocksSubList.append(item);
          }
        }
      }
    });
  },

  adjustListsHeight: function () {
    let lists = $(".drag-elements-sidepane > div:not(.block-preview)");
    let properties = $(".component-properties >.tab-content");
    let wHeight = $(window).height();
    function adjust(elements) {
      let maxOffset = 0;
      elements.each(function (i, e) {
        maxOffset = Math.max(maxOffset, e.getBoundingClientRect()["top"]);
      });

      elements.each(function (i, e) {
        if( maxOffset==0 )
        {
          maxOffset=130;
        }
        e.style.height = wHeight - maxOffset + "px";
      });
    }

    adjust(lists);
    adjust(properties);
  },

  loadUrl: function (url, callback) {
    var self = this;
    $("#select-box").hide();

    self.initCallback = callback;
    if (Vvveb.Builder.iframe.src != url) Vvveb.Builder.iframe.src = url;
  },

  /* iframe */
  _loadIframe: function (url) {
  
    var self = this;
    self.iframe = this.documentFrame.get(0);
    self.iframe.src = url;

    return this.documentFrame.on("load", function () {
     
      window.FrameWindow = self.iframe.contentWindow;
      window.FrameDocument = self.iframe.contentWindow.document;
      let doc = self.iframe.contentWindow.document;
      //replace variable 
      try {
        // console.log();
        let body_style = doc.querySelector("#ssf__custom_body_bimg");
        if(body_style != null)
        {
          let cf_body_style = body_style.innerHTML;
          let lsf= cf_body_style.replace(/({|})/gi,'');
          let cssbody =  lsf.replace('body ','');
          console.log(cssbody);
          let bodyinner = doc.getElementsByTagName("body")[0];
          bodyinner.setAttribute("style",cssbody);
        }
        
      } catch (error) {
        console.log(error);
      }
        // 
     
      //replace variable
      var addSectionBox = $("#add-section-box");
      var highlightBox = $("#highlight-box").hide();

      $(window.FrameWindow).on("beforeunload", function (event) {
        
        if (Vvveb.Undo.undoIndex >= 0) {
          var dialogText = "You have unsaved changes";
          event.returnValue = dialogText;
          return dialogText;
        }
      });

      $(window.FrameWindow).on("unload", function (event) {
        $(".loading-message").addClass("active");
       
      });

      $(window.FrameWindow).on("scroll resize", function (event) {
        if (self.selectedEl) {
          var offset = self.selectedEl.offset();
          $("#select-box").css({
            top: offset.top - self.frameDoc.scrollTop() - self.selectPadding,
            left: offset.left - self.frameDoc.scrollLeft() - self.selectPadding,
          });
        }

        if (self.highlightEl) {
          var offset = self.highlightEl.offset();

          highlightBox.css({
            top: offset.top - self.frameDoc.scrollTop() - self.selectPadding,
            left: offset.left - self.frameDoc.scrollLeft() - self.selectPadding,
          });

          //addSectionBox.hide();
        }

        self.adjustListsHeight();
      });

      Vvveb.WysiwygEditor.init(window.FrameDocument);
      Vvveb.StyleManager.init(window.FrameDocument);
      if (self.initCallback) self.initCallback();

      return self._frameLoaded();
    });
  },

  _frameLoaded: function () {
    var self = Vvveb.Builder;

    self.frameDoc = $(window.FrameDocument);
    self.frameHtml = $(window.FrameDocument).find("html");
    self.frameBody = $(window.FrameDocument).find("body");
    self.frameHead = $(window.FrameDocument).find("head");

    //insert editor helpers like non editable areas
    self.frameHead.append(
      '<link data-vvveb-helpers href="' +
        Vvveb.baseUrl +
        '../../css/vvvebjs-editor-helpers.css" rel="stylesheet">'
    );

    self._initHighlight();

    $(window).triggerHandler("vvveb.iframe.loaded", self.frameDoc);
    $(".loading-message").removeClass("active");
  },

  _getElementType: function (el) {
    //search for component attribute
    var componentName = "";
    var componentAttribute = "";

    if (el.attributes) {
      for (var j = 0; j < el.attributes.length; j++) {
        var nodeName = el.attributes[j].nodeName;

        if (nodeName.indexOf("data-component") > -1) {
          componentName = nodeName.replace("data-component-", "");
        }

        if (nodeName.indexOf("data-v-") > -1) {
          componentAttribute =
            (componentAttribute ? componentAttribute + " - " : "") +
            nodeName.replace("data-v-", "") +
            " ";
        }
      }
    }
    if (componentName != "") return componentName;

    return (
      el.tagName +
      (componentName ? " - " + componentName : "") +
      (componentAttribute ? " - " + componentAttribute : "")
    );
  },

  loadNodeComponent: function (node) {
    data = Vvveb.Components.matchNode(node);
    var component;

    if (data) component = data.type;
    else component = Vvveb.defaultComponent;

    Vvveb.component = Vvveb.Components.get(component);
    Vvveb.Components.render(component);
  },

  moveNodeUp: function (node) {
    if (!node) {
      node = Vvveb.Builder.selectedEl.get(0);
    }

    oldParent = node.parentNode;
    oldNextSibling = node.nextSibling;

    next = $(node).prev();

    if (next.length > 0) {
      next.before(node);
    } else {
      $(node).parent().before(node);
    }

    newParent = node.parentNode;
    newNextSibling = node.nextSibling;

    Vvveb.Undo.addMutation({
      type: "move",
      target: node,
      oldParent: oldParent,
      newParent: newParent,
      oldNextSibling: oldNextSibling,
      newNextSibling: newNextSibling,
    });
  },

  moveNodeDown: function (node) {
    if (!node) {
      node = Vvveb.Builder.selectedEl.get(0);
    }

    oldParent = node.parentNode;
    oldNextSibling = node.nextSibling;

    next = $(node).next();

    if (next.length > 0) {
      next.after(node);
    } else {
      $(node).parent().after(node);
    }

    newParent = node.parentNode;
    newNextSibling = node.nextSibling;

    Vvveb.Undo.addMutation({
      type: "move",
      target: node,
      oldParent: oldParent,
      newParent: newParent,
      oldNextSibling: oldNextSibling,
      newNextSibling: newNextSibling,
    });
  },

  cloneNode: function (node) {
    if (!node) {
      node = Vvveb.Builder.selectedEl;
    }

    clone = node.clone();

    node.after(clone);

    node = clone.click();

    element = clone.get(0);

    Vvveb.Undo.addMutation({
      type: "childList",
      target: node.parentNode,
      addedNodes: [element],
      nextSibling: node.nextSibling,
    });
  },

  selectNode: function (node) {
    var self = this;

    if (!node) {
      $("#select-box").hide();
      return;
    }

    if (self.texteditEl && self.selectedEl.get(0) != node) {
      Vvveb.WysiwygEditor.destroy(self.texteditEl);
      self.selectPadding = 0;
      $("#select-box").removeClass("text-edit").find("#select-actions").show();
      self.texteditEl = null;
    }

    var target = $(node);

    if (target) {
      self.selectedEl = target;

      try {
        var offset = target.offset();
        $("#select-box").css({
          top: offset.top - self.frameDoc.scrollTop() - self.selectPadding,
          left: offset.left - self.frameDoc.scrollLeft() - self.selectPadding,
          width: target.outerWidth() + self.selectPadding * 2,
          height: target.outerHeight() + self.selectPadding * 2,
          display: "block",
        });
        if (
          offset.left - self.frameDoc.scrollLeft() - self.selectPadding <=
            125 &&
          target.width() <= 125
        ) {
          $("#select-actions").css({ right: -153 });
        } else {
          $("#select-actions").css({ right: -1 });
        }
        Vvveb.Breadcrumb.loadBreadcrumb(target.get(0));
      } catch (err) {
        console.log(err);
        return false;
      }
    }

    $("#highlight-name").html(this._getElementType(node));
  },

  /* iframe highlight */
  _initHighlight: function () {
    var self = Vvveb.Builder;

    self.frameBody.on("mousemove dragover touchmove", function (event) {
      if (
        self.highlightEnabled == true &&
        event.target &&
        isElement(event.target) &&
        event.originalEvent
      ) {
        self.highlightEl = target = $(event.target);
        var offset = target.offset();
        var height = target.outerHeight();
        var halfHeight = Math.max(height / 2, 50);
        var width = target.outerWidth();
        var halfWidth = Math.max(width / 2, 50);
        var prepend = true;

        var x = event.originalEvent.x;
        var y = event.originalEvent.y;

        if (self.isResize) {
          if (!self.initialPosition) {
            self.initialPosition = { x, y };
          }

          var deltaX = x - self.initialPosition.x;
          var deltaY = y - self.initialPosition.y;

          offset = self.selectedEl.offset();

          width = self.initialSize.width;
          height = self.initialSize.height;

          switch (self.resizeHandler) {
            // top
            case "top-left":
              height -= deltaY;
              width -= deltaX;
              break;

            case "top-center":
              height -= deltaY;
              break;

            case "top-right":
              height -= deltaY;
              width += deltaX;
              break;

            // center
            case "center-left":
              width -= deltaX;
              break;

            case "center-right":
              width += deltaX;
              break;

            // bottom
            case "bottom-left":
              width -= deltaX;
              height += deltaY;
              break;

            case "bottom-center":
              height += deltaY;
              break;

            case "bottom-right":
              width += deltaX;
              height += deltaY;
              break;
          }

          if (self.resizeMode == "css") {
            self.selectedEl.css({ width, height });
          } else {
            self.selectedEl.attr({ width, height });
          }
          $("#select-box").css({
            top: offset.top - self.frameDoc.scrollTop(),
            left: offset.left - self.frameDoc.scrollLeft(),
            width: width,
            height: self.selectedEl.outerHeight(),
          });
        } else if (self.isDragging) {
          var parent = self.highlightEl;

          try {
            if (offset.top < y - halfHeight || offset.left < x - halfWidth) {
              self.dragElement.appendTo(parent);
              prepend = true;
            } else {
              prepend = false;
              self.dragElement.prependTo(parent);
            }

            if (self.designerMode) {
              var parentOffset = self.dragElement.offsetParent().offset();

              self.dragElement.css({
                position: "absolute",
                left: x - (parentOffset.left - self.frameDoc.scrollLeft()),
                top: y - (parentOffset.top - self.frameDoc.scrollTop()),
              });
            }

            /*
							  $("#drop-highlight-box").css(
								  {"top": offset.top - self.frameDoc.scrollTop() , 
								   "left": offset.left - self.frameDoc.scrollLeft() , 
								   "width" : parentWidth, 
								   "height": "5px",
									"display" :"block",
								   });
							  */
          } catch (err) {
            console.log(err);
            return false;
          }

          if (!self.designerMode && self.iconDrag) {
            self.iconDrag.css({
              left: x + self.leftPanelWidth + 10,
              top: y + 60,
            });
          }
        } // else //uncomment else to disable parent highlighting when dragging
        {
          $("#highlight-box").css({
            top: offset.top - self.frameDoc.scrollTop(),
            left: offset.left - self.frameDoc.scrollLeft(),
            width: width,
            height: height,
            display: event.target.hasAttribute("contenteditable")
              ? "none"
              : "block",
            border: self.isDragging ? "1px dashed #0d6efd" : "", //when dragging highlight parent with green
          });
          $("#section-actions").removeClass("outside");

          $("#highlight-name").html(self._getElementType(event.target));
        }
      }
    });

    self.frameHtml.on("mouseup dragend touchend", function (event) {
      self.isResize = false;
      $("#section-actions, #highlight-name, #select-box").show();

      if (self.isDragging) {
        self.isDragging = false;
        Vvveb.Builder.highlightEnabled = true;
        if (self.iconDrag) self.iconDrag.remove();
        $("#component-clone").remove();

        if (self.dragMoveMutation === false) {
          if (self.component.dragHtml || Vvveb.dragHtml) {
            //if dragHtml is set for dragging then set real component html
            newElement = $(self.component.html);
            self.dragElement.replaceWith(newElement);
            self.dragElement = newElement;
          }

          if (self.component.afterDrop)
            self.dragElement = self.component.afterDrop(self.dragElement);
        }

        self.dragElement.css("border", "");

        node = self.dragElement.get(0);
        self.selectNode(node);
        self.loadNodeComponent(node);

        if (self.dragMoveMutation === false) {
          Vvveb.Undo.addMutation({
            type: "childList",
            target: node.parentNode,
            addedNodes: [node],
            nextSibling: node.nextSibling,
          });
        } else {
          self.dragMoveMutation.newParent = node.parentNode;
          self.dragMoveMutation.newNextSibling = node.nextSibling;

          Vvveb.Undo.addMutation(self.dragMoveMutation);
          self.dragMoveMutation = false;
        }
      }
    });

    self.frameHtml.on("dblclick", function (event) {
      if (Vvveb.Builder.isPreview == false) {
        self.selectPadding = 10;
        self.texteditEl = target = $(event.target);

        Vvveb.WysiwygEditor.edit(self.texteditEl,self);

        _updateSelectBox = function (event) {
          if (!self.texteditEl) return;
          var offset = self.selectedEl.offset();

          $("#select-box").css({
            top: offset.top - self.frameDoc.scrollTop() - self.selectPadding,
            left: offset.left - self.frameDoc.scrollLeft() - self.selectPadding,
            width: self.texteditEl.outerWidth() + self.selectPadding * 2,
            height: self.texteditEl.outerHeight() + self.selectPadding * 2,
          });
        };

        //update select box when the text size is changed
        self.texteditEl.on("blur keyup paste input", _updateSelectBox);
        _updateSelectBox();

        $("#select-box").addClass("text-edit").find("#select-actions").hide();
        $("#highlight-box").hide();
      }
    });

    self.frameHtml.on("click", function (event) {
      if (Vvveb.Builder.isPreview == false) {
        if (event.target) {
          //if component properties is loaded in left panel tab instead of right panel show tab
          if ($(".component-properties-tab").is(":visible"))
            //if properites tab is enabled/visible
            $(".component-properties-tab a").show().tab("show");

          self.selectNode(event.target);
          self.loadNodeComponent(event.target);

          if (Vvveb.component.resizable) {
            $("#select-box").addClass("resizable");
            self.resizeMode = Vvveb.component.resizeMode;
          } else {
            $("#select-box").removeClass("resizable");
          }
        }
        $("#add-section-box").hide();
        event.preventDefault();
        return false;
      }
    });
  },

  _initBox: function () {
    var self = this;

    $("#drag-btn").on("mousedown", function (event) {
      self.dragElement = self.selectedEl.css("position", "");
      self.isDragging = true;
      $("#highlight-name, #select-box").hide();

      node = self.dragElement.get(0);

      self.dragMoveMutation = {
        type: "move",
        target: node,
        oldParent: node.parentNode,
        oldNextSibling: node.nextSibling,
      };

      //self.selectNode(false);
      event.preventDefault();
      return false;
    });

    $(".resize > div").on("mousedown", function (event) {
      $("#highlight-name, #highlight-box").hide();

      self.isResize = true;
      self.initialSize = {
        width: self.selectedEl.outerWidth(),
        height: self.selectedEl.outerHeight(),
      };
      self.initialPosition = false;
      self.resizeHandler = this.className;

      event.preventDefault();
      return false;
    });

    $("#down-btn").on("click", function (event) {
      $("#select-box").hide();

      Vvveb.Builder.moveNodeDown();

      event.preventDefault();
      return false;
    });

    $("#up-btn").on("click", function (event) {
      $("#select-box").hide();

      Vvveb.Builder.moveNodeUp();

      event.preventDefault();
      return false;
    });

    $("#clone-btn").on("click", function (event) {
      Vvveb.Builder.cloneNode();

      event.preventDefault();
      return false;
    });

    $("#parent-btn").on("click", function (event) {
      node = self.selectedEl.parent().get(0);

      self.selectNode(node);
      self.loadNodeComponent(node);

      event.preventDefault();
      return false;
    });

    $("#delete-btn").on("click", function (event) {
      $("#select-box").hide();

      node = self.selectedEl.get(0);

      Vvveb.Undo.addMutation({
        type: "childList",
        target: node.parentNode,
        removedNodes: [node],
        nextSibling: node.nextSibling,
      });

      self.selectedEl.remove();

      event.preventDefault();
      return false;
    });

    var addSectionBox = $("#add-section-box");
    var addSectionElement = {};

    $("#add-section-btn").on("click", function (event) {
      addSectionElement = self.selectedEl;

      var offset = $(addSectionElement).offset();
      var top =
        offset.top -
        self.frameDoc.scrollTop() +
        addSectionElement.outerHeight();
      var left =
        offset.left -
        self.frameDoc.scrollLeft() +
        addSectionElement.outerWidth() / 2 -
        addSectionBox.outerWidth() / 2;
      var outerHeight =
        $(window.FrameWindow).height() + self.frameDoc.scrollTop();

      //check if box is out of viewport and move inside
      if (left < 0) left = 0;
      if (top < 0) top = 0;
      if (left + addSectionBox.outerWidth() > self.frameDoc.outerWidth())
        left = self.frameDoc.outerWidth() - addSectionBox.outerWidth();
      if (
        top + addSectionBox.outerHeight() + self.frameDoc.scrollTop() >
        outerHeight
      )
        top = top - addSectionBox.outerHeight();

      addSectionBox.css({ top: top, left: left, display: "block" });

      event.preventDefault();
      return false;
    });

    $("#close-section-btn").on("click", function (event) {
      addSectionBox.hide();
    });

    function addSectionComponent(html, after = true) {
      var node = $(html);

      if (after) {
        addSectionElement.after(node);
      } else {
        addSectionElement.append(node);
      }

      node = node.get(0);

      Vvveb.Undo.addMutation({
        type: "childList",
        target: node.parentNode,
        addedNodes: [node],
        nextSibling: node.nextSibling,
      });
    }

    $(".components-list li ol li", addSectionBox).on("click", function (event) {
      var html = Vvveb.Components.get(this.dataset.type).html;

      addSectionComponent(
        html,
        $("[name='add-section-insert-mode']:checked").val() == "after"
      );

      addSectionBox.hide();
    });

    $(".blocks-list li ol li", addSectionBox).on("click", function (event) {
      var html = Vvveb.Blocks.get(this.dataset.type).html;

      addSectionComponent(
        html,
        $("[name='add-section-insert-mode']:checked").val() == "after"
      );

      addSectionBox.hide();
    });

    $(".sections-list li ol li", addSectionBox).on("click", function (event) {
      var html = Vvveb.Sections.get(this.dataset.type).html;

      addSectionComponent(
        html,
        $("[name='add-section-insert-mode']:checked").val() == "after"
      );

      addSectionBox.hide();
    });
  },

  /* drag and drop */
  _initDragdrop: function () {
    var self = this;
    self.isDragging = false;

    $(".drag-elements-sidepane ul > li > ol > li[data-drag-type]").on(
      "mousedown touchstart",
      function (event) {
        $this = $(this);

        $("#component-clone").remove();
        $("#highlight-name, #select-box").hide();

        if ($this.data("drag-type") == "component") {
          self.component = Vvveb.Components.get($this.data("type"));
        } else if ($this.data("drag-type") == "section") {
          self.component = Vvveb.Sections.get($this.data("type"));
        } else if ($this.data("drag-type") == "block") {
          self.component = Vvveb.Blocks.get($this.data("type"));
        }

        if (self.component.dragHtml) {
          html = self.component.dragHtml;
        } else if (Vvveb.dragHtml) {
          html = Vvveb.dragHtml;
        } else {
          html = self.component.html;
        }

        self.dragElement = $(html);
        //self.dragElement.css("border", "1px dashed #4285f4");

        if (self.component.dragStart)
          self.dragElement = self.component.dragStart(self.dragElement);

        self.isDragging = true;
        if (Vvveb.dragIcon == "html") {
          self.iconDrag = $(html)
            .attr("id", "dragElement-clone")
            .css("position", "absolute");
        } else if (self.designerMode == false) {
          self.iconDrag = $('<img src=""/>')
            .attr({
              id: "dragElement-clone",
              src: $this
                .css("background-image")
                .replace(/^url\(['"](.+)['"]\)/, "$1"),
            })
            .css({
              "z-index": 100,
              position: "absolute",
              width: "64px",
              height: "64px",
              top: event.originalEvent.y,
              left: event.originalEvent.x,
            });
        }

        $("body").append(self.iconDrag);

        event.preventDefault();
        return false;
      }
    );

    $("body").on("mouseup dragend touchend", function (event) {
      if (self.iconDrag && self.isDragging == true) {
        self.isDragging = false;
        $("#component-clone").remove();
        $("#section-actions, #highlight-name, #select-box").show();
        self.iconDrag.remove();
        if (self.dragElement) {
          self.dragElement.remove();
        }
      }
    });

    $("body").on("mousemove dragover touchmove", function (event) {
      if (self.iconDrag && self.isDragging == true) {
        var x = event.clientX || event.originalEvent.clientX;
        var y = event.clientY || event.originalEvent.clientY;

        self.iconDrag.css({ left: x - 60, top: y - 30 });

        elementMouseIsOver = document.elementFromPoint(x - 60, y - 40);

        //if drag elements hovers over iframe switch to iframe mouseover handler
        return;
        if (elementMouseIsOver && elementMouseIsOver.tagName == "IFRAME") {
          self.frameBody.trigger("mousemove", event);
          event.stopPropagation();
          self.selectNode(false);
        }
      }
    });

    $(".drag-elements-sidepane ul > ol > li > li").on(
      "mouseup dragend touchend",
      function (event) {
        self.isDragging = false;
        $("#component-clone").remove();
        $("#section-actions, #highlight-name, #select-box").show();
      }
    );
  },

  removeHelpers: function (html, keepHelperAttributes = false) {
    //tags like stylesheets or scripts
    html = html.replace(/<[^>]+?data-vvveb-helpers.+?>/gi, "");
    //attributes
    if (!keepHelperAttributes) {
      html = html.replace(/\s*data-vvveb-\w+(=["'].*?["'])?\s*/gi, "");
    }

    return html;
  },

  getHtml: function (keepHelperAttributes = true) {
    var doc = window.FrameDocument;
    var hasDoctpe = doc.doctype !== null;
    var html = "";

    $("[contenteditable]", doc).removeAttr("contenteditable");
    $("[spellcheckker]", doc).removeAttr("spellcheckker");

    $(window).triggerHandler("vvveb.getHtml.before", doc);

    if (hasDoctpe)
      html =
        "<!DOCTYPE " +
        doc.doctype.name +
        (doc.doctype.publicId ? ' PUBLIC "' + doc.doctype.publicId + '"' : "") +
        (!doc.doctype.publicId && doc.doctype.systemId ? " SYSTEM" : "") +
        (doc.doctype.systemId ? ' "' + doc.doctype.systemId + '"' : "") +
        ">\n";

    html += doc.documentElement.innerHTML + "\n</html>";

    html = this.removeHelpers(html, keepHelperAttributes);
    Vvveb.FontsManager.cleanUnusedFonts();

    $(window).triggerHandler("vvveb.getHtml.after", doc);

    var filter = $(window).triggerHandler("vvveb.getHtml.filter", html);
    if (filter) return filter;

    return html;
  },

  setHtml: function (html) {
    //update only body to avoid breaking iframe css/js relative paths
    start = html.indexOf("<body");
    end = html.indexOf("</body");

    if (start >= 0 && end >= 0) {
      body = html.slice(html.indexOf(">", start) + 1, end);
    } else {
      body = html;
    }

    if (this.runJsOnSetHtml) self.frameBody.html(body);
    else window.FrameDocument.body.innerHTML = body;

    //below methods brake document relative css and js paths
    //return self.iframe.outerHTML = html;
    //return self.documentFrame.html(html);
    //return self.documentFrame.attr("srcdoc", html);
  },

  saveAjax: function (fileName, startTemplateUrl, callback, saveUrl) {
    var data = {};
    data["file"] =
      fileName && fileName != ""
        ? fileName
        : Vvveb.FileManager.getCurrentFileName();
    data["startTemplateUrl"] = startTemplateUrl;
    if (!startTemplateUrl || startTemplateUrl == null) {
      data["html"] = this.getHtml();
    }
    let doc = document.createElement("html");
    doc.innerHTML = data["html"];
    let cf_styles = "";
    let cf_scripts = "";
    let cf_html = "";
    let cf_body_style="";

    try {
      //create sf-input-data-value with input name
      let els = doc.querySelectorAll("[name]");
      els.forEach((el) => {
        let name = el.getAttribute("name");
        let val = name.length > 0 ? `{member.${name}}` : "";
        el.setAttribute(`sf-input-data-value`, val);
      });
    } catch (err) {
      console.log(err);
    }

    try {
      //Handle forgot password
      let els = doc.querySelectorAll(`[data_fpwd_step]`);
      els.forEach((el) => {
        let key = el.getAttribute(`data_fpwd_step`);

        if (typeof key === "string") {
          key = key.toLocaleLowerCase();
          let reg = new RegExp(`({|%7b)(\/)*(${key})(}|%7d)`, "gi");
          let html = el.innerHTML;
          html = html.replace(reg, "");

          let opener = `{${key}}`;
          let closer = `{/${key}}`;

          html = `${opener}${html}${closer}`;
          el.innerHTML = html;
        }
      });
    } catch (err) {
      console.log(err);
    }

    try {
      //Deal with shipping details
      let els = doc.querySelectorAll(`[data_shipping_detail_loop]`);
      let key = "shipping_detail";
      els.forEach((el) => {
        let html = el.innerHTML;
        let reg = new RegExp(`({|%7b)(\/)*(${key})(}|%7d)`, "gi");
        html = el.innerHTML;
        html = html.replace(reg, "");

        let opener = `{${key}}`;
        let closer = `{/${key}}`;

        html = `${opener}${html}${closer}`;
        el.innerHTML = html;
      });
    } catch (err) {
      console.log(err);
    }

    try {
      //Deal with products
      let els = doc.querySelectorAll(`[data_member_order_detail]`);
      let key = "products";
      els.forEach((el) => {
        let html = el.innerHTML;
        let reg = new RegExp(`({|%7b)(\/)*(${key})(}|%7d)`, "gi");
        html = el.innerHTML;
        html = html.replace(reg, "");

        let opener = `{${key}}`;
        let closer = `{/${key}}`;

        html = `${opener}${html}${closer}`;
        el.innerHTML = html;
      });
    } catch (err) {
      console.log(err);
    }

    try {
      try {
        let headers = doc.querySelectorAll("head")[0];
        headers.querySelectorAll("style").forEach((data) => {
          cf_styles += data.innerText.trim();
        });

        let allprds = doc.querySelectorAll("[data-allproduct_anypage]");
        if (allprds.length) {
          allprds.forEach((allprd) => {
            if (allprd != "") {
              let ifrepeatinside = allprd.querySelectorAll(
                "[data-allproduct_anypage]"
              );
              if (ifrepeatinside.length > 0) {
                ifrepeatinside.forEach((ifrepeat) => {
                  ifrepeat.remove();
                });
              }
              let atr = ``;
              let atrars = [
                "limit",
                "sortby",
                "collection_id",
                "is_collection",
                "title_length"
              ];
              atrars.forEach((atrar) => {
                if (allprd.hasAttribute(`${atrar}`)) {
                  let atval = "";
                  if (atrar == "limit") {
                    atval = parseInt(allprd.getAttribute(`${atrar}`));
                    atr += `limit=${atval}  `;
                  } else if (atrar == "sortby") {
                    atval = allprd.getAttribute(`${atrar}`);
                    atr += ` sortby="${atval}" `;
                  } else if (atrar == "collection_id") {
                    atval = parseInt(allprd.getAttribute(`${atrar}`));
                    atr += ` collection_id=${atval}  `;
                  } else if (atrar == "title_length") {
                    atval = parseInt(allprd.getAttribute(`${atrar}`));
                    atr += ` title_length=${atval}  `;
                  }
                   else {
                    atr += ` is_collection=1  `;
                  }
                }
              });
              let newsrts = `{mini_collection  ${atr}}\n`;
              let endshort = `{/mini_collection}`;
              allprd.insertAdjacentHTML("beforebegin", newsrts);
              allprd.insertAdjacentHTML("afterend", endshort);
            }
          });
        }
        let oallprds = doc.querySelectorAll("[data-allproduct_orderpage]");
        if (oallprds.length) {
          oallprds.forEach((oallprd) => {
            if (oallprd != null) {
              let oifrepeatinside = oallprd.querySelectorAll(
                "[data-allproduct_orderpage]"
              );
              if (oifrepeatinside.length > 0) {
                oifrepeatinside.forEach((oifrepeat) => {
                  oifrepeat.remove();
                });
              }
              let onewsrts = `{products}\n`;
              let oendshort = `{/products}`;
              oallprd.insertAdjacentHTML("beforebegin", onewsrts);
              oallprd.insertAdjacentHTML("afterend", oendshort);
            }
          });
        }
        let allcolts = doc.querySelectorAll("[data-collection_anypage]");

        if (allcolts.length) {
          allcolts.forEach((allcolt) => {
            if (allcolt != "") {
              let cifrepeatinside = allcolt.querySelectorAll(
                "[data-collection_anypage]"
              );
              if (cifrepeatinside.length > 0) {
                cifrepeatinside.forEach((cifrepeat) => {
                  cifrepeat.remove();
                });
              }
              let atr = ``;
              if (
                allcolt.hasAttribute("limit") &&
                allcolt.hasAttribute("sortby")
              ) {
                let limit = parseInt(allcolt.getAttribute("limit"));
                let sortby = allcolt.getAttribute("sortby");
                atr = ` limit=${limit} sortby=${sortby} `;
              } else if (allcolt.hasAttribute("sortby")) {
                let sortby = allcolt.getAttribute("sortby");
                atr += ` sortby=${sortby} `;
              } else if (allcolt.hasAttribute("limit")) {
                let allcolt = allcolt.getAttribute("limit");
                atr += ` limit=${limit} `;
              }
              let newsrts = `{collections  ${atr}}\n`;
              let endshort = `{/collections}`;
              allcolt.insertAdjacentHTML("beforebegin", newsrts);
              allcolt.insertAdjacentHTML("afterend", endshort);
            }
          });
        }

        let prdFieldss = doc.querySelectorAll("[sf-element-data-html]");
        if( prdFieldss.length > 0 )
        {
          prdFieldss.forEach(element => {
            let elHTML = element.innerHTML;
            let elAttrVal = element.getAttribute("sf-element-data-html");
            let stelAttrVal = elAttrVal.replace(/(^{product.|})/gm,"");
            stelAttrVal = stelAttrVal.replace(/(^{collection.|})/gm,"");
            stelAttrVal = stelAttrVal.replace("{","");
            if( stelAttrVal !='{collection.media}' && stelAttrVal !='{collection.url}' && stelAttrVal !='url' )
            {
              let neelHTML=elHTML.replace(/\s/gmi,"-");
              neelHTML=neelHTML.replace(/(?!([&nbsp;]))[a-zA-Z0-9].*?(-|)[a-zA-Z0-9].*(?!(&nbsp;))/gm,elAttrVal);
              neelHTML=neelHTML.replace(/(?![&nbsp;]).*[^\w].*/gmi,elAttrVal);
              element.innerHTML=neelHTML.replace(/\-/gmi,"&nbsp;");
              element.setAttribute("sf-element-data-backup",encodeURIComponent(elHTML));
            }
          });
        } 

        try {
          headers
            .querySelectorAll("script[cfdefaultscript='1']")
            .forEach((data) => {
              cf_scripts += data.innerText.trim();
            });
        } catch (err) {
          console.log(err);
        }
      } catch (err) {
        console.log(err);
      }
      try {
        
        let htmlbody = doc.querySelectorAll("body")[0];
        let body_style = htmlbody.getAttribute("style");
        let body_style_html = htmlbody.querySelector("#ssf__custom_body_bimg");

        console.log(body_style_html,body_style);
        if( body_style_html  && body_style_html.tagName=="STYLE"  )
        {
          body_style_html.innerHTML = `body {${body_style}}`;
        }else{
          if(body_style!=null)
          {
            cf_body_style = `<style id="ssf__custom_body_bimg">body{${body_style}}</style>`;
            htmlbody.insertAdjacentHTML("beforeend", cf_body_style);
          }
        }
        cf_html = htmlbody.innerHTML.trim();
      } catch (err) {
        console.log(err);
      }
    } catch (err) {
      console.log(err);
    }
    cf_html = cf_html.replace(cf_global_fontawesome_url, "");
    // console.log(cf_html);

    cf_html = cf_html.replace(
      /((true|false)\/)+(text\/javascript)/gi,
      `text/javascript`
    );

    let tfreg = /(type=)+("|')+((true\/)|(false\/))+("|')+/gi;
    cf_html = cf_html.replace(tfreg, `<script type="text/javascript">`);

    cf_html = cf_html.replace(/(contenteditable=\"true\")+/gi, "");

    let bgimgreg = /(url\(&quot;)+(.(?!(quot;)))*(&quot;)+/gi;
    let matchesbg = cf_html.match(bgimgreg);

    //console.log(matchesbg);
    if (Array.isArray(matchesbg)) {
      matchesbg.forEach((matchesbg_each) => {
        cf_html = cf_html.replace(
          matchesbg_each,
          matchesbg_each.replace(/&quot;/g, "'")
        );
      });
    }
    let googleFontLink='';
    let googleFont = doc.getElementsByTagName("head")[0].querySelector("#google-fonts-link");
    if( googleFont != null  )
    {
      googleFont.removeAttribute("id");
      googleFontLink = googleFont.outerHTML;
    }

    //cf_html=cf_html.replace(`<script type='true/false/'>`,`<script type="text/javascript">`);
    console.log(cf_request.escHTML(cf_html));
    let formdata = new FormData(document.createElement("form"));
    formdata.append("savetemplate", 1);
    formdata.append("reqfrom_editor", 1);
    formdata.append("funnel_id", cf_request.escHTML(cf_page_fid));
    formdata.append("type", cf_request.escHTML(cf_page_abtype));
    formdata.append("lbl", cf_request.escHTML(cf_page_lbl));
    formdata.append("category", cf_request.escHTML(cf_page_categ));
    formdata.append("folder", cf_request.escHTML(cf_page_folder));
    formdata.append("html", cf_request.escHTML(cf_html));
    formdata.append("fontlink", cf_request.escHTML(googleFontLink));
    //alert(cf_styles);
    formdata.append("css", cf_request.escHTML(cf_styles));
    //alert(cf_scripts);
    formdata.append("js", cf_request.escHTML(cf_scripts));
    formdata.append("cfhttp", 1);

    var config_alert = document.createElement("div");
    config_alert.setAttribute(
      "style",
      "top:20%;left:50%;transform:translate(-50%,-50%);position:fixed;background-color:white;padding:10px;z-index:10;border-radius:2px;"
    );
    config_alert.innerHTML =
      "<h3><img src='assets/img/visual_cog.gif' style='max-width:100px;margin-left:8px;'>" +
      t("Saving Changes...") +
      "</h3>";
    document.body.appendChild(config_alert);

    cf_request.setCookie(
      "templateedited_" + cf_page_fid + "_" + cf_page_lbl,
      cf_page_lbl,
      30
    );

    $.ajax({
      type: "POST",
      url: "req.php", //set your server side save script url
      data: formdata,
      cache: false,
      processData: false,
      contentType: false,
      success: function (data) {
        console.log(data);
        //if (callback) callback(data);
        setTimeout(function () {
          cf_request.postRequestCb(
            "req.php",
            {
              take_funnel_screenshot: 1,
              funnel: cf_page_fid,
              page: cf_page_folder,
              abtype: cf_page_abtype,
              lavel: cf_page_lbl,
              category: cf_page_categ,
            },
            (res) => {
              document.body.removeChild(config_alert);
              var savealert = document.createElement("div");
              savealert.setAttribute(
                "style",
                "top:20%;left:50%;transform:translate(-50%,-50%);position:fixed;background-color:white;padding:10px;z-index:10;border-radius:2px;"
              );
              savealert.innerHTML =
                "<h3><img src='assets/img/visual_done.gif?id=" +
                Math.random() +
                "' style='max-width:100px;margin-left:8px;'>" +
                t("Saved Changes") +
                "</h3>";
              document.body.appendChild(savealert);
              if(track_block_save)
						  {
							  window.location.reload();
						  }
              setTimeout(function () {
                document.body.removeChild(savealert);
              }, 5000);
            }
          );
        }, 2000);
      },
      error: function (data) {
        document.body.removeChild(config_alert);
        alert(data.responseText);
      },
    });
  },

  setDesignerMode: function (designerMode = false) {
    this.designerMode = designerMode;
  },
};

Vvveb.CodeEditor = {
  isActive: false,
  oldValue: "",
  doc: false,

  init: function (doc) {
    $("#vvveb-code-editor textarea").val(Vvveb.Builder.getHtml());

    $("#vvveb-code-editor textarea").keyup(function () {
      delay(() => Vvveb.Builder.setHtml(this.value), 1000);
    });

    //load code on document changes
    Vvveb.Builder.frameBody.on(
      "vvveb.undo.add vvveb.undo.restore",
      function (e) {
        Vvveb.CodeEditor.setValue();
      }
    );
    //load code when a new url is loaded
    Vvveb.Builder.documentFrame.on("load", function (e) {
      Vvveb.CodeEditor.setValue();
    });

    this.isActive = true;
  },

  setValue: function (value) {
    if (this.isActive) {
      $("#vvveb-code-editor textarea").val(Vvveb.Builder.getHtml());
    }
  },

  destroy: function (element) {
    //this.isActive = false;
  },

  toggle: function () {
    if (this.isActive != true) {
      this.isActive = true;
      return this.init();
    }
    this.isActive = false;
    this.destroy();
  },
};

Vvveb.Gui = {
  init: function () {
    $("[data-vvveb-action]").each(function () {
      on = "click";
      if (this.dataset.vvvebOn) on = this.dataset.vvvebOn;

      $(this).on(on, Vvveb.Gui[this.dataset.vvvebAction]);
      if (this.dataset.vvvebShortcut) {
        $(document).bind(
          "keydown",
          this.dataset.vvvebShortcut,
          Vvveb.Gui[this.dataset.vvvebAction]
        );
        $(window.FrameDocument, window.FrameWindow).bind(
          "keydown",
          this.dataset.vvvebShortcut,
          Vvveb.Gui[this.dataset.vvvebAction]
        );
      }
    });
  },

  undo: function () {
    if (Vvveb.WysiwygEditor.isActive) {
      Vvveb.WysiwygEditor.undo();
    } else {
      Vvveb.Undo.undo();
    }
    Vvveb.Builder.selectNode();
  },

  redo: function () {
    if (Vvveb.WysiwygEditor.isActive) {
      Vvveb.WysiwygEditor.redo();
    } else {
      Vvveb.Undo.redo();
    }
    Vvveb.Builder.selectNode();
  },

  //show modal with html content
  save: function () {
    $("#textarea-modal textarea").val(Vvveb.Builder.getHtml());
    $("#textarea-modal").modal();
  },

  //post html content through ajax to save to filesystem/db
  saveAjax: function () {
    /*
    **  New editor cocde
    ----------------------
    // var saveUrl = this.dataset.vvvebUrl;
    // var url = Vvveb.FileManager.getPageData("file");

    // return Vvveb.Builder.saveAjax(
    //   url,
    //   null,
    //   function (data) {
    //     var messageModal = new bootstrap.Modal(
    //       document.getElementById("message-modal"),
    //       {
    //         keyboard: false,
    //       }
    //     );

    //     $("#message-modal .modal-body").html(data);
    //     messageModal.show();
    //   },
    //   saveUrl
    // );
    */
    /*
     ** Old Editor Code
    */  
    var url = Vvveb.FileManager.getCurrentUrl();
		
		return Vvveb.Builder.saveAjax(url, null, function (data) {
			$('#message-modal').modal().find(".modal-body").html("File saved at: " + data);
		});		
  },

  download: function () {
    let url = new URL(window.location.href);
    //filename = /[^\/]+$/.exec(Vvveb.Builder.iframe.src)[0];

    let filename = `index.html`;
    try {
      filename = `${url.searchParams.get("fid")}-${url.searchParams.get(
        "folder"
      )}.html`;
    } catch (err) {}
    //cf_installation_url
    let content_html = Vvveb.Builder.getHtml();
    //assets/bootstrap
    //assets/js

    content_html = content_html.replace(
      /assets\/bootstrap/g,
      `${cf_installation_url}/assets/bootstrap`
    );
    content_html = content_html.replace(
      /assets\/js/g,
      `${cf_installation_url}/assets/js`
    );

    uriContent =
      "data:application/octet-stream," + encodeURIComponent(content_html);

    var link = document.createElement("a");
    if ("download" in link) {
      link.download = filename;
      link.href = uriContent;
      link.target = "_blank";

      document.body.appendChild(link);
      result = link.click();
      document.body.removeChild(link);
      link.remove();
    } else {
      location.href = uriContent;
    }
  },

  viewport: function () {
    $("#canvas").attr("class", this.dataset.view);
  },

  toggleEditor: function () {
    $("#vvveb-builder").toggleClass("bottom-panel-expand");
    $("#toggleEditorJsExecute").toggle();
    Vvveb.CodeEditor.toggle();
  },

  toggleEditorJsExecute: function () {
    Vvveb.Builder.runJsOnSetHtml = this.checked;
  },

  preview: function () {
    Vvveb.Builder.isPreview == true
      ? (Vvveb.Builder.isPreview = false)
      : (Vvveb.Builder.isPreview = true);
    $("#iframe-layer").toggle();
    $("#vvveb-builder").toggleClass("preview");
  },

  fullscreen: function () {
    launchFullScreen(document); // the whole page
  },

  search: function () {
    let searchText = this.value.toLowerCase();
    let panel = $("div > ul", this.parentNode.parentNode);

    $("li ol li", panel).each(function () {
      $this = $(this);

      $this.hide();
      if ($this.data("search").indexOf(searchText) > -1) $this.show();
    });
  },

  clearSearch: function (e) {
    $("input", this.parentNode).val("").keyup();
  },

  expand: function (e) {
    $(
      'input.header_check[type="checkbox"]',
      this.parentNode.parentNode.parentNode
    ).prop("checked", true);
  },

  collapse: function (e) {
    $(
      'input.header_check[type="checkbox"]',
      this.parentNode.parentNode.parentNode
    ).prop("checked", false);
  },

  //Pages, file/components tree
  newPage: function () {
    var newPageModal = $("#new-page-modal");

    newPageModal
      .modal("show")
      .find("form")
      .off("submit")
      .submit(function (e) {
        var data = {};
        $.each($(this).serializeArray(), function () {
          data[this.name] = this.value;
        });

        var name = (data["name"] = data["file"]);
        data["url"] = data["file"] = data["folder"] + "/" + data["file"];

        Vvveb.FileManager.addPage(data.name, data);
        e.preventDefault();

        return Vvveb.Builder.saveAjax(
          data.file,
          data.startTemplateUrl,
          function (data) {
            Vvveb.FileManager.loadPage(name);
            Vvveb.FileManager.scrollBottom();
            newPageModal.modal("hide");
          },
          this.action
        );
      });
  },

  deletePage: function () {},

  setDesignerMode: function () {
    //aria-pressed attribute is updated after action is called and we check for false instead of true
    var designerMode = this.attributes["aria-pressed"].value != "true";
    Vvveb.Builder.setDesignerMode(designerMode);
  },
  //layout
  togglePanel: function (panel, cssVar) {
    var panel = $(panel);
    var body = $("body");
    var prevValue = body.css(cssVar);
    if (prevValue !== "0px") {
      panel.data("layout-toggle", prevValue);
      body.css(cssVar, "0px");
      panel.hide();
      return false;
    } else {
      prevValue = panel.data("layout-toggle");
      body.css(cssVar, "");
      panel.show();
      return true;
    }
  },

  toggleFileManager: function () {
    Vvveb.Gui.togglePanel("#filemanager", "--builder-filemanager-height");
  },

  toggleLeftColumn: function () {
    Vvveb.Gui.togglePanel("#left-panel", "--builder-left-panel-width");
  },

  toggleRightColumn: function (rightColumnEnabled = null) {
    rightColumnEnabled = Vvveb.Gui.togglePanel(
      "#right-panel",
      "--builder-right-panel-width"
    );

    $("#vvveb-builder").toggleClass("no-right-panel");
    $(".component-properties-tab").toggle();

    Vvveb.Components.componentPropertiesElement =
      (rightColumnEnabled ? "#right-panel" : "#left-panel") +
      " .component-properties";
    if ($("#properties").is(":visible"))
      $(".component-tab a").show().tab("show");
  },
};
/*
Vvveb.StyleManager = {
  styles: {},
  cssContainer: false,
  mobileWidth: "320px",
  tabletWidth: "768px",

  init: function (doc) {
    if (doc) {
      var style = false;
      var _style = false;

      //check if editor style is present
      for (let i = 0; i < doc.styleSheets.length; i++) {
        _style = doc.styleSheets[i];
        if (_style.ownerNode.id && _style.ownerNode.id == "vvvebjs-styles") {
          style = _style;
          break;
        }
      }

      //if style element does not exist create it
      if (!style) {
        this.cssContainer = $('<style id="vvvebjs-styles"></style>');
        $(doc.head).append(this.cssContainer);
        return this.cssContainer;
      }

      //if style exist then load all css styles for editor
      for (let j = 0; j < style.cssRules.length; j++) {
        media =
          typeof style.cssRules[j].media === "undefined"
            ? "desktop"
            : style.cssRules[j].media[0] === "screen and (max-width: 1220px)"
            ? "tablet"
            : style.cssRules[j].media[0] === "screen and (max-width: 320px)"
            ? "mobile"
            : "desktop";

        selector =
          media === "desktop"
            ? style.cssRules[j].selectorText
            : style.cssRules[j].cssRules[0].selectorText;
        styles =
          media === "desktop"
            ? style.cssRules[j].style
            : style.cssRules[j].cssRules[0].style;

        if (media) {
          this.styles[media] = {};
          if (selector) {
            this.styles[media][selector] = {};

            for (let k = 0; k < styles.length; k++) {
              property = styles[k];
              value = styles[property];

              this.styles[media][selector][property] = value;
            }
          }
        }
      }

      return (this.cssContainer = $("#vvvebjs-styles", doc));
    }
  },

  getSelectorForElement: function (element) {
    var currentElement = element;
    var selector = [];

    while (currentElement.parentElement) {
      let elementSelector = "";
      let classSelector = Array.from(currentElement.classList)
        .map(function (className) {
          if (Vvveb.Builder.ignoreClasses.indexOf(className) == -1) {
            return "." + className;
          }
        })
        .join("");

      //stop at a unique element (with id)
      if (currentElement.id) {
        elementSelector = "#" + currentElement.id;
        selector.push(elementSelector);
        break;
      } else if (classSelector) {
        //class selector
        elementSelector = classSelector;
      } else {
        //element (tag) selector
        var tag = currentElement.tagName.toLowerCase();
        //exclude top most element body unless the parent element is body
        if (tag != "body" || (tag == "body" && selector.length <= 1)) {
          elementSelector = tag;
        }
      }

      if (elementSelector) {
        selector.push(elementSelector);
      }

      currentElement = currentElement.parentElement;
    }

    return selector.reverse().join(" > ");
  },

  setStyle: function (element, styleProp, value) {
    selector = this.getSelectorForElement(element.get(0));
    media = $("#canvas").hasClass("tablet")
      ? "tablet"
      : $("#canvas").hasClass("mobile")
      ? "mobile"
      : "desktop";

    //styles[media][selector][styleProp] = value
    if (!this.styles[media]) {
      this.styles[media] = {};
    }
    if (!this.styles[media][selector]) {
      this.styles[media][selector] = {};
    }
    if (!this.styles[media][selector][styleProp]) {
      this.styles[media][selector][styleProp] = {};
    }
    this.styles[media][selector][styleProp] = value;

    this.generateCss(element,media);

    return element;
    //uncomment bellow code to set css in element's style attribute
    //return element.css(styleProp, value);
  },

  generateCss: function (element,media) {
    //var css = "";
    //for (selector in this.styles[media]) {

    //	css += `${selector} {`;
    //	for (property in this.styles[media][selector]) {
    //		value = this.styles[media][selector][property];
    //		css += `${property}: ${value};`;
    //	}
    //	css += '}';
    //}

    //this.cssContainer.html(css);

    //return element;
    var css = "";
    for (media in this.styles) {
      if (media === "tablet" || media === "mobile") {
        css += `@media screen and (max-width: ${
          media === "tablet" ? this.tabletWidth : this.mobileWidth
        }){`;
      }
      for (selector in this.styles[media]) {
        css += `${selector} {`;
        let pos="";
        for (property in this.styles[media][selector]) {
          value = this.styles[media][selector][property];
          // if( property=="background-position-x" || property=="background-position-y" )
          // {
          //   pos+=`${value} `;
          //   css += `background-position: ${pos};`;
          // }else{
          // }
          css += `${property}: ${value};`;
        }
        css += "}";
      }
      if (media === "tablet" || media === "mobile") {
        css += `}`;
      }
    }
    console.log(css);

    this.cssContainer.html(css);

    return element;
  },

  _getCssStyle: function (element, styleProp) {
    var value = "";
    var el = element.get(0);

    selector = this.getSelectorForElement(el);
    media = $("#canvas").hasClass("tablet")
      ? "tablet"
      : $("#canvas").hasClass("mobile")
      ? "mobile"
      : "desktop";

    if (el.style && el.style.length > 0 && el.style[styleProp])
      //check inline
      var value = el.style[styleProp];
    else if (
      this.styles[media] !== undefined &&
      this.styles[media][selector] !== undefined &&
      this.styles[media][selector][styleProp] !== undefined
    ) {
      //check defined css
      var value = this.styles[media][selector][styleProp];
    } else if (window.getComputedStyle) {
      var value = document.defaultView.getDefaultComputedStyle
        ? document.defaultView
            .getDefaultComputedStyle(el, null)
            .getPropertyValue(styleProp)
        : window.getComputedStyle(el, null).getPropertyValue(styleProp);
    }

    return value;
  },

  getStyle: function (element, styleProp) {
    return this._getCssStyle(element, styleProp);
  },
};
*/
Vvveb.StyleManager = {
  init: function (doc) {
    if (doc) {
      var style = false;
      var _style = false;

      //check if editor style is present
      for (let i = 0; i < doc.styleSheets.length; i++) {
        _style = doc.styleSheets[i];
        if (_style.ownerNode.id && _style.ownerNode.id == "vvvebjs-styles") {
          style = _style;
          break;
        }
      }

      //if style element does not exist create it
      if (!style) {
        this.cssContainer = $('<style id="vvvebjs-styles"></style>');
        $(doc.head).append(this.cssContainer);
        return this.cssContainer;
      }

      //if style exist then load all css styles for editor
      for (let j = 0; j < style.cssRules.length; j++) {
        media =
          typeof style.cssRules[j].media === "undefined"
            ? "desktop"
            : style.cssRules[j].media[0] === "screen and (max-width: 1220px)"
            ? "tablet"
            : style.cssRules[j].media[0] === "screen and (max-width: 320px)"
            ? "mobile"
            : "desktop";

        selector =
          media === "desktop"
            ? style.cssRules[j].selectorText
            : style.cssRules[j].cssRules[0].selectorText;
        styles =
          media === "desktop"
            ? style.cssRules[j].style
            : style.cssRules[j].cssRules[0].style;

        if (media) {
          this.styles[media] = {};
          if (selector) {
            this.styles[media][selector] = {};

            for (let k = 0; k < styles.length; k++) {
              property = styles[k];
              value = styles[property];

              this.styles[media][selector][property] = value;
            }
          }
        }
      }

      return (this.cssContainer = $("#vvvebjs-styles", doc));
    }
  },
	setStyle: function(element, styleProp, value) {

    if( styleProp == "font-family" )
    {
      setAllFontFamily( element, value );
    }
    else{
      element[0].style.setProperty(`${styleProp}`, `${value}`,'important');
    }
    return element
	},
	
	
	_getCssStyle: function(element, styleProp){
		var value = "";
		var el = element.get(0);
		
		if (el.style && el.style.length > 0 && el.style[styleProp])//check inline
			var value = el.style[styleProp];
		else
		if (el.currentStyle)	//check defined css
			var value = el.currentStyle[styleProp];
		else if (window.getComputedStyle)
		{
			var value = document.defaultView.getDefaultComputedStyle ? 
							document.defaultView.getDefaultComputedStyle(el,null).getPropertyValue(styleProp) : 
							window.getComputedStyle(el,null).getPropertyValue(styleProp);
		}
		return value;
	},
	
	getStyle: function(element,styleProp){
		return this._getCssStyle(element, styleProp);
	}
}
// */
Vvveb.ContentManager = {
  getAttr: function (element, attrName) {
    return element.attr(attrName);
  },

  setAttr: function (element, attrName, value) {
    return element.attr(attrName, value);
  },

  setHtml: function (element, html) {
    return element.html(html);
  },

  getHtml: function (element) {
    return element.html();
  },
};

// Set all type font-family as a inlince style 

function setAllFontFamily( element, value ){
  let attrval = element.attr('st-font-family');
  let pr = element.parents("body").prev("head");
  if( attrval !=null )
  {
    let styl = $(pr).find(`[remove-css-${attrval}]`);
    $(styl).remove();
    element.removeClass(attrval);
    element.removeAttr("st-font-family");
  }
  let propStyle = document.createElement("STYLE");
  let nodenam = element[0].tagName; 
  let randomn = Math.floor(Math.random() * (9999999 - 100000)) + 1000000;
  let clasName = `${nodenam}--${randomn}`;
  
  element.attr("st-font-family",clasName);
  propStyle.setAttribute(`remove-css-${clasName}`,'1');
  element.addClass(clasName);
  eleStyle = `.${clasName}{ font-family:${value} !important }`;
  propStyle.innerHTML=eleStyle;
  $(pr).append(propStyle);
}

function getNodeTree(node, parent, allowedComponents) {
  function getNodeTreeTraverse(node, parent) {
    if (node.hasChildNodes()) {
      for (var j = 0; j < node.childNodes.length; j++) {
        child = node.childNodes[j];

        //skip text and comments nodes
        if (child.nodeType == 3 || child.nodeType == 8) {
          continue;
        }

        if (
          child &&
          child["attributes"] != undefined &&
          (matchChild = Vvveb.Components.matchNode(child))
        ) {
          if (
            Array.isArray(allowedComponents) &&
            allowedComponents.indexOf(matchChild.type) == -1
          ) {
            element = getNodeTreeTraverse(child, parent);
            continue;
          }

          element = {
            name: matchChild.name,
            image: matchChild.image,
            type: matchChild.type,
            node: child,
            children: [],
          };

          element.children = [];
          parent.push(element);

          element = getNodeTreeTraverse(child, element.children);
        } else {
          element = getNodeTreeTraverse(child, parent);
        }
      }
    }

    return false;
  }

  getNodeTreeTraverse(node, parent);
}

function drawComponentsTree(tree) {
  var j = 1;
  var prefix = Math.floor(Math.random() * 100);

  function drawComponentsTreeTraverse(tree) {
    var html = $("<ol></ol>");
    j++;

    for (i in tree) {
      var node = tree[i];
      var id = prefix + "-" + j + "-" + i;

      if (tree[i].children.length > 0) {
        var li = $(
          '<li data-component="' +
            node.name +
            '">\
								  <label for="id' +
            id +
            '" style="background-image:url(' +
            Vvveb.imgBaseUrl +
            node.image +
            ')"><span>' +
            node.name +
            '</span></label>\
								  <input type="checkbox" id="id' +
            id +
            '">\
							  </li>'
        );
        li.append(drawComponentsTreeTraverse(node.children));
      } else {
        var li = $(
          '<li data-component="' +
            node.name +
            '" class="file">\
							  <label for="id' +
            id +
            '" style="background-image:url(' +
            Vvveb.imgBaseUrl +
            node.image +
            ')"><span>' +
            node.name +
            '</span></label>\
							  <input type="checkbox" id="id' +
            id +
            '">\
							  </li>'
        );
      }

      li.data("node", node.node);
      html.append(li);
    }

    return html;
  }

  return drawComponentsTreeTraverse(tree);
}

var selected = null;
var dragover = null;

Vvveb.SectionList = {
  selector: ".sections-container",
  allowedComponents: {},

  init: function (allowedComponents = {}) {
    this.allowedComponents = allowedComponents;

    $(this.selector)
      .on("click", "> div .controls", function (e) {
        var node = $(e.currentTarget.parentNode).data("node");
        if (node) {
          delay(
            () =>
              Vvveb.Builder.frameHtml.animate(
                {
                  scrollTop: $(node).offset().top - $(node).height() / 2,
                },
                500
              ),
            300
          );

          //node.click();
          Vvveb.Builder.selectNode(node);
          Vvveb.Builder.loadNodeComponent(node);
        }
      })
      .on("dblclick", "> div", function (e) {
        node = $(e.currentTarget).data("node");
        node.click();
      });

    $(this.selector)
      .on("click", "li[data-component] label ", function (e) {
        node = $(e.currentTarget.parentNode).data("node");

        delay(
          () =>
            Vvveb.Builder.frameHtml.animate(
              {
                scrollTop: $(node).offset().top - $(node).height() / 2,
              },
              1000
            ),
          300
        );

        node.click();
      })
      .on("mouseenter", "li[data-component] label", function (e) {
        node = $(e.currentTarget.parentNode).data("node");

        delay(
          () =>
            Vvveb.Builder.frameHtml.animate(
              {
                scrollTop: $(node).offset().top - $(node).height() / 2,
              },
              500
            ),
          1000
        );

        $(node).trigger("mousemove");
      });

    $(this.selector).on("dragstart", "> div", this.dragStart);
    $(this.selector).on("dragover", "> div", this.dragOver);
    $(this.selector).on("dragend", "> div", this.dragEnd);

    $(this.selector).on("click", ".delete-btn", function (e) {
      var section = $(e.currentTarget).parents(".section-item");
      var node = section.data("node");
      node.remove();
      section.remove();

      e.stopPropagation();
      e.preventDefault();
    });

    $(".sections-list")
      .on("mouseenter", "li[data-section]", function (e) {
        var src = $("img", this).attr("src");
        $(".block-preview img").attr("src", src).show();
      })
      .on("mouseleave", "li[data-section]", function (e) {
        $(".block-preview img").attr("src", "").hide();
      });
    /*
		  $(this.selector).on("click", ".up-btn", function (e) {
			  var section = $(e.currentTarget).parents(".section-item");
			  var node = section.data("node");
			  Vvveb.Builder.moveNodeUp(node);
			  Vvveb.Builder.moveNodeUp(section.get(0));
			  
			  e.preventDefault();
		  });
  
  
		  $(this.selector).on("click", ".down-btn", function (e) {
			  var section = $(e.currentTarget).parents(".section-item");
			  var node = section.data("node");
			  Vvveb.Builder.moveNodeDown(node);
			  Vvveb.Builder.moveNodeDown(section.get(0));
			  
			  e.preventDefault();
		  });
		  */

    var self = this;
    $("#sections .sections-list").on(
      "click",
      " .add-section-btn",
      function (e) {
        var section = Vvveb.Sections.get(this.parentNode.dataset.type);
        var node = $(section.html);
        var sectionType = node[0].tagName.toLowerCase();
        var afterSection = $(sectionType + ":last", Vvveb.Builder.frameBody);

        if (afterSection.length) {
          afterSection.after(node);
        } else {
          if (sectionType != "footer") {
            afterSection = $("footer:first", Vvveb.Builder.frameBody);

            if (afterSection.length) {
              afterSection.before(node);
            } else {
              $(Vvveb.Builder.frameBody).append(node);
            }
          } else {
            $(Vvveb.Builder.frameBody).append(node);
          }
        }

        Vvveb.Builder.frameHtml.animate(
          {
            scrollTop: $(node).offset().top,
          },
          1000
        );

        node.click();

        node = node.get(0);
        Vvveb.Undo.addMutation({
          type: "childList",
          target: node.parentNode,
          addedNodes: [node],
          nextSibling: node.nextSibling,
        });

        self.loadSections();
        e.preventDefault();
      }
    );

    $(this.selector).on("click", ".properties-btn", function (e) {
      var section = $(e.currentTarget).parents(".section-item");
      var node = section.data("node");
      node.click();

      e.preventDefault();
    });
  },

  getSections: function () {
    var sections = [];
    var sectionList = $(
      "> section, > header, > footer, > main, > nav",
      window.FrameDocument.body
    );

    sectionList.each(function (i, node) {
      var id = node.id ? node.id : node.dataset.name;
      if (!id) {
        id = "section-" + Math.floor(Math.random() * 10000);
      }
      var section = {
        name: id.replace(/[^\w+]+/g, " "),
        id: node.id,
        type: node.tagName.toLowerCase(),
        node: node,
      };
      sections.push(section);
    });

    return sections;
  },

  loadComponents: function (sectionListItem, section, allowedComponents = {}) {
    var tree = [];
    getNodeTree(section, tree, allowedComponents);

    var html = drawComponentsTree(tree);
    $("ol", sectionListItem).replaceWith(html);
  },

  addSection: function (data) {
    var section = $(tmpl("vvveb-section", data));
    section.data("node", data.node);
    $(this.selector).append(section);

    this.loadComponents(section, data.node, this.allowedComponents);
  },

  loadSections: function () {
    var sections = this.getSections();

    $(this.selector).html("");
    for (i in sections) {
      this.addSection(sections[i]);
    }
  },

  //drag and drop
  dragOver: function (e) {
    if (e.target != dragover && e.target.className == "section-item") {
      if (dragover) {
        dragover.classList.remove("drag-over");
      }
      dragover = e.target;
      dragover.classList.add("drag-over");
    }
  },

  dragEnd: function (e) {
    if (dragover) {
      var parent = selected.parentNode;
      var selectedNode = $(selected).data("node");
      var replaceNode = $(dragover).data("node");

      if (dragover.offsetTop > selected.offsetTop) {
        //replace section item list
        parent.insertBefore(selected, dragover.nextElementSibling);
        //replace section
        replaceNode.parentNode.insertBefore(
          selectedNode,
          replaceNode.nextElementSibling
        );
      } else {
        //replace section item list
        parent.insertBefore(selected, dragover);
        //replace section
        replaceNode.parentNode.insertBefore(selectedNode, replaceNode);
      }

      dragover.classList.remove("drag-over");

      var node = selectedNode.get(0);

      self.dragMoveMutation = {
        type: "move",
        target: node,
        oldParent: node.parentNode,
        oldNextSibling: node.nextSibling,
      };
    }

    selected = null;
    dragover = null;
  },

  dragStart: function (e) {
    selected = e.target;
  },
};

Vvveb.FileManager = {
  tree: false,
  pages: {},
  currentPage: false,
  allowedComponents: {},

  init: function (allowedComponents = {}) {
    this.allowedComponents = allowedComponents;
    this.tree = $("#filemanager .tree > ol").html("");

    $(this.tree).on("click", "a", function (e) {
      e.preventDefault();
      return false;
    });

    $(this.tree).on("click", "li[data-page] label", function (e) {
      var page = $(this.parentNode).data("page");
      if (page) Vvveb.FileManager.loadPage(page, allowedComponents);
      return false;
    });

    $(this.tree)
      .on("click", "li[data-component] label ", function (e) {
        node = $(e.currentTarget.parentNode).data("node");

        delay(
          () =>
            Vvveb.Builder.frameHtml.animate(
              {
                scrollTop: $(node).offset().top - $(node).height() / 2,
              },
              500
            ),
          500
        );

        node.click();
      })
      .on("mouseenter", "li[data-component] label", function (e) {
        node = $(e.currentTarget.parentNode).data("node");

        delay(
          () =>
            Vvveb.Builder.frameHtml.animate(
              {
                scrollTop: $(node).offset().top - $(node).height() / 2,
              },
              500
            ),
          1000
        );

        $(node).trigger("mousemove");
      });
  },

  addPage: function (name, data) {
    this.pages[name] = data;
    data["name"] = name;

    var folder = this.tree;
    if (data.folder) {
      if (
        !(folder = this.tree.find('li[data-folder="' + data.folder + '"]'))
          .length
      ) {
        data.folderTitle = data.folder[0].toUpperCase() + data.folder.slice(1);
        folder = $(tmpl("vvveb-filemanager-folder", data));
        this.tree.append(folder);
      }

      folder = folder.find("> ol");
    }

    folder.append(tmpl("vvveb-filemanager-page", data));
  },

  addPages: function (pages) {
    for (page in pages) {
      this.addPage(pages[page]["name"], pages[page]);
    }
  },

  addComponent: function (name, url, title, page) {
    $("[data-page='" + page + "'] > ol", this.tree).append(
      tmpl("vvveb-filemanager-component", {
        name: name,
        url: url,
        title: title,
      })
    );
  },

  loadComponents: function (allowedComponents = {}) {
    var tree = [];
    getNodeTree(window.FrameDocument.body, tree, allowedComponents);

    var html = drawComponentsTree(tree);
    $("[data-page='" + this.currentPage + "'] > ol", this.tree).replaceWith(
      html
    );
  },

  getCurrentUrl: function () {
    if (this.currentPage) return this.pages[this.currentPage]["url"];
  },

  getPageData: function (key) {
    if (this.currentPage) return this.pages[this.currentPage][key];
  },

  getCurrentFileName: function () {
    if (this.currentPage) {
      var folder = this.pages[this.currentPage]["folder"];
      folder = folder ? folder + "/" : "";
      return folder + this.pages[this.currentPage]["file"];
    }
  },

  reloadCurrentPage: function () {
    if (this.currentPage) return this.loadPage(this.currentPage);
  },

  loadPage: function (name, allowedComponents = false, disableCache = true) {
    $("[data-page]", this.tree).removeClass("active");
    $("[data-page='" + name + "']", this.tree).addClass("active");

    this.currentPage = name;
    var url = this.pages[name]["url"];

    Vvveb.Builder.loadUrl(
      url +
        (disableCache
          ? (url.indexOf("?") > -1 ? "&r=" : "?r=") + Math.random()
          : ""),
      function () {
        Vvveb.FileManager.loadComponents(allowedComponents);
        Vvveb.SectionList.loadSections(allowedComponents);
        Vvveb.StyleManager.init();
      }
    );
  },

  scrollBottom: function () {
    var scroll = this.tree.parent();
    scroll.scrollTop(scroll.prop("scrollHeight"));
  },
};

Vvveb.Breadcrumb = {
  tree: false,

  init: function () {
    this.tree = $(".breadcrumb-navigator > .breadcrumb").html("");

    $(this.tree)
      .on("click", ".breadcrumb-item", function (e) {
        var node = $(this).data("node");
        if (node) {
          node.click();
        }
        e.preventDefault();
      })
      .on("mouseenter", ".breadcrumb-item", function (e) {
        var node = $(this).data("node");

        delay(
          () =>
            Vvveb.Builder.frameHtml.animate(
              {
                scrollTop: $(node).offset().top - $(node).height() / 2,
              },
              500
            ),
          1000
        );

        $(node).trigger("mousemove");
      });
  },

  addElement: function (data, element) {
    var li = $(tmpl("vvveb-breadcrumb-navigaton-item", data));
    li.data("node", element);
    $(this.tree).prepend(li);
  },

  loadBreadcrumb: function (element) {
    this.tree.html("");
    var currentElement = element;

    while (currentElement.parentElement) {
      this.addElement(
        {
          name: Vvveb.Builder._getElementType(currentElement).toLowerCase(),
        },
        currentElement
      );

      currentElement = currentElement.parentElement;
    }
  },
};

Vvveb.FontsManager = {
  activeFonts: [],
  providers: {}, //{"google":GoogleFontsManager};

  addProvider: function (provider, Obj) {
    this.providers[provider] = Obj;
  },

  //add also element so we can keep track of the used fonts to remove unused ones
  addFont: function (provider, fontFamily, element = false) {
    if (!provider) return;

    let providerObj = this.providers[provider];
    if (providerObj) {
      providerObj.addFont(fontFamily);
      this.activeFonts.push({ provider, fontFamily, element });
    }
  },

  removeFont: function (provider, fontFamily) {
    let providerObj = this.providers[provider];
    if (provider != "default" && providerObj) {
      providerObj.removeFont(fontFamily);
    }
  },

  //check if the added fonts are still used for the elements they were set and remove unused ones
  cleanUnusedFonts: function () {
    for (i in this.activeFonts) {
      let elementFont = this.activeFonts[i];
      console.log(elementFont);
      if (elementFont.element) {
        if (
          getComputedStyle(elementFont.element)["font-family"] !=
          elementFont.fontFamily
        ) {
          this.removeFont(elementFont.provider, elementFont.fontFamily);
        }
      }
    }
  },
};

// Toggle fullscreen
function launchFullScreen(document) {
  if (document.documentElement.requestFullScreen) {
    if (document.FullScreenElement) document.exitFullScreen();
    else document.documentElement.requestFullScreen();
    //mozilla
  } else if (document.documentElement.mozRequestFullScreen) {
    if (document.mozFullScreenElement) document.mozCancelFullScreen();
    else document.documentElement.mozRequestFullScreen();
    //webkit
  } else if (document.documentElement.webkitRequestFullScreen) {
    if (document.webkitFullscreenElement) document.webkitExitFullscreen();
    else document.documentElement.webkitRequestFullScreen();
    //ie
  } else if (document.documentElement.msRequestFullscreen) {
    if (document.msFullScreenElement) document.msExitFullscreen();
    else document.documentElement.msRequestFullscreen();
  }
}

let fontList = [
  {
    value: "",
    text: "Default",
  },
  {
    value: "Arial, Helvetica, sans-serif",
    text: "Arial",
  },
  {
    value: "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
    text: "Lucida Grande",
  },
  {
    value: "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
    text: "Palatino Linotype",
  },
  {
    value: "'Times New Roman', Times, serif",
    text: "Times New Roman",
  },
  {
    value: "Georgia, serif",
    text: "Georgia, serif",
  },
  {
    value: "Tahoma, Geneva, sans-serif",
    text: "Tahoma",
  },
  {
    value: "'Comic Sans MS', cursive, sans-serif",
    text: "Comic Sans",
  },
  {
    value: "Verdana, Geneva, sans-serif",
    text: "Verdana",
  },
  {
    value: "Impact, Charcoal, sans-serif",
    text: "Impact",
  },
  {
    value: "'Arial Black', Gadget, sans-serif",
    text: "Arial Black",
  },
  {
    value: "'Trebuchet MS', Helvetica, sans-serif",
    text: "Trebuchet",
  },
  {
    value: "'Courier New', Courier, monospace",
    text: "Courier New",
  },
  {
    value: "'Brush Script MT', sans-serif",
    text: "Brush Script",
  },
];
