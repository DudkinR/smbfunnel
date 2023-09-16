function CFProofConvertMangeInputs(){
  this.current_inp_count=0;
  this.createINP=function(name='',email='',address=''){
    ++this.current_inp_count;
    let inp=document.createElement('div');
    inp.setAttribute('lvl',this.current_inp_count);
    inp.classList.add('lvl-container');
    inp.classList.add('mb-2');
    let inp_html=`
      <div class="row">
        <div class="col-lg-11">
          <div class="row">
            <div class="col-md-4">
              <input type="text"  class="form-control inpname" value="${name}">
            </div>
            <div class="col-md-4">
              <input type="text"  class="form-control inpemail" value="${email}">
            </div>
            <div class="col-md-4">
              <input type="text"  class="form-control inpaddress" value="${address}">
            </div>
          </div>
        </div>
          <div class="col-lg-1">
          <button class="btn btn-danger cfproofconvertdelinp">Delete</button>
          </div>
      </div>`;
        
      inp.innerHTML=inp_html;
      let main_container=document.querySelectorAll("#cfproof_convert_fake_container")[0];
      main_container.appendChild(inp);
      main_container.scrollTop=main_container.scrollHeight;    
      let _this=this;
      let inp_close=inp.querySelectorAll('.cfproofconvertdelinp')[0];
      inp_close.inp=inp;
      inp_close.addEventListener('click',function(){
      let inp=this.inp;
      let doc=document.querySelectorAll(`#cfproof_convert_fake_container`)[0];
      doc.removeChild(inp);
      let count=0;
      doc.querySelectorAll("div[lvl]").forEach(lvl=>{
      ++count;
      lvl.setAttribute('lvl',count);
    });
  });
};
  this.getInputs=function(){
    let docs=document.querySelectorAll("#cfproof_convert_fake_container div[lvl]");
    let inputs=[];
    docs.forEach((item, index)=>{
    let lvl=index+1;
    let getVal=function(cls){
      return item.querySelectorAll(`.${cls}`)[0].value;
    };    
    let name=getVal('inpname');
    let email=getVal('inpemail');
    let address=getVal('inpaddress');
      inputs.push({name, email, address});
    });
    return JSON.stringify(inputs);
  };
}

