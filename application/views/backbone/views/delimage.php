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
    <form id="image-upload" method="post" enctype="multipartformdata">
        <label for="imgfile">Image file:</label>
        <input id="imgfile" name="imgfile" type="file" />
        <label for="imgcaption">Caption:</label>
        <input id="imgcaption" name="imgcaption" value="" type="text" />
        <label for="imgalt">Alt text for image:</label>
        <input id="imgalt" name="imgalt" value="" type="text" />
        <input id="addimage" name="addimage" value="Add to gallery" type="submit" />
    </form>
    <ul id="gallery-list">

    </ul>
  </div>
  <script type="text/template" id="item-template">
    <li class="gallery-item">
      <img src="<%= image_source %>" alt="<%= image_alt ? image_alt : "" %>" class="thmb"><br />
      <caption class="imgcaption"><%= image_title ? image_title : ""  %></caption><br/>
      <a href="<%= image_id %>" class="imgdeletebutn" title="Remove this image?">Remove this image?</a>  
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
    },
    validate: function(attribs){
        if(attribs.image_source === undefined){
            return "AWWW!!! No image uploaded.";
        }
        if(attribs.image_alt === undefined){
            return "Alt would be good to have.";
        }
        if(attribs.image_title === undefined){
            return "Title is a nice thing to have.";
        }
    },
    initialize:function(){
        this.on("invalid", function( error){
            console.log("Error: "+error );
        });
    }
});

var GalleryImageCollection = Backbone.Collection.extend({
    model: GalleryImage
});

var GalleryImageCollectionView = Backbone.View.extend({
    el: '#gallery',
    listHolder: $('#gallery-list'),
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
          var cid = item.cid;
          item.set("image_id", cid);
          liList = liList + that.imgViewTpl( item.toJSON() ) ;
      } );
      this.listHolder.html( liList );
      return this;
    },
    events:{
        "submit #image-upload":function(e){
            //do an ajax upload here use the response
            var responseObj = new GalleryImage();
            responseObj.set({
                //image_source: Math.random(),
                image_alt: $('#imgalt').val(),
                image_title: $('#imgcaption').val()
            },{validate:true});
            e.preventDefault();
            this.collection.add(responseObj);
            this.render();
        },
        "click .imgdeletebutn":function(e){
            e.preventDefault();
            var $target = e.target,
                model_cid = $($target).attr('href'),
                this_model = this.collection.get(model_cid);
            this.collection.remove(this_model);
            console.log(this.collection);
            $($target).parent().remove();
        }
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