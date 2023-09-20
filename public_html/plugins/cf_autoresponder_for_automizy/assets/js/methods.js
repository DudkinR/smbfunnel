let CFautores_methods=
{
    //automizy setup
   'automizy': {
        title: 'Automizy',
        logo: 'automizy.jpg',
        setup_header:``,
        setup_footer: ``,
        setup_title: 'Setup Automizy',

        fields: {
            title:{
                name: 'Title',
                placeholder:"Enter Title",
                label: "Enter Title",
                type: 'text',
                value: "",
                required: true,
            },
            api_token:{
                name: 'API Token',
                placeholder:"Enter API Token",
                label: "Enter API Token",
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

export default CFautores_methods;