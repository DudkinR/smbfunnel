let installurl=document.querySelectorAll("#cfpay_base_url")[0].value;

let CFPay_methods=
{
 
   //ccavenue setup
   'ccavenue': {
    title: 'ccavenue',
    logo: 'ccavenue.png',
    setup_header:``,
    setup_footer: ``,
    setup_title: 'CCAvenue API Setup',

    fields: {
        title:{
            name: 'Title',
            placeholder:"Enter Title",
            label: "Enter Title",
            type: 'text',
            value: "",
            required: true,
        },
        client_id: {
            name: 'Client Id',
            placeholder:"Enter Merchant Id",
            label: "Merchant Id",
            type: 'text',
            value: "",
            required: true,
        },
        client_secret: {
            name: 'Secret Key',
            placeholder:"Enter Access Code",
            label: "Access Code",
            type: 'text',
            value: "",
            required: true,
        },
        salt: {
            name: 'salt',
            placeholder:"Enter Working Key",
            label: "Working Key",
            type: 'text',
            value: "",
            required: true,
        },
        type:{
            name: 'type',
            label: "Select Type",
            type: "select",
            options:[
                {name:'Sandbox', value: 0},
                {name:'Live', value: 1},
            ],
            value: 1,
            title: 'Sandbox is to just test the payment method it will not create real payment.'
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
//Midtrans end
}

export default CFPay_methods;