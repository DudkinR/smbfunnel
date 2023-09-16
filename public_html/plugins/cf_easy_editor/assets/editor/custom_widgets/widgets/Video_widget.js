// Footer widget
class Video_widget extends Widget {
  getHtmlId() {
    return this.constructor.name;
  }

  init() {
    // default button html
    this.setButtonHtml(this.generateButtonHtml());

    // default content html
    this.setContentHtml(this.generateContentHtml());

    // default dragging html
    this.setDraggingHtml(this.getButtonHtml());
  }

  generateButtonHtml() {
    let html = `<div class="_1content widget-button">
        <div class="panel__body" title="{language.wpanel.widgets.title.title}">
        <div class="image-drag">
        <div class="ng-binding">
        <i class="fas fa-video"></i>
        </div>
        </div>
        <div class="body__title body__title--cs ng-binding" style="max-width: 100%; overflow">Video</div>
        </div>
        </div>`;
    return html;
  }
  generateContentHtml() {
    let html = /*html*/ `<div builder-element="Video_widget_element" builder-draggable class="px-3 my-3">
      <div data-component-video style="min-height:240px;min-width:240px;position:relative;padding:6px;" data-video-url="" data-video-id="-stFvGmg1A8" data-video-height="auto" data-video-width="100%"><iframe frameborder="0" src="https://www.youtube.com/embed/-stFvGmg1A8" style="width:100%;height:100%;position:absolute;left:0px;pointer-events:none"></iframe></div>
    </div>
    `;
    return html;
  }
}
