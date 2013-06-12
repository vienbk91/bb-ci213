var SliderImage = Backbone.Model.extend({
    defaults:{
        slider_id:"",
        slider_name:"",
        slider_subtitle:"",
        slider_description:"",
        slider_link:"",
        slider_order:"",
        slider_path:"",
        slider_status: false,
    }
});

var SliderImageCollection = Backbone.Collection.extend({
    model: SliderImage
});