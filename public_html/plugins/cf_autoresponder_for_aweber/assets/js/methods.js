let aweber_methods=
{
    //aweber setup
   'aweber': {
        title: 'Aweber',
        logo: 'aweber.png',
        setup_header:``,
        setup_footer: ``,
        setup_title: 'Setup Aweber',

        fields: {
            title:{
                name: 'Title',
                placeholder:"Enter Title",
                label: "Enter Title",
                type: 'text',
                value: "",
                required: true,
            },
            appid:{
                name: 'Authentication ID',
                placeholder:"Enter Authentication ID",
                label: "Enter Authentication ID",
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

export default aweber_methods;