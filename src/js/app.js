App = function(){
    this.templates = {};
    this.url;
}

App.prototype.init = function(urlbase){
    var url = urlbase;
    this.url = url;
    $(document).ready(function(){

        $('#fileupload').fileupload({
            url: url+'a/upload',
            dataType: 'json',
            start: function (e) {
                app.clearMessages();
                $("#progress .progress-bar").removeClass('progress-bar-danger');
                $("#progress .progress-bar").addClass('progress-bar-success');
                $('#fileupload').data('current_file',null);
            },
            done: function (e, data) {
                if(data.result.status == "SUCCESS"){
                    $('#fileupload').data('current_file',data.result.data);
                    app.showSuccess("File Uploaded. Please add a title and post the image.");
                }else{
                    app.showError(data.result.data);
                }
            },
            fail: function (e, data) {
                $("#progress .progress-bar").removeClass('progress-bar-success');
                $("#progress .progress-bar").addClass('progress-bar-danger');
                app.showError('Error occurred while uploading file');
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
            }
        });

        $(".post-button").off().on('click',function(){
            $("#upload_error").hide();
            var data = {};
            data['title'] = $("#post_title").val();
            //if(data['title'] == ''){
                //app.showError('Please enter a title');
                //return;
            //}
            data['file'] = $('#fileupload').data('current_file');
            if(data['file'] == '' || data['file'] == null || data['file'] == undefined){
                app.showError('Please upload a file');
                return;
            }

            $.post(url+'a/post_image', data, function(data) {
                app.clearMessages();
                if(data.status == "SUCCESS"){
                    app.showSuccess('Image successfully posted');
                    $('#fileupload').data('current_file',null);
                    $("#post_title").val('');
                    $('#progress .progress-bar').css(
                        'width',
                        '0%'
                    );

                    store.addPost(data.data);

                }else{
                    app.showError(data.data);
                }
            },"json");

        })
    });


    var postListener = new PostListener($(".postContainer"),store,app);
    this.startPostReader();
};

App.prototype.setTemplates = function(data){
    this.templates = data;   
};

App.prototype.clearMessages = function(){
    $("#upload_error").hide();
};

App.prototype.showError = function(msg){
    $("#upload_error .alert").removeClass('alert-success');
    $("#upload_error .alert").addClass('alert-danger');
    $("#upload_error .message").html(msg);
    $("#upload_error").show();
};

App.prototype.showSuccess = function(msg){
    $("#upload_error .alert").removeClass('alert-danger');
    $("#upload_error .alert").addClass('alert-success');
    $("#upload_error .message").html(msg);
    $("#upload_error").show();
};

App.prototype.startPostReader = function(){

    this.getNewPosts();

    setTimeout(function() {
        app.startPostReader();
    }, 15000);
};

App.prototype.getNewPosts = function(){

    var id = store.getTopPostId();

    $.post(this.url+'a/get_posts', {lastId:id}, function(data) {
        if(data.status == "SUCCESS"){
            for(var i=0;i<data.data.length;i++){
                store.addPost(data.data[i]);
            }
        }
    },"json");
};



PostStore = function(){
    this.posts = {};
    this.subcribers = [];
}

PostStore.prototype.notifyAll = function(data){
    for(var i=0;i<this.subcribers.length;i++){
        this.subcribers[i].update(data);
    }
};

PostStore.prototype.subscribe = function(listener){
    this.subcribers.push(listener);
};

PostStore.prototype.addPost = function(post){
    this.posts[post.id] = post;
    this.notifyAll(post);
};

PostStore.prototype.getTopPostId = function(){
    if(Object.keys(this.posts).length == 0){
        return 0;
    }
    var max =0;
    for(index in this.posts){
        if(max < index){
            max = index;
        }
    }
    return max;
};


PostListener = function(container, postStore, app){
    this.container = container;
    this.app = app;
    postStore.subscribe(this);
}

PostListener.prototype.update = function(post){
    if($("#post_"+post.id).length > 0){
        $("#post_"+post.id).remove();
    }
    this.container.prepend(this.build(post));
};

PostListener.prototype.build = function(post){
    var template = this.app.templates.post ;
    var t = template.replace('#_post_id_#','post_'+post.id);

    if(post.title == null || post.title == undefined || post.title == ""){
        t = t.replace('#_title_#','');
    }else{
        t = t.replace('#_title_#',post.title);
    }

    var t = t.replace('#_url_#',this.app.url+'data/'+post.image);
    $postHtml = $(t);
    if(post.title == null || post.title == undefined || post.title == ""){
        $postHtml.find('.title_hr').remove();
    }
    return $postHtml;
};
