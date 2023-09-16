Vvveb.BlocksGroup['Custom Blocks']= [];
let global_custom_templates= {};
var plotted_custom_inputs= false;

const editorDoSetBlock= function(id= 0, data= {}){
    //name dragHtml image html
    let request= cf_request;
    global_custom_templates[id]= {...data};
    data.id= id;
    request.postRequestCb('req.php', {editor_block: 1, set: 1, block: JSON.stringify(data) }, async function(data){
        //track_block_save
        //Vvveb.Gui.saveAjax()
        if(confirm("Save the page and reload to reflect blocks immediately?"))
        {
            track_block_save= true;
            Vvveb.Gui.saveAjax();
        }
    });
};

const editorDoDelBlock= function(id){
    let request= cf_request;
    request.postRequestCb('req.php', {editor_block: 1, delete: 1, block_id: id}, function(data){
        if(confirm("Save the page and reload to reflect blocks immediately?"))
        {
            track_block_save= true;
            Vvveb.Gui.saveAjax();
        }
    });
};

const createOptionsAndBlocks= function(){
    let blocks= global_custom_templates;
    let block_paths= [];
    global_custom_blocks_options= [{value: "", text: t("Create New Block")}];
    try
    {
        Vvveb.BlocksGroup['Custom Blocks']= [];
        for(let i in blocks)
        {
            Vvveb.Blocks.add(i, blocks[i]);
            block_paths.push(i);
            global_custom_blocks_options.push({value: i, text: blocks[i].name});    
        }
        Vvveb.BlocksGroup['Custom Blocks']= block_paths;

    }catch(err)
    {console.log(err);}
};

const doPlotCustomInputs= function(){
    setInterval(function(){
        try
        {
            let doc= $(`[data-key="element_custom_block_select"] select`);
            let options= $(`[data-key="element_custom_block_select"] select option`);
            if(doc.length>0 && global_custom_blocks_options.length !==options.length)
            {
                let selections= [];
                global_custom_blocks_options.forEach(s=>{
                    selections.push(`<option value="${s.value}">${s.text}</option>`);
                });

                $(`[data-key="element_custom_block_select"] select`).html(selections.join(''));
                plotted_custom_inputs= true;
            }
        }catch(err){console.log(err);}
    }, 1000);
};

const loadSavedBlocks= function(cb= false){
    return new Promise((resolve, reject)=>{
        try
        {
            let request= cf_request;
            let fields= {editor_block: 1, get: 1};
            request.postRequestCb('req.php', fields, function(data){
                try
                {
                    data= JSON.parse(data.trim());
                    if(typeof(data)==='object' && Object.keys(data).length>0)
                    {
                        let blocks= data;
                        global_custom_templates= blocks;
                        createOptionsAndBlocks();
                    }
                }catch(err){ console.log(err); }
                if(typeof(cb)==='function'){cb(true);}
                resolve(true);
            });
        }catch(err)
        {
            console.log(err);
            if(typeof(cb)==='function'){cb(false);}
            resolve(false);
        }
    });
};

(async function(){
    let loaded= await loadSavedBlocks();
    doPlotCustomInputs();
})()