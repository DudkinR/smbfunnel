let CFPay_methods=
{
   //iPaymu  setup
   'iPaymu': {
    title: 'iPaymu',
    logo: 'iPaymu.png',
    setup_header:``,
    setup_footer: ``,
    setup_title: 'iPaymu API Setup',

    fields: {
        title:{
            name: 'Title',
            placeholder:"Enter Title",
            label: "Enter Title",
            type: 'text',
            value: "",
            required: true,
        }, 
        client_secret: {
            name: 'Secret Key',
            placeholder:"Enter Virtual Account",
            label: "Enter Virtual Account",
            type: 'text',
            value: "",
            required: true,
        },    
        client_id: {
            name: 'Client Id',
            placeholder:"Enter Api Key",
            label: "Api Key",
            type: 'text',
            value: "",
            required: true,
        },
        cctype: {
            name: 'Select Payment Type',
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