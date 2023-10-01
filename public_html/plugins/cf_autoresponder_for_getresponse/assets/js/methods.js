let getresponse_methods=
{
    //getresponse setup
   'getresponse': {
        title: 'Getresponse',
        logo: 'getresponse.png',
        setup_header:``,
        setup_footer: ``,
        setup_title: 'Setup Getresponse',

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
            campaignid:{
                name: 'campaign Id',
                placeholder:"Enter Campaign Id",
                label: "Enter Campaign Id",
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

export default getresponse_methods;