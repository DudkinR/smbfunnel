let media_storage= new Vuex.Store({
    state:{
        view: 'init',
        uploadable_file: null,
        media_url: null,
        current_type: 'all',
        types: [],
        files:{},
        selected_file: null,
        selected_page: 1,
        max_files:0,
        search_files: [],
    },
    getters: {},
    mutations: {
        update:function(state,data){
            for(let i in data)
            {
                state[i]=data[i];
            }
        },
        pushIntoArr: function(state, data)
        {

        },
        pushIntoObject: function(state, data)
        {

        },
        rmFromArr: function(state, data)
        {

        },
        rmFromObject: function(state, data)
        {

        }
    },
    actions: {}
});