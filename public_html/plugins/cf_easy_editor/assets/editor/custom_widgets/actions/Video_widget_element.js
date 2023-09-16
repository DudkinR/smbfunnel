class Video_widget_element extends SuperElement {
  name() {
    return "Advanced Video";
  }
  getControls() {
    var element = this;

    return [
      new (class extends Control {
        constructor(...args) {
          super(...args);
          this.controllerKeys = [
            "data-video-provider",
            "data-video-id",
            "data-video-url",
            "data-video-height",
            "data-video-width",
            "data-video-autoplay",
            "data-video-controls",
            "data-video-loop",
          ];
        }
        renderHtml() {
          var thisControl = this;
          var html = /*html*/ `
                <div id="ProductListControl">
                    <div class="control-[ID]">
                        <div class="widget-section d-flex align-items-center pr-3">
                            <div class="label mr-auto">Video type</div>
                            <select class="form-control" style="width:200px;" data-video-provider>
                                <option value="y">Youtube</option>
                                <option value="v">Vimeo</option>
                                <option value="h">HTML</option>
                            </select>
                        </div>
                        <div class="widget-section d-flex align-items-center pr-3 data-video-id">
                            <div class="label mr-auto">Video Id</div>
                            <input type="text" style="width:200px" data-video-id>
                        </div>
                        <div class="widget-section d-flex align-items-center pr-3 data-video-url" style="display:none !important">
                            <div class="label mr-auto">Video URL</div>
                            <input type="url" style="width:200px" data-video-url>
                        </div>
                        <div class="widget-section d-flex align-items-center pr-3">
                            <div class="label mr-auto">Height (Value in PX or %)</div>
                            <input type="text" style="width:200px" data-video-height>
                        </div>
                        <div class="widget-section d-flex align-items-center pr-3">
                            <div class="label mr-auto">width (Value in PX or %)</div>
                            <input type="text" style="width:200px" data-video-width>
                        </div>
                        <div class="widget-section d-flex align-items-center pr-3">
                            <div class="label mr-auto">Autoplay&nbsp;&nbsp;</div>
                            <input type="checkbox" data-video-autoplay>
                        </div>
                        <div class="widget-section d-flex align-items-center pr-3">
                            <div class="label mr-auto">Controls&nbsp;&nbsp;</div>
                            <input type="checkbox" data-video-controls>
                        </div>
                        <div class="widget-section d-flex align-items-center pr-3">
                            <div class="label mr-auto">Loop&nbsp;&nbsp;</div>
                            <input type="checkbox" data-video-loop>
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
          var thisControl = this;
          let selector = thisControl.selector;
          this.controllerKeys.forEach((attr) => {
            try {
              let val = element.obj.find(`[data-component-video]`).attr(attr);
              if (val !== undefined) {
                let el = $(selector).find(`[${attr}]`);
                if (el.attr("type") === "checkbox") {
                  el.checked = val === "true" || val === true ? true : false;
                } else {
                  el.val(val);
                }
              }
            } catch (err) {
              console.log(err);
            }
          });
        }
        afterRender() {
          let thisControl = this;
          let selector = thisControl.selector;

          // when something changed
          this.controllerKeys.forEach((attr) => {
            try {
              let input = $(selector).find(`[${attr}]`);
              $(input).on(`change keyup`, (e) => {
                let vid = $("<div></div>");
                vid.attr("data-component-video", "");
                vid.css("padding", "6px");

                let vidType;
                let info = {};

                this.controllerKeys.forEach((attr) => {
                  let input = $(selector).find(`[${attr}]`);
                  let val;
                  if (input.attr("type") === "checkbox") {
                    val = input.checked ? true : false;
                  } else {
                    val = input.val();
                  }

                  if (attr === "data-video-provider") {
                    vidType = val;
                    if (val === "h") {
                      $(selector)
                        .find(".data-video-url")
                        .attr("style", "display: flex !important");
                      $(selector)
                        .find(".data-video-id")
                        .attr("style", "display: none !important");
                    } else {
                      $(selector)
                        .find(".data-video-url")
                        .attr("style", "display: none !important");
                      $(selector)
                        .find(".data-video-id")
                        .attr("style", "display: flex !important");
                    }
                  }
                  vid.attr(attr, val);
                  info[attr.replace("data-video-", "")] = val;
                });

                console.log(info);
                let vidElement;

                if (vidType === "y") {
                  vidElement = `<div style="overflow:hidden;position:relative;height:0;padding-bottom:56.25%;"><iframe src="https://www.youtube.com/embed/${info.id}?&amp;autoplay=${info.autoplay}&amp;controls=${info.controls}&amp;loop=${info.loop}" allowfullscreen="true" style="top:0; left:0; height: 100%; width: 100%; position: absolute;" frameborder="0"></iframe></div>`;
                } else if (vidType === "v") {
                  vidElement = `<div style="overflow:hidden;position:relative;height:0;padding-bottom:56.25%;"><iframe src="https://player.vimeo.com/video/${info.id}?&amp;autoplay=${info.autoplay}&amp;controls=${info.controls}&amp;loop=${info.loop}" allowfullscreen="true" style="top:0; left:0; height: 100%; width: 100%; position: absolute;" frameborder="0"></iframe></div>`;
                } else {
                  vidElement = `<div style="width: ${info.width}; height: ${
                    info.height
                  }; overflow: hidden;"><video src="${info.url}"${
                    info.controls ? " controls" : ""
                  }${
                    info.loop ? "loop" : ""
                  } style="width: 100%;"></video></div>`;
                }

                $(vid).html(vidElement);
                element.obj.find(`[data-component-video]`).replaceWith(vid);
              });
            } catch (err) {
              console.log(err);
            }
          });
          $(thisControl.selector)
            .find("[name=button-text]")
            .on("change keyup", function (e) {
              thisControl.callback($(this).val());
            });
          // get values
          thisControl.getValues();
        }
      })("Change Text", element.obj.find("button").html(), (text) => {
        console.log("Text changed");
      }),
      ...Common_controls.getControls(element),
    ];
  }
}
