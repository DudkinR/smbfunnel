let installurl=document.querySelectorAll("#cfpay_base_url")[0].value;

let CFPay_methods=
{
 
   //midtrans setup
   'midtrans': {
    title: 'Midtrans',
    logo: 'midtrans.png',
    setup_header:``,
    setup_footer: ``,
    setup_title: 'Midtrans API Setup',

    fields: {
        success_Payment:{
            name: '',
            label: "Callback URL for successful payment",
            type: 'text',
            value: ""+installurl+"/index.php?page=do_payment&execute=1&success=true",
            required: false,
        },
        cancel_Payment:{
            name: 'Title',
            label: "Callback URL for canceled payment",
            type: 'text',
            value: ""+installurl+"/index.php?page=do_payment&execute=1&success=false",
            required: false,
        },
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
            placeholder:"Enter  Client Key",
            label: "Client Key",
            type: 'text',
            value: "",
            required: true,
        },
        salt: {
            name: 'salt',
            placeholder:"Enter Server Key",
            label: "Server Key",
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