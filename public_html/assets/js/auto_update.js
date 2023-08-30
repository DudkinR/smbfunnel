function qfnlAutoUpdate()
{
    this.request=new ajaxRequest();
    this.downloadurl=0;
    this.version=0;
    this.changes="";
    this.container=0;
    this.is_gcp=0;
    this.init=function(){
        var thiss=this;
        thiss.request.postRequestCb('req.php',{"checkforqfnl_update":"install_dependency"},function(data){
            if(data.trim()==1)
            {
                thiss.checkForUpdate();
            }
            else
            {
                console.log(data);
            }
            });
    };
    this.checkForUpdate=function(){
        try{
            var thiss=this;
            this.request.postRequestCb('req.php',{"checkforqfnl_update":"check"},function(data){
                //console.log(data);
                data=data.trim();
            if(data !="0")
            {
            try
            {
                data=JSON.parse(data.trim());
                thiss.downloadurl=data.download_url;
                thiss.version=data.version;
                thiss.changes=data.changes;
                thiss.showUpdateBox();
            }
            catch(err)
            {
                console.log(err.message);
            }
            }
            else if(data !=='0')
            {
                thiss.tryReInstallation(data);
            }
            });
        }
        catch(err){console.log(err.message);}
    };
    this.doDownload=function()
    {
        var thiss=this;
        thiss.container.querySelectorAll(".card-body")[0].innerHTML="<strong>Downloading...</strong>";
        this.request.postRequestCb('req.php',{"checkforqfnl_update":"download","checkforqfnl_update_url":btoa(this.downloadurl),"checkforqfnl_update_version":this.version},function(data){
            //console.log(data);
            if(data.trim()==1)
            {
                thiss.container.querySelectorAll(".card-body")[0].innerHTML="<strong>Installing...</strong>";
                thiss.doInstall();
            }
            else
            {
                thiss.tryReInstallation(data);
            }
        });
    };
    this.doInstall=function(){
        var thiss=this;
        //alert('checkforqfnl_update=install&checkforqfnl_update_version='+this.version);
        this.request.postRequestCb('req.php',{"checkforqfnl_update":"install","checkforqfnl_update_version":this.version},function(data){
            //alert(data);
            if(data.trim()==1)
            {
                thiss.request.postRequestCb('req.php',{"checkforqfnl_update":"install_dependency","checkforqfnl_update_version":thiss.version},function(data){
                    if(data.trim()==1)
                    {
                        thiss.container.querySelectorAll(".card-body")[0].innerHTML="<font color='green'><strong>Installed Successfully.</strong></font>";
                        setTimeout(function(){document.body.removeChild(thiss.container);},1000);
                    }
                    else
                    {
                        thiss.tryReInstallation(data);
                    }
                });
            }
            else
            {
                thiss.tryReInstallation(data);
            }
        })
    };
    this.tryReInstallation=function(err){
        console.log(err);
        this.container.getElementsByClassName("card-body")[0].innerHTML="<font style='color:#e6005c'><strong>Unable To Install Update.</strong></font>";
        var buttons=this.container.getElementsByTagName("button");
        for(var i=0;i<buttons.length;i++)
        {
            if(buttons[i].disabled)
            {
                buttons[i].disabled=false;
            }
        }
    };
    this.showUpdateBox=function(version,changes)
    {
        if(!this.is_gcp)
        {
            var install_button=`<button class="btn btn-success btn-block">Install&nbsp;Update</button>`;
        }
        else
        {
            let dependancy_update_url=`index.php?page=install_update_dependencies&after_update_redirect=${btoa(window.location.href)}`;

            var install_button=`<a href="${dependancy_update_url}"><button class="btn btn-success btn-block">Install&nbsp;Update</button></a>`;   
        }

        var div=document.createElement("div");
        var html=`<div class="card">
                    <div class="card-header bg-success">New Update Available (Version: ${this.version})</div>
                    <div class="card-body">
                        <a href="#" data-bs-toggle="collapse" data-bs-target="#upwhatsnewdetail"><strong>Whats New In This Version?</strong></a><p id="upwhatsnewdetail" class="collapse">${this.changes}</p>
                    </div>
                    <div class="card-footer">
                            <div class="row">
                                <div class='col text-left'>
                                    <button class="btn btn-warning btn-block">Remind&nbsp;Later</button>
                                </div>
                                <div class='col text-right'>
                                    ${install_button}
                                </div>
                            </div>
                    </div>
                </div>`;
        div.setAttribute("id","qmlrautoupdatediv");
        div.innerHTML=html;
        document.body.appendChild(div);
        var thiss=this;
        div.getElementsByTagName("button")[1].onclick=function(e){
            thiss.doDownload();
            e.target.disabled=true;
            div.getElementsByTagName("button")[0].disabled=true;
        };
        div.getElementsByTagName("button")[0].onclick=function(){thiss.remindLater();};
        this.container=div;
        var active=1;
        div.onclick=function(){active=0;};
        document.body.onclick=function(){
            if(active==1)
            {
                document.body.removeChild(div);
            }
            else
            {
                active=1;
            }
        };
    };
    this.remindLater=function(){
        document.body.removeChild(this.container);
        this.request.postRequestCb('req.php',{"checkforqfnl_update":"install_later"},function(data){});
    };
}