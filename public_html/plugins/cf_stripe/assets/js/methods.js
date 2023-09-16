let CFPay_methods=
{
   //Stripe setup
   'stripe': {
    title: 'Stripe',
    logo: 'stripe.png',
    setup_header:``,
    setup_footer: ``,
    setup_title: 'Stripe API Setup',

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
            name: 'Publishable key',
            placeholder:"Enter Publishable key",
            label: "Publishable key",
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
        payment_type: {
            name: 'Payment Type',
            placeholder:"Select Payment Type",
            label: "Select Payment Type",
            type: 'select',
            value: "payment",
            options:[
                { name:"One time payment",value:"payment" },
                { name:"Subscription based payment",value:"subscription" }
            ],
            required: true,
        },
        endpoint_secret: {
            name: 'End Point Secret',
            placeholder:"Enter End Point Secrect (optional)",
            label: "Enter End Point Secrect (optional)",
            type: 'text',
            value: "",
            required: false,
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