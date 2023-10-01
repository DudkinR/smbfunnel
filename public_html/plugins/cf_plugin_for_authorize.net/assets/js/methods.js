let CFPay_methods=
{
   //Authorize.net setup
   'authorize.net': {
    title: 'Authorize.net',
    logo: 'authorizenet.png',
    setup_header:``,
    setup_footer: ``,
    setup_title: 'Authorize.net API Setup',

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
            placeholder:"Enter API Login ID",
            label: "Enter API Login ID",
            type: 'text',
            value: "",
            required: true,
        },
        client_secret: {
            name: 'Secret Key',
            placeholder:"Enter Transaction Key",
            label: "Enter Transaction Key",
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