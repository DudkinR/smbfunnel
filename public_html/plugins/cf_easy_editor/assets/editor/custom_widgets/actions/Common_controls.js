class Common_controls {
  static getControls(e) {
    return [
      new CellOptionControl(
        getI18n("cell_options"),
        {
          count: e.obj.children().length,
          layout: e.obj.attr("data-layout"),
        },
        {
          setLayout: function (t) {
            e.setLayout(t),
              setTimeout(function () {
                currentEditor.select(e), currentEditor.handleSelect();
              }, 300);
          },
        }
      ),
      new FontFamilyControl(
        getI18n("font_family"),
        e.obj.css("font-family"),
        function (t) {
          e.obj.css("font-family", t), e.select();
        }
      ),
      new BackgroundImageControl(
        getI18n("background_image"),
        {
          image: e.obj.css("background-image"),
          color: e.obj.css("background-color"),
          repeat: e.obj.css("background-repeat"),
          position: e.obj.css("background-position"),
          size: e.obj.css("background-size"),
        },
        {
          setBackgroundImage: function (t) {
            e.isWrapper()
              ? e.obj.closest("body").css("background-image", t)
              : e.obj.css("background-image", t);
          },
          setBackgroundColor: function (t) {
            e.isWrapper()
              ? e.obj.closest("body").css("background-color", t)
              : e.obj.css("background-color", t);
          },
          setBackgroundRepeat: function (t) {
            e.isWrapper()
              ? e.obj.closest("body").css("background-repeat", t)
              : e.obj.css("background-repeat", t);
          },
          setBackgroundPosition: function (t) {
            e.isWrapper()
              ? e.obj.closest("body").css("background-position", t)
              : e.obj.css("background-position", t);
          },
          setBackgroundSize: function (t) {
            e.isWrapper()
              ? e.obj.closest("body").css("background-size", t)
              : e.obj.css("background-size", t);
          },
        }
      ),
      new BlockOptionControl(
        getI18n("padding"),
        {
          padding: e.obj.css("padding"),
          top: e.obj.css("padding-top"),
          bottom: e.obj.css("padding-bottom"),
          right: e.obj.css("padding-right"),
          left: e.obj.css("padding-left"),
        },
        function (t) {
          e.obj.css("padding", t.padding),
            e.obj.css("padding-top", t.top),
            e.obj.css("padding-bottom", t.bottom),
            e.obj.css("padding-right", t.right),
            e.obj.css("padding-left", t.left),
            e.select();
        }
      ),
    ];
  }
  static getCommonIdfs(element) {
    return new (class extends Control {
      constructor(...args) {
        super(...args);
        this.attrs = ["id", "class", "type", "name", "placeholder"];
        this.inpTags = ["input", "textarea", "select", "button"];
      }
      renderHtml() {
        let nameOptions = [];
        if (
          Array.isArray(cf_global_page_inputs) &&
          cf_global_page_inputs.length > 0
        ) {
          nameOptions = cf_global_page_inputs.map((inp) => {
            return `<option value="${inp}">${inp}</option>`;
          });
        }
        nameOptions = nameOptions.join("");

        var thisControl = this;
        var html = /*html*/ `
        <div id="ProductListControl">
          <div class="control-[ID]">
            <div class="widget-section d-flex align-items-center pr-3" data-el-id>
              <div class="label mr-auto">Id</div>
              <input type="text" class="input" style="width:200px" name="id" value=""/>
            </div>
            <div class="widget-section d-flex align-items-center pr-3" data-el-class>
              <div class="label mr-auto">Class</div>
              <input type="text" class="input" style="width:200px" name="class" value=""/>
            </div>
            <div class="widget-section d-flex align-items-center pr-3" data-el-type>
              <div class="label mr-auto">Type</div>
              <input type="text" class="input" style="width:200px" name="type" value=""/>
            </div>
            <div class="widget-section d-flex align-items-center pr-3" data-el-name>
              <div class="label mr-auto">Name</div>
              <select class="form-control input" name="name" style="width: 200px">
                <option class="input" value="@ednothingselected@">Select name</option>
                ${nameOptions}
              </select>
            </div>
            <div class="widget-section d-flex align-items-center pr-3" data-el-placeholder>
              <div class="label mr-auto">Placeholder</div>
              <input class="input" type="text" name="placeholder" style="width:200px" value=""/>
            </div>
          </div>
        </div>
        `;

        thisControl.selector = ".control-" + thisControl.id;
        html = html.replace("[ID]", thisControl.id);
        html = html.replace("[TITLE]", thisControl.title);
        var div = $("<DIV>").html(html);

        return div.html();
      }
      getValues() {
        let _this = this;
        var thisControl = this;

        let inp = $(element.obj);
        let tagName = inp[0].nodeName.toLowerCase();

        let found = false;
        if (!_this.inpTags.includes(tagName.toLowerCase())) {
          let _inp = inp;
          for (let i in _this.inpTags) {
            let tag = _this.inpTags[i];
            inp = $(_inp).children(tag);
            if (inp.length > 0) {
              break;
            }
          }
        }

        if (inp.length > 0) {
          found = true;
        }

        let el;
        if (found) {
          el = inp;
        } else {
          $(thisControl.selector)
            .find("[data-el-name]")
            .attr("style", "display:none !important;");
          $(thisControl.selector)
            .find("[data-el-placeholder]")
            .attr("style", "display:none !important");
          el = $(element.obj);
        }

        try {
          if (!["button", "input"].includes(el[0].nodeName.toLowerCase())) {
            $(thisControl.selector)
              .find("[data-el-type]")
              .attr("style", "display:none !important;");
          }
        } catch (err) {
          console.log(el);
          console.log(err);
        }

        this.attrs.forEach((attr) => {
          if (el.attr(attr) !== undefined) {
            let val = el.attr(attr);
            $(thisControl.selector).find(`[data-el-${attr}] .input`).val(val);
          }
        });
      }
      afterRender() {
        var thisControl = this;
        let selectors = $(thisControl.selector).find(".input");

        let _this = this;
        selectors.on("change keyup input", function () {
          let type = $(this).attr("name");
          let val = $(this).val();

          let added = false;
          //if (["name", "placeholder"].includes(type)) {
          let inp = $(element.obj);
          let tagName = inp[0].nodeName.toLowerCase();

          if (!_this.inpTags.includes(tagName.toLowerCase())) {
            let _inp = inp;
            for (let i in _this.inpTags) {
              let tag = _this.inpTags[i];
              inp = $(_inp).children(tag);
              if (inp.length > 0) {
                break;
              }
            }
          }

          if (inp.length > 0) {
            $(inp[0]).attr(type, val);
            added = true;
          }
          //}

          if (!added) {
            $(element.obj).attr(type, val);
          }
        });
        thisControl.getValues();
      }
    })();
  }
}
