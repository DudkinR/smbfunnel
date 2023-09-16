  function CFGlobalMangeInputs(){
    this.current_inp_count=0;
    this.createINP=function(name='',title='',custom=0){
      ++this.current_inp_count;
      let inp=document.createElement('div');
      inp.setAttribute('lvl',this.current_inp_count);
      inp.classList.add('lvl-container');
      inp.classList.add('mb-2');
      custom=parseInt(custom);

      let inp_html=`<div class="row">
          <div class="col-12 text-right"><i class="fas fa-times-circle delinp"></i></div>
          <div class="col pdr-1">
          <input type="text" class="form-control inpname" placeholder="Enter Input Name" required value="${name}">
          </div>
          <div class="col pdl-0">
          <input type="text" class="form-control ttl" placeholder="${'Enter Value'}" required value="${title}">
          </div>
        </div>

        <div class="row mt-1">
          <div class="col pdr-1">
            <select class="form-control stat">
              <option value=0${(custom)? ` selected`:``}>Default</option>
              <option value=1${(custom)? ` selected`:``}>Custom</option>
            </select>
            </div>
          </div>
        <div class="row updowncontainer" style="display:none;">
          <div class="col-sm-12"><div class="cfext-switch"><center><button class="btn unstyled-button extinp-up"><i class="fas fa-chevron-up"></i></button><button class="btn unstyled-button extinp-down"><i class="fas fa-chevron-down"></i></button></center></div></div>
        </div>
        `;
        inp.innerHTML=inp_html;
        
        let main_container=document.querySelectorAll("#cfglobal_input_container")[0];
        main_container.appendChild(inp);
        main_container.scrollTop=main_container.scrollHeight;
        
        let _this=this;

        setTimeout( function(){
              let arr=[
                  inp.querySelectorAll('input.inpname')[0],
                  inp.querySelectorAll('select.stat')[0]
              ];

              let ttl=inp.querySelectorAll('input.ttl')[0];
                arr.forEach(doc=>{doc.disabled=false;});
                ttl.placeholder="Enter Value";
            }, 200 );

        inp.addEventListener('click',function(){
          document.querySelectorAll("#cfglobal_input_container div[lvl]").forEach(doc=>{
            doc.classList.remove('selected_input');
          });
          //console.log(this);
          this.classList.add('selected_input');
        });

        inp.addEventListener('mousemove',function(){
          let doc=this;
          //console.log(doc);
          doc.querySelectorAll(".updowncontainer")[0].style.display="block";
        });
        
        inp.addEventListener('mouseout',function(){
          let doc=this;
          doc.querySelectorAll(".updowncontainer")[0].style.display="none";
        });
        let inp_up=inp.querySelectorAll('.extinp-up')[0];
        inp_up.inp=inp;
        inp_up.addEventListener('click',function(eve){
          eve.preventDefault();
          let inp=this.inp;
          let current_lvl=parseInt(inp.getAttribute('lvl'));
          if(current_lvl>1)
          {
            let up_doc=document.querySelectorAll(`#cfglobal_input_container div[lvl="${current_lvl-1}"`)[0];
            //console.log(up_doc);
            document.querySelectorAll(`#cfglobal_input_container`)[0].insertBefore(inp,up_doc);
            inp.setAttribute('lvl',(current_lvl-1));
            up_doc.setAttribute('lvl',current_lvl);
          }
        });

        let inp_down=inp.querySelectorAll('.extinp-down')[0];
        inp_down.inp=inp;
        inp_down.addEventListener('click',function(eve){
          eve.preventDefault();
          let inp=this.inp;
          current_inp_count=_this.current_inp_count;
          let current_lvl=parseInt(inp.getAttribute('lvl'));
          if(current_lvl<current_inp_count)
          {
            let down_doc=document.querySelectorAll(`#cfglobal_input_container div[lvl="${current_lvl+1}"`)[0];
            document.querySelectorAll(`#cfglobal_input_container`)[0].insertBefore(down_doc,inp);
            down_doc.setAttribute('lvl',current_lvl);
            inp.setAttribute('lvl',(current_lvl+1))
          }
        });

        let inp_close=inp.querySelectorAll('.delinp')[0];
        inp_close.inp=inp;
        inp_close.addEventListener('click',function(){
          let inp=this.inp;
          let doc=document.querySelectorAll(`#cfglobal_input_container`)[0];
          doc.removeChild(inp);
          let count=0;
          doc.querySelectorAll("div[lvl]").forEach(lvl=>{
            ++count;
            lvl.setAttribute('lvl',count);
          });
        });

        };
    this.getInputs=function(){
      let docs=document.querySelectorAll("#cfglobal_input_container div[lvl]");
      let inputs=[];
      docs.forEach((item, index)=>{
        let getVal=function(cls){
          return item.querySelectorAll(`.${cls}`)[0].value;
        };
        
        let title=getVal('ttl');
        let name=getVal('inpname');
        let custom=getVal('stat');
        inputs.push({name, title, name, custom});
      });
      return JSON.stringify(inputs);
    };
  }

  function CFGlobalMangeHeaderInputs(){
    this.current_inp_count=0;
    this.createINP=function(name='',title=''){
      ++this.current_inp_count;
      let inp=document.createElement('div');
      inp.setAttribute('lvl',this.current_inp_count);
      inp.classList.add('lvl-container');
      inp.classList.add('mb-2');

      let inp_html=`<div class="row">
          <div class="col-12 text-right"><i class="fas fa-times-circle delinp"></i></div>
          <div class="col pdr-1">
          <input type="text" class="form-control inpname" placeholder="Enter Input Name" required value="${name}">
          </div>
          <div class="col pdl-0">
          <input type="text" class="form-control ttl" placeholder="${'Enter Value'}" required value="${title}">
          </div>
        </div>
        <div class="row updowncontainer" style="display:none;">
          <div class="col-sm-12"><div class="cfext-switch"><center><button class="btn unstyled-button extinp-up"><i class="fas fa-chevron-up"></i></button><button class="btn unstyled-button extinp-down"><i class="fas fa-chevron-down"></i></button></center></div></div>
        </div>
        `;
        inp.innerHTML=inp_html;
        
        let main_container=document.querySelectorAll("#cfglobal_input_header_container")[0];
        main_container.appendChild(inp);
        main_container.scrollTop=main_container.scrollHeight;
        
        let _this=this;

        setTimeout( function(){
              let arr=[
                  inp.querySelectorAll('input.inpname')[0]
              ];

              let ttl=inp.querySelectorAll('input.ttl')[0];
                arr.forEach(doc=>{doc.disabled=false;});
                ttl.placeholder="Enter Value";
            }, 200 );

        inp.addEventListener('click',function(){
          document.querySelectorAll("#cfglobal_input_header_container div[lvl]").forEach(doc=>{
            doc.classList.remove('selected_input');
          });
          //console.log(this);
          this.classList.add('selected_input');
        });

        inp.addEventListener('mousemove',function(){
          let doc=this;
          //console.log(doc);
          doc.querySelectorAll(".updowncontainer")[0].style.display="block";
        });
        
        inp.addEventListener('mouseout',function(){
          let doc=this;
          doc.querySelectorAll(".updowncontainer")[0].style.display="none";
        });
        let inp_up=inp.querySelectorAll('.extinp-up')[0];
        inp_up.inp=inp;
        inp_up.addEventListener('click',function(eve){
          eve.preventDefault();
          let inp=this.inp;
          let current_lvl=parseInt(inp.getAttribute('lvl'));
          if(current_lvl>1)
          {
            let up_doc=document.querySelectorAll(`#cfglobal_input_header_container div[lvl="${current_lvl-1}"`)[0];
            //console.log(up_doc);
            document.querySelectorAll(`#cfglobal_input_header_container`)[0].insertBefore(inp,up_doc);
            inp.setAttribute('lvl',(current_lvl-1));
            up_doc.setAttribute('lvl',current_lvl);
          }
        });

        let inp_down=inp.querySelectorAll('.extinp-down')[0];
        inp_down.inp=inp;
        inp_down.addEventListener('click',function(eve){
          eve.preventDefault();
          let inp=this.inp;
          current_inp_count=_this.current_inp_count;
          let current_lvl=parseInt(inp.getAttribute('lvl'));
          if(current_lvl<current_inp_count)
          {
            let down_doc=document.querySelectorAll(`#cfglobal_input_header_container div[lvl="${current_lvl+1}"`)[0];
            document.querySelectorAll(`#cfglobal_input_header_container`)[0].insertBefore(down_doc,inp);
            down_doc.setAttribute('lvl',current_lvl);
            inp.setAttribute('lvl',(current_lvl+1))
          }
        });

        let inp_close=inp.querySelectorAll('.delinp')[0];
        inp_close.inp=inp;
        inp_close.addEventListener('click',function(){
          let inp=this.inp;
          let doc=document.querySelectorAll(`#cfglobal_input_header_container`)[0];
          doc.removeChild(inp);
          let count=0;
          doc.querySelectorAll("div[lvl]").forEach(lvl=>{
            ++count;
            lvl.setAttribute('lvl',count);
          });
        });

        };
    this.getInputs=function(){
      let docs=document.querySelectorAll("#cfglobal_input_header_container div[lvl]");
      let inputs=[];
      docs.forEach((item, index)=>{
        let getVal=function(cls){
          return item.querySelectorAll(`.${cls}`)[0].value;
        };
        
        let title=getVal('ttl');
        let name=getVal('inpname');
        inputs.push({name, title});
      });
      return JSON.stringify(inputs);
    };
  }