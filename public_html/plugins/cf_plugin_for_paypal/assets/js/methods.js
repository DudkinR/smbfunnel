let CFPay_methods=
{
   //Paypal setup
   'paypal': {
    title: 'Paypal',
    logo: 'paypal.png',
    setup_header:``,
    setup_footer: ``,
    setup_title: 'Paypal API Setup',

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
            placeholder:"Enter Client Id",
            label: "Client Id",
            type: 'text',
            value: "",
            required: true,
        },
        client_secret: {
            name: 'Secret Key',
            placeholder:"Enter your secret key",
            label: "Enter Secret Key",
            type: 'text',
            value: "",
            required: true,
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