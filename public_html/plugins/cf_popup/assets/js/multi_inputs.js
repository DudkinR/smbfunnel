  function CFRESpoMangeInputs(){
    this.non_inputs=['p','h1','h2','h3','h4','h5','h6'];
    this.current_inp_count=0;
    this.createINP=function(name='',placeholder='',title='',required=1, type='text'){
      ++this.current_inp_count;
      let inp=document.createElement('div');
      inp.setAttribute('lvl',this.current_inp_count);
      inp.classList.add('lvl-container');
      inp.classList.add('mb-2');
      required=parseInt(required);

      let inp_html=`<div class="row">
          <div class="col-12 text-right"><i class="fas fa-times-circle delinp"></i></div>
          <div class="col pdr-0">
          <input type="text" class="form-control plc" placeholder="Enter Placeholder" value="${placeholder}">
          </div>
          <div class="col pdl-0">
          <input type="text" class="form-control ttl" placeholder="${(this.non_inputs.indexOf(type)>-1)? 'Enter Text':'Enter Title'}" value="${title}">
          </div>
        </div>

        <div class="row mt-1">
        <div class="col pdr-0">
            <select class="form-control type">
              <option value="text"${(type==='text')? ` selected`:``}>Text</option>
              <option value="email"${(type==='email')? ` selected`:``}>Email</option>
              <option value="url"${(type==='url')? ` selected`:``}>URL</option>
              <option value="number"${(type==='number')? ` selected`:``}>Number</option>
              <option value="textarea"${(type==='textarea')? ` selected`:``}>Text Area</option>
              <option value="checkbox"${(type==='checkbox')? ` selected`:``}>Check Box</option>
              <option value="radio"${(type==='radio')? ` selected`:``}>Radio button</option>
              <option value="p"${(type==='p')? ` selected`:``}>Paragraph</option>
              <option value="h1"${(type==='h1')? ` selected`:``}>Heading-1</option>
              <option value="h2"${(type==='h2')? ` selected`:``}>Heading-2</option>
              <option value="h3"${(type==='h3')? ` selected`:``}>Heading-3</option>
              <option value="h4"${(type==='h4')? ` selected`:``}>Heading-4</option>
              <option value="h5"${(type==='h5')? ` selected`:``}>Heading-5</option>
              <option value="h6"${(type==='h6')? ` selected`:``}>Heading-6</option>
            </select>
          </div>
          <div class="col pdr-0 pdl-0">
            <input type="text" class="form-control inpname" placeholder="Enter Input Name" value="${name}">
          </div>
          <div class="col pdl-0">
            <select class="form-control stat">
              <option value=1${(required)? ` selected`:``}>Required</option>
              <option value=0${(!required)? ` selected`:``}>Optional</option>
            </select>
          </div>
        </div>
        <div class="row updowncontainer" style="display:none;">
          <div class="col-sm-12"><div class="cfext-switch"><center><button class="btn unstyled-button extinp-up"><i class="fas fa-chevron-up"></i></button><button class="btn unstyled-button extinp-down"><i class="fas fa-chevron-down"></i></button></center></div></div>
        </div>
        `;
        inp.innerHTML=inp_html;
        
        let main_container=document.querySelectorAll("#cfrespo_input_container")[0];
        main_container.appendChild(inp);
        main_container.scrollTop=main_container.scrollHeight;
        
        let _this=this;

        setTimeout((inp)=>{
          inp.querySelectorAll("select.type")[0].onchange=(e)=>{
            let el=e.target;
            let arr=[
              inp.querySelectorAll('input.plc')[0],
              inp.querySelectorAll('input.inpname')[0],
              inp.querySelectorAll('select.stat')[0]
            ];
            let ttl=inp.querySelectorAll('input.ttl')[0];
            if(this.non_inputs.indexOf(el.value)>-1)
            {
              arr.forEach(doc=>{doc.disabled=true;});
              ttl.placeholder="Enter Text";
            }
            else
            {
              arr.forEach(doc=>{doc.disabled=false;});
              ttl.placeholder="Enter Title";
            }
          };
        },200, inp);

        setTimeout( function(){
              let arr=[
                  inp.querySelectorAll('input.plc')[0],
                  inp.querySelectorAll('input.inpname')[0],
                  inp.querySelectorAll('select.stat')[0]
              ];

              let ttl=inp.querySelectorAll('input.ttl')[0];
              if(type=="p" || type=="h1" || type=="h2" || type=="h3" || type=="h4" || type=="h5" || type=="h6" )
              {

                arr.forEach(doc=>{doc.disabled=true;});
                ttl.placeholder="Enter Text";
              }
              else
              {
                arr.forEach(doc=>{doc.disabled=false;});
                ttl.placeholder="Enter Title";
              }

            }, 200 );

        inp.addEventListener('click',function(){
          document.querySelectorAll("#cfrespo_input_container div[lvl]").forEach(doc=>{
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
            let up_doc=document.querySelectorAll(`#cfrespo_input_container div[lvl="${current_lvl-1}"`)[0];
            //console.log(up_doc);
            document.querySelectorAll(`#cfrespo_input_container`)[0].insertBefore(inp,up_doc);
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
            let down_doc=document.querySelectorAll(`#cfrespo_input_container div[lvl="${current_lvl+1}"`)[0];
            document.querySelectorAll(`#cfrespo_input_container`)[0].insertBefore(down_doc,inp);
            down_doc.setAttribute('lvl',current_lvl);
            inp.setAttribute('lvl',(current_lvl+1))
          }
        });

        let inp_close=inp.querySelectorAll('.delinp')[0];
        inp_close.inp=inp;
        inp_close.addEventListener('click',function(){
          let inp=this.inp;
          let doc=document.querySelectorAll(`#cfrespo_input_container`)[0];
          doc.removeChild(inp);
          let count=0;
          doc.querySelectorAll("div[lvl]").forEach(lvl=>{
            ++count;
            lvl.setAttribute('lvl',count);
          });
        });

        };
    this.getInputs=function(){
      let docs=document.querySelectorAll("#cfrespo_input_container div[lvl]");
      let inputs=[];
      docs.forEach((item, index)=>{
        let lvl=index+1;
        let getVal=function(cls){
          return item.querySelectorAll(`.${cls}`)[0].value;
        };
        
        let placeholder=getVal('plc');
        let title=getVal('ttl');
        let type=getVal('type');
        let name=getVal('inpname');
        let required=getVal('stat');
        inputs.push({name, placeholder, title, type, name, required});
      });
      return JSON.stringify(inputs);
    };
  }

