let mautic_methods=
{
    //mautic setup
   'mautic': {
        title: 'Mautic',
        logo: 'mautic.png',
        setup_header:`<div class="alert alert-danger" role="alert">
            <b>Note: </b>If the authentication failed the first time, then go to your mautic installation site and ,<br><b>setting->configuration->Path to the cache directory</b> <br>
                and change the cache directory path
                <br>
                <div class="text-right">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg">Procedure Images</button></div>
            </div>`,
        setup_footer: ``,
        setup_title: 'Setup Mautic',

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
                name: 'API URL',
                placeholder:"Enter Base URL",
                label: "Enter the base URL for for your Mautic installation",
                type: 'Enter URL',
                value: "",
                required: true,
            },
            appid:{
                name: 'Api Id',
                placeholder:"Enter Username",
                label: "Enter Username",
                type: 'text',
                value: "",
                required: true,
            },
            apikey:{
                name: 'Api key',
                placeholder:"Enter Password",
                label: "Enter Password",
                type: 'password',
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

export default mautic_methods;