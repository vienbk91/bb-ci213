<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
      <ul id="gallery-list">
          
      </ul>
  </div>
  <script type="text/template" id="item-template">
    <li class="gallery-item">
      <img src="<%= image_source %>" alt="<%= image_alt ? image_alt : "" %>" class="thmb">
      <caption><%= image_title ? image_title : ""  %></caption>
    </li>
  </script>
<script src="<?php echo base_url(); ?>js/libs/jquery.js"></script>
<script src="<?php echo base_url(); ?>js/libs/underscore.js"></script>
<script src="<?php echo base_url(); ?>js/libs/backbone.js"></script>
<script>
var GalleryImage = Backbone.Model.extend({
    defaults:{
        image_source: base_url+'img/default.jpg',
        image_alt: '',
        image_title: 'Untitled!'
    }
});

var GalleryImageView = Backbone.View.extend({
    el: '#gallery-list',
    imgViewTpl: _.template($('#item-template').html()),
    model: new GalleryImage({
        image_source: base_url+'img/default.jpg',
        image_alt: '',
        image_title: 'Untitled!'
    }),
    initialize:function(){
        this.render();
    },
    render: function() {
      this.$el.html( this.imgViewTpl( this.model.toJSON() ) );
      return this;
    }
});

  $(function() {
    new GalleryImageView();
  });

</script>
</body>
</html>