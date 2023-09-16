let CFPay_methods=
{
   //Clickbank setup
   'clickbank': {
    title: 'ClickBank',
    logo: 'clickbank.png',
    setup_header:``,
    setup_footer: ``,
    setup_title: 'ClickBank API Setup',

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
            placeholder:"Enter secret key",
            label: "Enter Secret Key",
            type: 'text',
            value: "",
            required: true,
        },
    },
},
//----------end----------
//Flutterwave
}

export default CFPay_methods;