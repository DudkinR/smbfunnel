let cfstripe_setting_from=new Vue({
    el: "#cfstripe-setting-from",
    mounted:function()
    {
        try{
            let data = document.getElementById("cfstripe_setting_data").value;
            let stripe_id = document.getElementById("cfstripe_stripe_id").value;
            let esubject = document.getElementById("cfstripe_email_subject").value;
            let ebody = document.getElementById("cfstripe_email_content").value;
            let return_url = document.getElementById("cfstripe_return_url").value;
            let email_day = document.getElementById("cfstripe_return_email_day").value;
            this.fileContent = JSON.parse(ebody);
            this.fileSubejct = JSON.parse(esubject);
            this.return_url = JSON.parse(return_url);
            this.email_days = parseInt(email_day);
            if(data)
            {
                let alldata = JSON.parse(data);
                this.form_data=alldata.btn;
                this.block=alldata.card;

            }
            this.cardHtmlf();
            this.stripe_id=stripe_id;
        }
        catch(e)
        {

        }

    },
    data: {
        stripe_id:0,
        fileContent:'',
        fileSubejct:'',
        return_url:'',
        email_days:10,
        fontWeight:[300,400,500,600,700,800,900],
        form_data:{
            bText:"Manage Billing",
            bColor: "#ffffff",
            bSize: "16",
            bBColor: "#007bff",
            bFWeight: "500",
            
            bBorderColor:"#007bff",
            bBorderWidth:"1",
            bBorderStyle:"solid",

            bMargin:{
                mTop:'1',
                mLeft:'1',
                mBottom:"1",
                mRight:'1'
            },
            bPadding:{
                pTop:'7',
                pLeft:'10',
                pBottom:"10",
                pRight:'10'
            },
        },
        block:{
            card:{
                width:'col-md-4',
                backgroundColor:'#ffffff',
            },
            cardHeader:{
                textAlign:'center',
                backgroundColor:'#007BFF',
                padding:'10px 12px 10px 12px',
               
            },
            headerPadding:{
                left:'12',
                right:'12',
                top:'10',
                bottom:'10',
            },
            cardHeading:{
                color:'#ffffff',
                fontSize:'20',
                fontWeight:'600',
            },
            pStatus:{
                color:'#000000',
                text:'Status',
                pStatuscolor:'#28a745',
                fontSize:'16',
                fontWeight:'600',
            },
            pExpire:{
                color:'#000000',
                text:'Expire In',
                pExpirecolor:'#007bff',
                fontSize:'16',
                fontWeight:'600',
            },
          },
        dostyleobj:{
            color:'#ffffff',
            fontSize: "16px",
            backgroundColor: "#007bff",
            fontWeight: "500",
            borderColor:"#007bff",
            borderWidth:"1px",
            borderStyle:"solid",
            margin:"1px 1px 1px 1px",
            padding:"7px 10px 10px 10px"
        },
        cardHtml:''
    },
    methods: {

        cardHtmlf: function()
        {
        this.cardHtml=`<div class="${this.block.card.width}">
                <div class="card"  style="background-color:${this.block.card.backgroundColor}">
                    <div class="card-header"  style="text-align: ${this.block.cardHeader.textAlign}; background-color: ${this.block.cardHeader.backgroundColor}; padding: ${this.block.cardHeader.padding}">
                        <div class=" py-1" style=" color: ${this.block.cardHeading.color}; font-size: ${this.block.cardHeading.fontSize}px; font-weight:${ this.block.cardHeading.fontWeight};">{product_title}</div>
                    </div>
                    <div class="card-body" >
                        <div class="py-1"   style="color: ${this.block.pStatus.color}; font-size: ${this.block.pStatus.fontSize}px;font-weight: ${this.block.pStatus.fontWeight};">${this.block.pStatus.text}: <span  style="color: ${this.block.pStatus.pStatuscolor}" class="text-success"> <i class="fas fa-check-circle "></i> {status}</span> </div>
                        <div class="py-1"   style="color: ${this.block.pExpire.color}; font-size: ${this.block.pExpire.fontSize}px;font-weight: ${this.block.pExpire.fontWeight};">${this.block.pExpire.text}: <span  style="color: ${this.block.pExpire.pExpirecolor}"> {days}</span> day(s)  </div>
                        <div class="pt-3">
                           {form_start}
                            <input type="hidden" name="cfstripe_get_session" value="{sales_id}">
                            <button type="submit" class="btn btn-primary" style="color:${this.dostyleobj.color};font-size:${this.dostyleobj.fontSize};
                            background-color:${this.dostyleobj.backgroundColor};font-weight:${this.dostyleobj.fontWeight};border-color:${this.dostyleobj.borderColor}
                            border-width:${this.dostyleobj.borderWidth};border-style:${this.dostyleobj.borderStyle};margin:${this.dostyleobj.margin};padding:${this.dostyleobj.padding}
                            ">${this.form_data.bText} &nbsp;<i class="fas fa-file-invoice-dollar"></i></button>
                           {form_end}
                        </div>
                    </div>
                </div>
            </div>`;
            return this.cardHtml;
        },

        saveSetup:function(){
            let btn = this.$refs.stripe_setting_btn;
            let url = document.getElementById("cfstripe_ajaxUrl").value;
            btn.innerHTML=`Saving <i class="fa fa-spinner fa-spin"></i>`;
            let req=new XMLHttpRequest();
            req.onreadystatechange=()=>{
                if(req.readyState==4)
                {
                    if(req.status===200)
                    {
                        let re=req.responseText.trim();
                        try{
                            let res = JSON.parse(re);
                            console.log(res.status);
                            if(res.status==1)
                            {
                                setTimeout(()=>{
                                    window.location=location.href;
                                },200);
                            }
                            else
                            {
                                this.err="Unable to save the setup";
                            }
                        }catch(e)
                        {
                            console.log(e);
                        }
                    }
                    else
                    {
                        console.log(req.statusText);
                    }
                }
            };
            this.fileContent =tinyMCE.get("cfstripe_gmail_content").getContent();
            let fdata= new FormData();
            let setting={ btn:this.form_data,card:this.block};
            req.open('POST',url,true);
            fdata.append('setting',JSON.stringify(setting));
            fdata.append('card_html',this.cardHtmlf());
            fdata.append('return_url', this.return_url);
            fdata.append('file_content', this.fileContent);
            fdata.append('email_days', this.email_days);
            fdata.append('file_subject', this.fileSubejct);
            fdata.append('action','cfstripe_billing_btn');
            fdata.append('stripe_id',this.stripe_id);
            req.send(fdata);

        }

        
    },
    watch: {
        'form_data.bSize':{
            handler: function(val)
            {
                this.dostyleobj.fontSize=val+"px";
            },
            deep:true
        },
        'form_data.bColor':{
            handler: function(val)
            {
                this.dostyleobj.color=val;
            },
            deep:true
        },
        'form_data.bBColor':{
            handler: function(val)
            {
                this.dostyleobj.backgroundColor=val;
            },
            deep:true
        },
        'form_data.bFWeight':{
            handler: function(val)
            {
                this.dostyleobj.fontWeight=val;
            },
            deep:true
        },
        'form_data.bBorderColor':{
            handler: function(val)
            {
                this.dostyleobj.borderColor=val;
            },
            deep:true
        },
        'form_data.bBorderWidth':{
            handler: function(val)
            {
                this.dostyleobj.borderWidth=val+"px";
            },
            deep:true
        },
        'form_data.bBorderStyle':{
            handler: function(val)
            {
                this.dostyleobj.borderStyle=val;
            },
            deep:true
        },
        'form_data.bMargin':{
            handler: function(val)
            {
                let margin= val.mTop+"px "+val.mRight+"px "+val.mBottom+"px "+val.mLeft+"px";
                this.dostyleobj.margin=margin;
            },
            deep:true
        },
        'form_data.bPadding':{
            handler: function(val)
            {
                let padding= val.pTop+"px "+val.pRight+"px "+val.pBottom+"px "+val.pLeft+"px";
                this.dostyleobj.padding=padding;
            },
            deep:true
        },
        'block.headerPadding':{
            handler: function(val)
            {
                let padding= val.top+"px "+val.right+"px "+val.bottom+"px "+val.left+"px";
                this.block.cardHeader.padding=padding;
            },
            deep:true
        },
    }
});