{{ config_load file="{{ $gimme->language->english_name }}.conf" }}
{{ include file="_tpl/_html-head.tpl" }}

<body>
<!--[if lt IE 7]>
    <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->
          
{{ include file="_tpl/header.tpl" }}

<section role="main" class="homepage">
    <div class="wrapper">

    {{ include file="_tpl/front-slider.tpl" }}

        <div class="container">
            <section id="content">
                <div class="row">

                {{ include file="_tpl/front-stories.tpl" }}
                                              
                {{ include file="_tpl/sidebar.tpl" }}          
                </div> <!--end div class="row"-->

                {{ include file="_tpl/multimedia.tpl" }}          

                <div class="row front-bottom-blocks">
                    {{ include file="_tpl/more-news-block.tpl" }}          

                    {{ include file="_tpl/map.tpl" }}          
                </div>

            </section> <!-- end section id=content -->
        </div> <!-- end div class='container' -->
    </div> <!-- end div class='wrapper' -->
</section> <!-- end section role main -->

{{ include file="_tpl/footer.tpl" }}

{{ include file="_tpl/_html-foot.tpl" }}
