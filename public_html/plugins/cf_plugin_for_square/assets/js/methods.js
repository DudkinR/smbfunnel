let CFPay_methods=
{
   //square  setup
   'square': {
    title: 'square',
    logo: 'square.png',
    setup_header:``,
    setup_footer: ``,
    setup_title: 'square API Setup',

    fields: {
        title:{
            name: 'Title',
            placeholder:"Enter Title",
            label: "Enter Title",
            type: 'text',
            value: "",
            required: true,
        }, 
        app_id: {
            name: 'Application ID',
            placeholder:"Enter Application ID",
            label: "Application ID",
            type: 'text',
            value: "",
            required: true,
        },    
        access_token: {
            name: 'Access token',
            placeholder:"Enter Access token",
            label: "Access token",
            type: 'text',
            value: "",
            required: true,
        },
        location_id: {
            name: 'Location ID',
            placeholder:"Enter Location ID",
            label: "Location ID",
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
        storeName:{
            name: 'Store Name',
            placeholder:"Enter Store Name",
            label: "Enter Store Name",
            type: 'text',
            value: "",
            required: true,
        },
        logo_url:{
            name: 'Logo URL',
            placeholder:"Enter Logo URL(Size 60 * 40)",
            label: "Enter Logo URL",
            type: 'url',
            value: "",
            required: true,
        },
        
    },
},
//----------end----------
//Flutterwave
}

export default CFPay_methods;