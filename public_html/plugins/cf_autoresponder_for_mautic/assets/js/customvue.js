let custom_model_vue= new Vue({
    el:"#custom-model-vue",
    data:{
        imgsrc:'',
        imageText:'one'
    },
    methods:{
        chanagetheImage:function(a1,a2){
            this.imgsrc=a1;
            this.imageText=a2;
        }
    }
});