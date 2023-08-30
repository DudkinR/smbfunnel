(function(){
    let media_app=new Vue({
        el: "#cf_media_app",
        mounted: function(){
    
        },
        data: {
            request: new ajaxRequest(),
            storage: media_storage,
            allowed_views: ['uploader'],
            view: 'uploader',
            use_mode: 'internal'
        },
        methods: {
            
        },
        watch:{
    
        }
    });
})();
