<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$base_url =  base_url().'index.php/slider/';
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Image gallery</title>
  <meta name="description" content="">
  <script>
      var base_url = '<?php echo base_url(); ?>';
  </script>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/gallery.css" />
</head>
<body>
  <div id="gallery">
      <form id="image-upload" method="post" enctype="multipartformdata" action="<?php echo $base_url;?>addupdate">
        <div>
            <label for="imgfile">Image file:</label>
            <input id="imgfile" name="imgfile" type="file" />
        </div>
        <div>
            <label for="slider_name">Caption:</label>
            <input id="slider_name" name="slider_name" value="" type="text" />
        </div>
        <div>
            <label for="slider_subtitle">Slider subtitle:</label>
            <input id="slider_subtitle" name="slider_subtitle" value="" type="text" />
        </div>
            <input id="addimage" name="addimage" value="Add to slider" type="submit" />
            <input id="slider_form_action" name="slider_form_action" value="add" type="hidden" />
    </form>
    <ul id="gallery-list">

    </ul>
  </div>
  <script type="text/template" id="item-template">
    <li class="gallery-item">
    <img src="<?php echo base_url().'img/uploads/';?><%= slider_path %>" alt="<%= slider_name ? slider_name : "" %>" class="thmb"><br />
      <caption class="imgcaption"><%= slider_subtitle ? slider_subtitle : ""  %></caption><br/>
      <a href="<?php echo $base_url;?>delete/<%= slider_id %>" class="imgdeletebutn" title="Remove this image?">Remove this image?</a>  
    </li>
  </script>
<script src="<?php echo base_url(); ?>js/libs/jquery.js"></script>
<script src="<?php echo base_url(); ?>js/libs/jquery.form.js"></script>
<script src="<?php echo base_url(); ?>js/libs/underscore.js"></script>
<script src="<?php echo base_url(); ?>js/libs/backbone.js"></script>
<script src="<?php echo base_url(); ?>index.php/js/files/slider.js"></script>
<script src="<?php echo base_url(); ?>index.php/js/models/slidecollection"></script>
<script>
var SliderImageView = Backbone.View.extend({
    tagName: "li",
    className: "gallery-item",
    imgViewTpl: _.template($('#item-template').html()),
    initialize: function(){
        this.render();
    },
    render: function(){
        return this.imgViewTpl( this.model.toJSON() ); 
    }
});
var SliderImageCollectionView = Backbone.View.extend({
    el: '#gallery',
    listHolder: $('#gallery-list'),
    imgViewTpl: _.template($('#item-template').html()),
    collection: new SliderImageCollection(),
    initialize:function(){
        this.render();
    },
    render: function() {
      var imgCollection = this.collection.models,
            liList = '';
      _.each( imgCollection, function(item){
          var slideView = new SliderImageView({model: item}).render();
          liList = liList + slideView ;
      } );
      this.listHolder.html( liList );
      return this;
    },
    events:{
        "submit #image-upload":function(e){
            e.preventDefault();
            //TODO: submit ajax form, update models
            this.render();
        },
        "click .imgdeletebutn":function(e){
            e.preventDefault();
            var $target = e.target,
                slider_id = $($target).attr('href').replace("<?php echo ($base_url);?>delete/", ""),
                this_model = this.collection.where({"slider_id": slider_id});
            this.collection.remove(this_model);
            $($target).parent().remove();
        }
    }
});

$(function() {
    new SliderImageCollectionView({
        collection: slides
    });
});

</script>
</body>
</html>