let constantcont_methods=
{
    //constantcont setup
   'constantcont': {
        title: 'Constant Contact',
        logo: 'constantcont.png',
        setup_header:``,
        setup_footer: ``,
        setup_title: 'Setup Constant Contact',

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
            accesstoken:{
                name: 'Access token',
                placeholder:"Enter Access token",
                label: "Enter Access taoken",
                type: 'text',
                value: "",
                required: true,
            },
            listid:{
                name: 'List Id',
                placeholder:"Enter List Id",
                label: "Enter List Id",
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

export default constantcont_methods;