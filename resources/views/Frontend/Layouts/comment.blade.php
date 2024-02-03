<div class="form">

    <h4 style="text-align: right;">نظرات شما</h4>
    <form action="" method="post" role="form" class="contactForm">
        @if(!Auth::check())
            <div class="form-group">
                <input type="text" name="name" class="form-control" id="name" placeholder="نام" data-rule="minlen:4" data-msg="Please enter at least 4 chars" />
                <div class="validation"></div>
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" id="email" placeholder="ایمیل" data-rule="email" data-msg="Please enter a valid email" />
                <div class="validation"></div>
            </div>
        @endif


        <div class="form-group">
            <textarea class="form-control" name="message" rows="5" data-rule="required" data-msg="Please write something for us" placeholder="نظر شما"></textarea>
            <div class="validation"></div>
        </div>

        <div class="text-right"><button type="submit" class="btn-success" title="Send Message">ارسال نظر</button></div>
    </form>
</div>
<div class="reply-comment" >
    <div class="main" style="">
        <div class="profile" >
            <img class="avatar" src="{{ asset('/upload/images/default/default.jpg')}}" width="50" style="border-radius: 50%; vertical-align:middle;" />
            <span class="name">علی مرتضوی</span>
        </div>
        <div class="icon">
            <i class="fa fa-reply fa-flip-horizontal"></i>
        </div>
        <div style="clear: both;"></div>
        <div class="text-right body " >پست بسیار خوبی بود</div>
    </div>
    <div class="main reply">
        <div class="profile">
            <img class="avatar" src="{{ asset('/upload/images/default/default.jpg')}}" width="50" style="border-radius: 50%; vertical-align:middle;" />
            <span class="name">علی مرتضوی</span>
        </div>
        <div class="icon" id="reply">
            <i class="fa fa-reply fa-flip-horizontal"></i>
        </div>
        <div style="clear: both;"></div>
        <div class="text-right body ">پست بسیار خوبی بود</div>
    </div>
    <div id="reply-box"></div>

</div>
<script src="https://code.jquery.com/jquery-3.5.0.js"></script>
<script>
    $( "#reply" ).click(function() {
        $( "#reply-box" ).append( "<p>Test</p>" );
    });
</script>