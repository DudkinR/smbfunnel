let CFPay_methods = {
    //Coinpayment setup
    'coinpayment': {
        title: 'Coin Payment',
        logo: 'logo.png',
        setup_header: ``,
        setup_footer: ``,
        setup_title: 'Coin Payment API Setup',

        fields: {
            // except the `title` & `tax` all the key and corresponding value will be taken as pyment credenrials, title will be taken as payment method name 
            title: {
                name: 'Title',
                placeholder: "Enter Title",
                label: "Enter Title",
                type: 'text',
                value: "",
                required: true,
            },
            merchant_id: {
                name: 'Merchant ID',
                placeholder: "Enter Your Merchant ID",
                label: "Enter Merchant ID",
                type: 'text',
                value: "",
                required: true,
            },
            ipn_secret: {
                name: 'IPN Secret',
                placeholder: "Enter Your IPN Secret",
                label: "Enter IPN Secret",
                type: 'text',
                value: "",
                required: true,
            },
            tax: {
                name: 'Tax',
                placeholder: "Enter tax amount",
                label: "Enter Tax Amount (will be applied as a percentage)",
                type: 'number',
                value: 0,
                required: true,
            },
        },
    }
    //----------end----------
    //Coin Payment
}

export default CFPay_methods;