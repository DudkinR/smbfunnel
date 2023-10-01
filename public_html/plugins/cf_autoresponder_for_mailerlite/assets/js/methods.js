let mailerlite_methods=
{
    //mailerlite setup
   'mailerlite': {
        title: 'Mailerlite',
        logo: 'mailerlite.png',
        setup_header:``,
        setup_footer: ``,
        setup_title: 'Setup Mailerlite',

        fields: {
            title:{
                name: 'Title',
                placeholder:"Enter Title",
                label: "Enter Title",
                type: 'text',
                value: "",
                required: true,
            },
            apikey:{
                name: 'Api key',
                placeholder:"Enter Api Key",
                label: "Enter Api Key",
                type: 'text',
                value: "",
                required: true,
            },
            listid:{
                name: 'List Id',
                placeholder:"Enter Group Id",
                label: "Enter Group Id",
                type: 'text',
                value: "",
                required: true,
            },
            email:{
                name: 'Email',
                placeholder:"Enter Unique Email ID Not Present in List",
                label: "Enter Email",
                type: 'email',
                value: "",
                required: true,
            },
        },
   },
   //---------end-----------
}

export default mailerlite_methods;