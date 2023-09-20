let CFPay_methods=
{
   //Paystack setup
   'flutterwave': {
    title: 'Flutterwave',
    logo: 'flutterwave.png',
    setup_header:``,
    setup_footer: ``,
    setup_title: 'Flutterwave API Setup',

    fields: {
        // except the `title` & `tax` all the key and corresponding value will be taken as pyment credenrials, title will be taken as payment method name 
        title:{
            name: 'Title',
            placeholder:"Enter Title",
            label: "Enter Title",
            type: 'text',
            value: "",
            required: true,
        },
        public_key:{
            name: 'Public Key',
            placeholder:"Enter your public key",
            label: "Enter Public Key",
            type: 'text',
            value: "",
            required: true,
        },
        secret_key:{
            name: 'Secret Key',
            placeholder:"Enter your secret key",
            label: "Enter Secret Key",
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
}
//----------end----------
//Flutterwave
}

export default CFPay_methods;