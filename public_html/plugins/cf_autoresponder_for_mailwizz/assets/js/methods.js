let mailwizz_methods=
{
    //mailwizz setup
   'mailwizz': {
        title: 'MailWizz',
        logo: 'mailwizz.png',
        setup_header:``,
        setup_footer: ``,
        setup_title: 'Setup MailWizz',

        fields: {
            title:{
                name: 'Title',
                placeholder:"Enter Title",
                label: "Enter Title",
                type: 'text',
                value: "",
                required: true,
            },
            apiurl:{
                name: 'Api URL',
                placeholder:"Enter Api URL",
                label: "Enter Api URL",
                type: 'text',
                value: "",
                required: true,
            },
            apikey:{
                name: 'Api key',
                placeholder:"Enter Api Public Key",
                label: "Enter Api Public Key",
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

export default mailwizz_methods;