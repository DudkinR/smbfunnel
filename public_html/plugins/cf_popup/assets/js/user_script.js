function cfrespoDoLoadUserSideScript(fid,frmid, exit_popup,delayed_popup, btn_click, delay_time, force_show = false, form_width_data) {

  let popupFormModal = document.getElementById(`cfrespo-modal_${fid}`);
  let close_btn = document.getElementById(`cfrespo-modal-close-${fid}`);
  let screen_width_css = `.cfrespo-modal-${fid} .modal-dialog`;
  let style=document.getElementById('width_css_style');
  
  $(window).resize(function() {
    if(form_width_data > screen.width) {
      style.innerHTML = "\n@media screen and (max-width:"+screen.width+"px) { "+screen_width_css+" { max-width: 100% !important; } }";
    }
  });

  if(force_show) { 
    let bdy=popupFormModal.querySelectorAll('.cfrespo-modal-body')[0];
    bdy.scrollTop= bdy.scrollHeight;

    popupFormModal.classList.add('show');
    popupFormModal.style.display="block";
  }
  
  close_btn.onclick = function() {
    popupFormModal.classList.remove('show');
    popupFormModal.style.display="none";
  }

  window.onclick = function(event) {
    if (event.target == popupFormModal) {
      popupFormModal.classList.remove('show');
      popupFormModal.style.display="none";
    }
  };

  // delayed popup
  if( delayed_popup == '1' ) {
    setTimeout( function(){
      popupFormModal.classList.add('show');
      popupFormModal.style.display="block";
    }, delay_time);

  }
  //exit popup
  if( exit_popup == '1' ) {
    document.addEventListener('mouseleave', function(){
      popupFormModal.classList.add('show');
      popupFormModal.style.display="block";

    });
  }

  // onclick popup
  if( btn_click == '1' ) {
    // event on hash change
    window.addEventListener("hashchange", function(eve)
    {
        eve.preventDefault();
        let hass=this.window.location.hash;
        let dd = hass.split("_");
        let frid = dd[dd.length-1];
        let md = document.querySelector(`.cfrespo-modelno-${frid}`);
        md.classList.add('show');
        md.style.display="block";
        const curl =  new URL(window.location);
        this.window.history.replaceState(null, null, ' ');
    });
  }
}