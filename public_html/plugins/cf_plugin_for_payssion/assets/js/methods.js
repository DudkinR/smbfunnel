let CFPay_methods=
{
   //payssion  setup
   'payssion': {
    title: 'payssion',
    logo: 'payssion.png',
    setup_header:``,
    setup_footer: ``,
    setup_title: 'payssion API Setup',

    fields: {
        title:{
            name: 'Title',
            placeholder:"Enter Title",
            label: "Enter Title",
            type: 'text',
            value: "",
            required: true,
        }, 
        client_API: {
            name: 'API Key',
            placeholder:"Enter API Key",
            label: "API Key",
            type: 'text',
            value: "",
            required: true,
        },    
        client_secret: {
            name: 'Secret Key',
            placeholder:"Enter Secret Key",
            label: "Secret Key",
            type: 'text',
            value: "",
            required: true,
        },
        cctype: {
            name: 'Select Payment Type ',
            label: "Select Payment Type",
            type: 'select',
            value:1,
            options:[
                { name:"Live",value:"1" },
                { name:"Sandbox",value:"0" }
            ],  
            
        },
        tax:{
            name: 'Tax',
            placeholder:"Enter tax amount",
            label: "Enter Tax Amount (will be applied as a percentage)",
            type: 'number',
            value: 0,
            required: true,
        },
    },
},
//----------end----------
//Flutterwave
}

export default CFPay_methods;