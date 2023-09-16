<?php


$cfseo_page_url = stripcslashes(trim(stripcslashes($page_url),"\r\n"));
$cfseo_description = stripcslashes(trim(stripcslashes($seo->description),"\r\n"));
$cfseo_title = stripcslashes(trim(stripcslashes($seo->title),"\r\n"));
$cfseo_keyword = stripcslashes(trim(stripcslashes($seo->keywords),"\r\n"));
$cfseo_author = stripcslashes(trim(stripcslashes($seo->author),"\r\n"));
$cfseo_robots_value = stripcslashes(trim(stripcslashes($seo->robots_value),"\r\n"));
$cfseo_icon = stripcslashes(trim(stripcslashes($seo->icon),"\r\n"));
$cfseo_custom_meta_tag = stripcslashes(trim(stripcslashes($r['custom_meta']),"\r\n"));
$cfseo_schema_file = str_ireplace("\\r\\n ", "", $cfseo_schema_file );
$cfseo_schema_file = stripcslashes($cfseo_schema_file);
$cfseo_schema_file = str_ireplace(" ", "", strip_tags($cfseo_schema_file));

if($qry2->num_rows>0)
{
    $social_data = $qry2->fetch_assoc();
    $a_d = json_decode($social_data['accounts_data']);
    $a = json_decode($social_data['accounts']);
    $cfseo_enable_og = ( ($a_d->enable_og)  && isset( $a_d->enable_og ) && !empty( $a_d->enable_og ) ) ?  $a_d->enable_og:false;
    $cfseo_locale = stripcslashes(trim(stripcslashes($a_d->locale),"\r\n"));
    $cfseo_article_type = stripcslashes(trim(stripcslashes($a_d->article_type),"\r\n"));
    $cfseo_facebook_image_url = stripcslashes(trim(stripcslashes($a_d->facebook_image_url),"\r\n"));
    $cfseo_facebook_app_id = stripcslashes(trim(stripcslashes($a_d->facebook_app_id),"\r\n"));
    $cfseo_twitter_site = stripcslashes(trim(stripcslashes($a->twitter),"\r\n"));
    $cfseo_twitter_card = stripcslashes(trim(stripcslashes($a_d->twitter_default_card),"\r\n"));
    $cfseo_reading_time = stripcslashes(trim(stripcslashes($a_d->reading_time),"\r\n"));
    $cfseo_pinterest_verification = stripcslashes(trim(stripcslashes($a_d->pinterest_verification),"\r\n"));
}else{
    $cfseo_enable_og=false;
    $cfseo_locale="";
    $cfseo_article_type="";
    $cfseo_facebook_image_url="";
    $cfseo_facebook_app_id="";
    $cfseo_twitter_site="";
    $cfseo_twitter_card="";
    $cfseo_reading_time="";
    $cfseo_pinterest_verification="";
}
echo PHP_EOL.PHP_EOL;
?>
<!-- SEO Plugin Start -->
<?php
if(!empty($cfseo_title))
{
    if($prds && isset( $prds->title ) )
    {
        $cfseo_title = str_ireplace("{product_title}",$prds->title,$cfseo_title);
    }
    if($collections && isset( $collections->title ) )
    {
        $cfseo_title = str_ireplace("{collection_title}",$collections->title,$cfseo_title);
    }
    echo "<title>".$cfseo_title."</title>".PHP_EOL;
}
if( !empty( $cfseo_description ))
{
    if($prds && isset( $prds->description ) )
    {
        $cfseo_description = str_ireplace("{product_description}",strip_tags($prds->description),$cfseo_description);
    }
    if($collections && isset( $collections->description ) )
    {
        $cfseo_description = str_ireplace("{collection_description}",$collections->description,$cfseo_description);
    }
    if(stristr($cfseo_description,"'")){
        echo '<meta name="description" content="'.$cfseo_description.'" />'.PHP_EOL;
    }elseif(stristr($cfseo_description,'"')){
        echo "<meta name='description' content='".$cfseo_description."' />".PHP_EOL;
    }else{
        echo "<meta name='description' content='".$cfseo_description."' />".PHP_EOL;
    }
}
if(!empty( $cfseo_keyword ))
{
    if(stristr($cfseo_keyword,"'")){
        echo '<meta name="keywords" content="'.$cfseo_keyword.'" />'.PHP_EOL;
    }elseif(stristr($cfseo_keyword,'"')){
        echo "<meta name='keywords' content='".$cfseo_keyword."' />".PHP_EOL;
    }else{
        echo "<meta name='keywords' content='".$cfseo_keyword."' />".PHP_EOL;

    }
}
if(!empty( $cfseo_author ) )
{
    if(stristr($cfseo_author,"'")){
        echo '<meta name="author" content="'.$cfseo_author.'" />'.PHP_EOL;
    }elseif(stristr($cfseo_keyword,'"')){
        echo "<meta name='author' content='".$cfseo_author."' />".PHP_EOL;
    }else{
        echo "<meta name='author' content='".$cfseo_author."' />".PHP_EOL;
    }
}
if(!empty($cfseo_robots_value))
{
    if(stristr($cfseo_robots_value,"'")){
        echo '<meta name="robots" content="'.$cfseo_robots_value.'" />'.PHP_EOL;
    }elseif(stristr($cfseo_keyword,'"')){
        echo "<meta name='robots' content='".$cfseo_robots_value."' />".PHP_EOL;
    }else{
        echo "<meta name='robots' content='".$cfseo_robots_value."' />".PHP_EOL;
    }
}
if( !empty($cfseo_icon) )
{
    echo '<link rel="icon" href="'.$cfseo_icon.'" type="image/x-icon">'.PHP_EOL;
}
if( !empty($cfseo_locale)  )
{
    echo '<meta property="og:locale" content="'.$cfseo_locale.'" />'.PHP_EOL;
}

