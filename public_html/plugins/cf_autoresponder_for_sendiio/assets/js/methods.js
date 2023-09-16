let sendiio_methods=
{
    //sendiio setup
   'sendiio': {
        title: 'Sendiio',
        logo: 'sendiio.png',
        setup_header:``,
        setup_footer: ``,
        setup_title: 'Setup sendiio',

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
                name: 'API Secret',
                placeholder:"Enter API Secret",
                label: "Enter API Secret",
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

export default sendiio_methods;