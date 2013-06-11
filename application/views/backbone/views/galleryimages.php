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

var GalleryImageCollection = Backbone.Collection.extend({
    model: GalleryImage
});

var GalleryImageCollectionView = Backbone.View.extend({
    el: '#gallery-list',
    imgViewTpl: _.template($('#item-template').html()),
    collection: new GalleryImageCollection(),
    initialize:function(){
        this.render();
    },
    render: function() {
      var imgCollection = this.collection.models,
            liList = '',
            that =  this;
      _.each( imgCollection, function(item){
          liList = liList + that.imgViewTpl( item.toJSON() ) ;
      } );
      this.$el.html( liList );
      return this;
    }
});

var galleryImagesCollection = new GalleryImageCollection(
    [
        {
            image_source: base_url+'img/kumar.jpg',
            image_alt: 'Kumar',
            image_title: 'Kumar Chetan Sharma'
        },
        {
            image_source: base_url+'img/none-shall-pass.png',
            image_alt: 'None shall pass!',
            image_title: "'Tis but a scratch"
        },
        {
            image_source: base_url+'img/scan-tron.jpg',
            image_alt: 'Some stock image',
            image_title: 'Oh, a list!'
        }
    ]
);

$(function() {
    new GalleryImageCollectionView({
        collection: galleryImagesCollection
    });
});

</script>
</body>
</html>