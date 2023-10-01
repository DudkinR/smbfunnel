let activecampaign_methods=
{
    //activecampaign setup
   'activecampaign': {
        title: 'Activecampaign',
        logo: 'activecampaign.png',
        setup_header:``,
        setup_footer: ``,
        setup_title: 'Setup Activecampaign',

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
            apiurl:{
                name: 'Api Url',
                placeholder:"Enter Api Url",
                label: "Enter Api Url",
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
           
        },
   },
   //---------end-----------
}

export default activecampaign_methods;