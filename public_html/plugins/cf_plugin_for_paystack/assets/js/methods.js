let CFPay_methods=
{
   //Paystack setup
   'paystack': {
    title: 'Paystack',
    logo: 'paystack.png',
    setup_header:``,
    setup_footer: ``,
    setup_title: 'Paystack API Setup',

    fields: {
        title:{
            name: 'Title',
            placeholder:"Enter Title",
            label: "Enter Title",
            type: 'text',
            value: "",
            required: true,
        },
        secret:{
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