if($cfseo_enable_og){
    if( !empty($cfseo_article_type) )
    {
        echo '<meta property="og:type" content="'.$cfseo_article_type.'" />'.PHP_EOL;
    }

    if( !empty($cfseo_title) )
    {
        if(stristr($cfseo_title,"'")){
            echo '<meta name="og:title" content="'.$cfseo_title.'" />'.PHP_EOL;
        }elseif(stristr($cfseo_title,'"')){
            echo "<meta name='og:title' content='".$cfseo_title."' />".PHP_EOL;
        }else{
            echo "<meta name='og:title' content='".$cfseo_title."' />".PHP_EOL;
        }
    }
    if( !empty($cfseo_description) )
    {
        if(stristr($cfseo_description,"'")){
            echo '<meta name="og:description" content="'.$cfseo_description.'" />'.PHP_EOL;
        }elseif(stristr($cfseo_description,'"')){
            echo "<meta name='og:description' content='".$cfseo_description."' />".PHP_EOL;
        }else{
            echo "<meta name='og:description' content='".$cfseo_description."' />".PHP_EOL;
        }
    }
    if( !empty($page_url) )
    {
        echo '<meta property="og:url" content="'.$cfseo_page_url.'" />'.PHP_EOL;
    }
    if( !empty($created_date) )
    {
        echo '<meta property="article:published_time" content="'.$created_date.'" />'.PHP_EOL;
    }
    if( !empty( $update_date ) )
    {
        echo '<meta property="article:modified_time" content="'.$update_date.'" />'.PHP_EOL;
    }

    if( !empty($cfseo_facebook_image_url) )
    {
        echo '<meta property="og:image" content="'.$cfseo_facebook_image_url.'" />'.PHP_EOL;
    }
    if( !empty($cfseo_facebook_app_id ) )
    {
        echo '<meta property="fb:app_id" content="'.$cfseo_facebook_app_id.'" />'.PHP_EOL;
    }



    if( !empty( $cfseo_twitter_site ) ){
        echo '<meta name="twitter:site" content="@'.$cfseo_twitter_site.'" />'.PHP_EOL;
        echo '<meta name="twitter:creator" content="@'.$cfseo_twitter_site.'" />'.PHP_EOL;
    }

    if( !empty($cfseo_twitter_card) && $cfseo_twitter_card=="s")
    {
        echo '<meta name="twitter:card" content="summary" />'.PHP_EOL;

    }elseif( !empty( $cfseo_twitter_card ) && $cfseo_twitter_card == "s_img"){

        echo '<meta name="twitter:card" content="summery with large image" />'.PHP_EOL;
    }

    echo '<meta name="twitter:label1" value="Written by">'.PHP_EOL;

    if( !empty($cfseo_author) )
    {
        if(stristr($cfseo_author,"'")){
            echo '<meta name="twitter:data1" content="'.$cfseo_author.'" />'.PHP_EOL;
        }elseif(stristr($cfseo_keyword,'"')){
            echo "<meta name='twitter:data1' content='".$cfseo_author."' />".PHP_EOL;
        }else{
            echo "<meta name='twitter:data1' content='".$cfseo_author."' />".PHP_EOL;
        }

    }
    echo '<meta name="twitter:label2" value="Est. reading time">'.PHP_EOL;

    if( !empty($cfseo_reading_time ) )
    {
        if(stristr($cfseo_reading_time,"'")){
            echo '<meta name="twitter:data2" content="'.$cfseo_reading_time.'" />'.PHP_EOL;
        }elseif(stristr($cfseo_reading_time,'"')){
            echo "<meta name='twitter:data1' content='".$cfseo_reading_time."' />".PHP_EOL;
        }else{
            echo "<meta name='twitter:data1' content='".$cfseo_reading_time."' />".PHP_EOL;
        }
    }

}


if( !empty( $cfseo_custom_meta_tag ) )
{
    echo $cfseo_custom_meta_tag.PHP_EOL;
}
if(!empty($cfseo_schema_file)){
    
    echo '<script type="application/ld+json">'.$cfseo_schema_file.'</script>'.PHP_EOL;
}

if( !empty( $cfseo_pinterest_verification )){
    echo $cfseo_pinterest_verification.PHP_EOL;
}

?>
<!-- SEO Plugin End -->
<?php
echo PHP_EOL;