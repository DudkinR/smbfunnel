let CFkirim_methods=
{
    //kirim setup
   'kirim': {
        title: 'kirim',
        logo: 'kirim.png',
        setup_header:``,
        setup_footer: ``,
        setup_title: 'Setup kirim',

        fields: {
            title:{
                name: 'Title',
                placeholder:"Enter Title",
                label: "Enter Title",
                type: 'text',
                value: "",
                required: true,
            },
            auth_id:{
                name: 'Auth Id',
                placeholder:"Enter Auth Id",
                label: "Enter Auth Id",
                type: 'text',
                value: "",
                required: true,
            },
            auth_token:{
                name: 'Auth Token',
                placeholder:"Enter Auth Token",
                label: "Enter Auth Token",
                type: 'text',
                value: "",
                required: true,
            },
            list_id:{
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

export default CFkirim_methods;