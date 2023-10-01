let convertkit_methods=
{
    //convertkit setup
   'convertkit': {
        title: 'ConvertKit',
        logo: 'convertkit.png',
        setup_header:``,
        setup_footer: ``,
        setup_title: 'Setup ConvertKit',

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
                name: 'Form Id',
                placeholder:"Enter Form Id",
                label: "Enter Form Id",
                type: 'text',
                value: "",
                required: true,
            },
            email:{
                name: 'email',
                placeholder:"Enter Unique email which is not present list",
                label: "Enter email",
                type: 'text',
                value: "",
                required: true,
            },
         
        },
   },
   //---------end-----------
}

export default convertkit_methods;