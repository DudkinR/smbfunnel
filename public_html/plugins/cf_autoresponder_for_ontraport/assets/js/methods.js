let ontraport_methods=
{
    //ontraport setup
   'ontraport': {
        title: 'Ontraport',
        logo: 'ontraport.png',
        setup_header:``,
        setup_footer: ``,
        setup_title: 'Setup Ontraport',

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
            appid:{
                name: 'App Id',
                placeholder:"Enter App Id",
                label: "Enter App Id",
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

export default ontraport_methods;