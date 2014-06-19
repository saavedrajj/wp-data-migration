<?php
/*
 * This script imports to Wordpress all the stories from the former yourdomain.com
 *
 */
require_once('wp-config.php');
#require_once (ABSPATH . '/assets/jwplayer/jwplayer.js');
//HOLA
/*
 $post = array(
 'ID'             => [ <post id> ] //Are you updating an existing post?
 'menu_order'     => [ <order> ] //If new post is a page, it sets the order in which it should appear in the tabs.
 'comment_status' => [ 'closed' | 'open' ] // 'closed' means no comments.
 'ping_status'    => [ 'closed' | 'open' ] // 'closed' means pingbacks or trackbacks turned off
 'pinged'         => [ ? ] //?
 'post_author'    => [ <user ID> ] //The user ID number of the author.
 'post_category'  => [ array(<category id>, <...>) ] //post_category no longer exists, try wp_set_post_terms() for setting a post's categories
 'post_content'   => [ <the text of the post> ] //The full text of the post.
 'post_date'      => [ Y-m-d H:i:s ] //The time post was made.
 'post_date_gmt'  => [ Y-m-d H:i:s ] //The time post was made, in GMT.
 'post_excerpt'   => [ <an excerpt> ] //For all your post excerpt needs.
 'post_name'      => [ <the name> ] // The name (slug) for your post
 'post_parent'    => [ <post ID> ] //Sets the parent of the new post.
 'post_password'  => [ ? ] //password for post?
 'post_status'    => [ 'draft' | 'publish' | 'pending'| 'future' | 'private' | 'custom_registered_status' ] //Set the status of the new post.
 'post_title'     => [ <the title> ] //The title of your post.
 'post_type'      => [ 'post' | 'page' | 'link' | 'nav_menu_item' | 'custom_post_type' ] //You may want to insert a regular post, page, link, a menu item or some custom post type
 'tags_input'     => [ '<tag>, <tag>, <...>' ] //For tags.
 'to_ping'        => [ ? ] //?
 'tax_input'      => [ array( 'taxonomy_name' => array( 'term', 'term2', 'term3' ) ) ] // support for custom taxonomies.
 );
 *
 * story_id   = field_5243ebac8ccec
 * media_id   = field_5243e3965732a
 * gallery_id = field_5245493167a1e
 * type       = field_5243f1551bb7b
 *
 */

// Check if a remote file exists
function checkRemoteFile($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (curl_exec($ch) !== FALSE) {
        return true;
    } else {
        return false;
    }
}

// Print Array for debug
if (!function_exists("preprint")) {
    function preprint($s, $return = false)
    {
        $x = "<pre>";
        $x .= print_r($s, 1);
        $x .= "</pre>";
        if ($return) return $x;
        else print $x;
    }
}


// set database server access variables:
if ($_SERVER['REMOTE_ADDR'] == '10.10.10.10') {
    $host = "HOST";
    $user = "USER";
    $pwd = "USER";
    $db = "DATABASE";
} else {
    $host = "HOST";
    $user = "USER";
    $pwd = "PASSWORD";
    $db = "DATABASE";
}

// open connection
$connection = mysql_connect($host, $user, $pwd) or die("Unable to connect!");

// select database
mysql_select_db($db) or die("Unable to select database!");

// create query
$query =
    "
    SELECT t2.id, t2.author_id, t2.published_at, t2.title, t2.summary, t2.text, t2.active, t2.media_id, t2.type, t1.section_id
    FROM
    (
    SELECT   id,story_id, section_id, MAX(section_position)
    FROM     story__sections
    GROUP BY story_id
    )
    AS t1,
    stories t2
    WHERE t1.story_id = t2.id
    AND (convert(t2.id,unsigned)  BETWEEN '17320' AND '17320')
    order by t2.id ASC
    ";

$queryTEMP =
    "
    SELECT t2.id, t2.author_id, t2.published_at, t2.title, t2.summary, t2.text, t2.active, t2.media_id, t2.type, t1.section_id
    FROM
    (
    SELECT   id,story_id, section_id, MAX(section_position)
    FROM     story__sections
    GROUP BY story_id
    )
    AS t1,
    stories t2
    WHERE t1.story_id = t2.id
    AND (convert(t2.id,unsigned)  in (

'1925','1904','1675',
'3027','3043','3058','3068',
'3000','3001','3002','3003',
'16962', '16964', '16969', '16971',
'15460', '15729', '15791', '15805',
'9772','9962','10575','11304',
'4032','9071',
 '10837','10869','10919'

    ))
    order by t2.id ASC
    ";


// execute query
$result = mysql_query($query) or die("Error in query: $query. " . mysql_error());

// see if any rows were returned
if (mysql_num_rows($result) > 0) {

    $cont = 0;
    #$cont_galleries = 0;
    while ($row = mysql_fetch_assoc($result)) {
        #echo $row['id']." |1 ". $row['story_id']." |2 " . $row['section_id']."3<br/>";

        // Transformations //
        //author_id
        switch ($row['author_id']) {
            case 1    :
                $row['author_id'] = 3;
                break; // Amy Wall
            case 16   :
                $row['author_id'] = 9;
                break; // Aoibhinn McBride
            case 22   :
                $row['author_id'] = 10;
                break; // Cathy Donohue
            case 15   :
                $row['author_id'] = 11;
                break; // Denise Calnan
            case 11   :
                $row['author_id'] = 12;
                break; // Evanne N Chuilinn
            case 13   :
                $row['author_id'] = 13;
                break; // Fiona McGarry
            case 18   :
                $row['author_id'] = 14;
                break; // Genna Patterson
            case 9    :
                $row['author_id'] = 15;
                break; // Georgina Ahren
            case 21   :
                $row['author_id'] = 16;
                break; // Grace O'Reilly
            case 14   :
                $row['author_id'] = 17;
                break; // Laura Whitmore
            case 10   :
                $row['author_id'] = 18;
                break; // Mairead Farrell
            case 5    :
                $row['author_id'] = 8;
                break; // Michelle McMahon
            case 17   :
                $row['author_id'] = 19;
                break; // Naoimh Wilkins
            case 3    :
                $row['author_id'] = 6;
                break; // Patricia Murphy
            case 2    :
                $row['author_id'] = 5;
                break; // Rebecca NckNight
            case 19   :
                $row['author_id'] = 20;
                break; // Sue Murphy
            case 20   :
                $row['author_id'] = 21;
                break; // Una Kavanagh
            case null :
                $row['author_id'] = 22;
                break; // Test
        }

        //section_id (Categories)
        switch ($row['section_id']) {
            case 1  :
                $row['section_id'] = 37;
                break; // News
            case 2  :
                $row['section_id'] = 2;
                break; // Style
            case 31 :
                $row['section_id'] = 12;
                break; // Style > Celeb Style
            case 70 :
                $row['section_id'] = 13;
                break; // Style > Fashion News
            case 71 :
                $row['section_id'] = 14;
                break; // Style > Trend Spend
            case 72 :
                $row['section_id'] = 15;
                break; // Style > Designer Insider
            case 3  :
                $row['section_id'] = 3;
                break; // Beauty
            case 33 :
                $row['section_id'] = 16;
                break; // Beauty > Hair Affairs
            case 34 :
                $row['section_id'] = 17;
                break; // Beauty > Beauty Buys
            case 35 :
                $row['section_id'] = 18;
                break; // Beauty > Preen & Pamper
            case 4  :
                $row['section_id'] = 4;
                break; // Celeb
            case 37 :
                $row['section_id'] = 19;
                break; // Celeb > News & Gossip
            case 20 :
                $row['section_id'] = 38;
                break; // Celeb > Gossip
            case 38 :
                $row['section_id'] = 39;
                break; // Celeb > In Pictures
            case 54 :
                $row['section_id'] = 40;
                break; // Celeb > We Would Ya!
            case 5  :
                $row['section_id'] = 5;
                break; // Entertainment
            case 21 :
                $row['section_id'] = 33;
                break; // Entertainment > Books
            case 22 :
                $row['section_id'] = 34;
                break; // Entertainment > Music
            case 23 :
                $row['section_id'] = 35;
                break; // Entertainment > TV
            case 25 :
                $row['section_id'] = 36;
                break; // Entertainment > Social Media
            case 79 :
                $row['section_id'] = 41;
                break; // Entertainment > Answer to some companys question
            case 73 :
                $row['section_id'] = 6;
                break; // Movies
            case 6  :
                $row['section_id'] = 7;
                break; // Life
            case 26 :
                $row['section_id'] = 20;
                break; // Life > Food & Drink
            case 28 :
                $row['section_id'] = 21;
                break; // Life > Relationships & Sex
            case 29 :
                $row['section_id'] = 22;
                break; // Life > Career & Money
            case 76 :
                $row['section_id'] = 23;
                break; // Life > Travel
            case 8  :
                $row['section_id'] = 8;
                break; // Family
            case 59 :
                $row['section_id'] = 24;
                break; // Family > Pregnancy, Baby & Toddler
            case 60 :
                $row['section_id'] = 25;
                break; // Family > Children
            case 61 :
                $row['section_id'] = 26;
                break; // Family > Teenagers
            case 63 :
                $row['section_id'] = 27;
                break; // Family > Mums
            case 27 :
                $row['section_id'] = 9;
                break; // Health
            case 64 :
                $row['section_id'] = 28;
                break; // Health > Health News
            case 65 :
                $row['section_id'] = 29;
                break; // Health > Fitness
            case 66 :
                $row['section_id'] = 30;
                break; // Health > Eating Well
            case 67 :
                $row['section_id'] = 31;
                break; // Health > Mind Matters
            case 69 :
                $row['section_id'] = 42;
                break; // Health > Ask The Doctor
            case 74 :
                $row['section_id'] = 10;
                break; // Motors
            case 9  :
                $row['section_id'] = 11;
                break; // Giveaways
            case 77 :
                $row['section_id'] = 43;
                break; // Test
            case 78 :
                $row['section_id'] = 44;
                break; // Placeholder
        }

        //active
        switch ($row['active']) {
            case 0 :
                $row['active'] = 'draft';
                break; // Inactive > draft
            case 1 :
                $row['active'] = 'publish';
                break; // Active > publish
        }

        switch ($row['type']) {
            case 'gallery' :
                echo "stories.id: " . $row['id'] . "<br/>";
                echo "stories.title: " . $row['title'] . "<br/>";
                echo "post_type: " . $row['type'] . "<br/><br/>";
                $text_broken = str_replace("../../../../../uploads/media/", "http://cdn.yourdomain.com/", $row['text']);
                $my_post_gallery = array(
                    'post_title' => $row['title'],
                    'post_content' => $text_broken,
                    'post_status' => $row['active'],
                    'post_date' => $row['published_at'],
                    'post_author' => $row['author_id'],
                    'post_excerpt' => $row['summary'],
                    'post_category' => array($row['section_id'], 45)
                );

                // Insert the post into the database
                $post_gallery_id = wp_insert_post($my_post_gallery);
                preprint($my_post_gallery);

                if ($post_gallery_id == TRUE) {
                    //story_id
                    update_field("field_5243ebac8ccec", $row['id'], $post_gallery_id);
                    //media_id
                    update_field("field_5243e3965732a", $row['media_id'], $post_gallery_id);
                    //type
                    update_field("field_5243f1551bb7b", $row['type'], $post_gallery_id);
                }

                $query_gallery =
                    "
                    SELECT id,gallery_id
                    FROM
                    story__gallery
                    where id=" . $row['id'];
                $result_gallery = mysql_query($query_gallery) or die("Error in query: $query_gallery. " . mysql_error());

                if (mysql_num_rows($result_gallery) > 0) {
                    while ($row_gallery = mysql_fetch_assoc($result_gallery)) {
                        $gallery_id = $row_gallery['gallery_id'];
                        update_field("field_5245493167a1e", $gallery_id, $post_gallery_id);
                    }
                }
                break;

            case 'media' :
                echo "stories.id: " . $row['id'] . "<br/>";
                echo "stories.title: " . $row['title'] . "<br/>";
                echo "post_type: " . $row['type'] . "<br/><br/>";
                $query_media = "
                    SELECT t2.name, t2.provider_name, t2.provider_reference, t2.provider_metadata, t2.width, t2.height
                    FROM
                    media__media t2
                    where
					t2.id=(
                    SELECT t1.story_media_id
                    FROM
                    story__media t1
                    where
					t1.id=" . $row['id'] . ")";

                $result_query_media = mysql_query($query_media) or die("Error in query: $query_media . " . mysql_error());

                if (mysql_num_rows($result_query_media) > 0) {

                    while ($row_query_media = mysql_fetch_assoc($result_query_media)) {
                        echo "provider_name: " . $row_query_media['provider_name'] . "<br/>";
                        echo "provider_reference: " . $row_query_media['provider_reference'] . "<br/>";
                        echo "name: " . $row_query_media['name'] . "<br/>";
                        echo "width: " . $row_query_media['width'] . "<br/>";
                        echo "height: " . $row_query_media['height'] . "<br/>";

                        ////////////////////////////////////////////////////
                        // Show depending kind of media
                        ////////////////////////////////////////////////////
                        switch ($row_query_media['provider_name']) {
                            case 'sonata.media.provider.youtube' :

                                remove_filter('content_save_pre', 'wp_filter_post_kses');
                                remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');

                                $text_temp =
                                    "<div align='center'><iframe width='" . $row_query_media['width'] . "' height='" . $row_query_media['height'] . "' src='//www.youtube.com/embed/" . $row_query_media['provider_reference'] . "' frameborder='0' allowfullscreen></iframe></div><br/>";
                                #echo "<br/>" . $text_temp . "<br/>";
                                $text_broken = str_replace("../../../../../uploads/media/", "http://cdn.yourdomain.com/", $row['text']);
                                $my_post_youtube = array(
                                    'post_title' => $row['title'],
                                    'post_content' => $text_temp . $text_broken,
                                    'post_status' => $row['active'],
                                    'post_date' => $row['published_at'],
                                    'post_author' => $row['author_id'],
                                    'post_excerpt' => $row['summary'],
                                    'post_category' => array($row['section_id'], 46)
                                );

                                // Insert the post into the database
                                $post_youtube_id = wp_insert_post($my_post_youtube);
                                preprint($my_post_youtube);

                                if ($post_youtube_id == TRUE) {
                                    //story_id
                                    update_field("field_5243ebac8ccec", $row['id'], $post_youtube_id);
                                    //media_id
                                    update_field("field_5243e3965732a", $row['media_id'], $post_youtube_id);
                                    //type
                                    update_field("field_5243f1551bb7b", $row['type'], $post_youtube_id);
                                }
                                add_filter('content_save_pre', 'wp_filter_post_kses');
                                add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
                                break 3;
                            case 'sonata.media.provider.vimeo' :

                                remove_filter('content_save_pre', 'wp_filter_post_kses');
                                remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');

                                $vimeo_width = 635;
                                $vimeo_height = 357;
                                $text_temp =
                                    "<div align='center'><iframe src='//player.vimeo.com/video/" . $row_query_media['provider_reference'] . "?title=0&amp;byline=0&amp;portrait=0' width='" . $vimeo_width . "' height='" . $vimeo_height . "' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>";
                                $text_broken = str_replace("../../../../../uploads/media/", "http://cdn.yourdomain.com/", $row['text']);
                                $my_post_vimeo = array(
                                    'post_title' => $row['title'],
                                    'post_content' => $text_temp . $text_broken,
                                    'post_status' => $row['active'],
                                    'post_date' => $row['published_at'],
                                    'post_author' => $row['author_id'],
                                    'post_excerpt' => $row['summary'],
                                    'post_category' => array($row['section_id'], 46)
                                );

                                // Insert the post into the database
                                $post_vimeo_id = wp_insert_post($my_post_vimeo);
                                preprint($my_post_vimeo);

                                if ($post_vimeo_id == TRUE) {
                                    //story_id
                                    update_field("field_5243ebac8ccec", $row['id'], $post_vimeo_id);
                                    //media_id
                                    update_field("field_5243e3965732a", $row['media_id'], $post_vimeo_id);
                                    //type
                                    update_field("field_5243f1551bb7b", $row['type'], $post_vimeo_id);
                                }
                                add_filter('content_save_pre', 'wp_filter_post_kses');
                                add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
                                break 3;
                            case 'joe_video.provider.jwplayer' :
                                remove_filter('content_save_pre', 'wp_filter_post_kses');
                                remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
                                echo "jwplayer" . "<br/>";
                                $path_server = get_bloginfo('url');
                                $path_server = "http://yourdomain.com";
                                #$path_server = "http://localhost:8888/draft.ie";
                                $path_videos = $path_server . "/wp-content/uploads/videos/";
                                $path_videos = $path_server . "/wp-content/uploads/videos/";
                                $video_metadata = $row_query_media['provider_metadata'];

                                echo "video metadata: " . $video_metadata . "<br/>";

                                $temp_video_metadata = substr($video_metadata, 18);
                                $temp_video_metadata01 = substr($temp_video_metadata, 0, -2);
                                $video_metadata = $temp_video_metadata01;

                                $text_temp = "
                                <div align='center'><object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' id='jw-0' name='jw-0' height='" . $row_query_media['height'] . "' width='" . $row_query_media['width'] . "'>
                                    <param name='movie' value='http://yourdomain.com/assets/jwplayer/player.swf'>
                                    <param name='allowfullscreen' value='true'>
                                    <param name='allowscriptaccess' value='always'>
                                    <param name='wmode' value='transparent'>
                                    <param name='flashvars' value='file=" . $path_videos . $row_query_media['provider_reference'] . "&image=" . $path_videos . $video_metadata . "'>
                                    <embed type='application/x-shockwave-flash' id='jw-0-a' name='jw-0-a' src='http://yourdomain.com/assets/jwplayer/player.swf' bgcolor='undefined' allowscriptaccess='always' allowfullscreen='true' wmode='transparent' flashvars='file=" . $path_videos . $row_query_media['provider_reference'] . "&image=" . $path_videos . $video_metadata . "' height='" . $row_query_media['height'] . "' width='" . $row_query_media['width'] . "'>
                                </object></div>";
                                $text_broken = str_replace("../../../../../uploads/media/", "http://cdn.yourdomain.com/", $row['text']);
                                $my_post_jwplayer = array(
                                    'post_title' => $row['title'],
                                    'post_content' => $text_temp . $text_broken,
                                    'post_status' => $row['active'],
                                    'post_date' => $row['published_at'],
                                    'post_author' => $row['author_id'],
                                    'post_excerpt' => $row['summary'],
                                    'post_category' => array($row['section_id'], 46)
                                );

                                // Insert the post into the database
                                $post_jwplayer_id = wp_insert_post($my_post_jwplayer);
                                preprint($my_post_jwplayer);

                                if ($post_jwplayer_id == TRUE) {
                                    //story_id
                                    update_field("field_5243ebac8ccec", $row['id'], $post_jwplayer_id);
                                    //media_id
                                    update_field("field_5243e3965732a", $row['media_id'], $post_jwplayer_id);
                                    //type
                                    update_field("field_5243f1551bb7b", $row['type'], $post_jwplayer_id);
                                }
                                add_filter('content_save_pre', 'wp_filter_post_kses');
                                add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
                                break 3;
                            case 'sonata.media.provider.dailymotion' :
                                remove_filter('content_save_pre', 'wp_filter_post_kses');
                                remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
                                $text_temp =
                                    "<div align='center'><iframe frameborder='0' width='" . $row_query_media['width'] . "' height='" . $row_query_media['height'] . "' src='http://www.dailymotion.com/embed/video/" . $row_query_media['provider_reference'] . "'></iframe></div>";
                                $text_broken = str_replace("../../../../../uploads/media/", "http://cdn.yourdomain.com/", $row['text']);
                                $my_post_dailymotion = array(
                                    'post_title' => $row['title'],
                                    'post_content' => $text_temp . $text_broken,
                                    'post_status' => $row['active'],
                                    'post_date' => $row['published_at'],
                                    'post_author' => $row['author_id'],
                                    'post_excerpt' => $row['summary'],
                                    'post_category' => array($row['section_id'], 46)
                                );

                                // Insert the post into the database
                                $post_dailymotion_id = wp_insert_post($my_post_dailymotion);
                                preprint($my_post_dailymotion);

                                if ($post_dailymotion_id == TRUE) {
                                    //story_id
                                    update_field("field_5243ebac8ccec", $row['id'], $post_dailymotion_id);
                                    //media_id
                                    update_field("field_5243e3965732a", $row['media_id'], $post_dailymotionid);
                                    //type
                                    update_field("field_5243f1551bb7b", $row['type'], $post_dailymotion_id);
                                }
                                add_filter('content_save_pre', 'wp_filter_post_kses');
                                add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
                                break 3;

                            case 'sonata.media.provider.image' :
                                remove_filter('content_save_pre', 'wp_filter_post_kses');
                                remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
                                echo "image" . "<br/>";
                                if ($_SERVER['REMOTE_ADDR'] == '10.190.254.11') {
                                    $story_url = "http://cdn.yourdomain.com/story/0001/";
                                    $default_url = "http://cdn.yourdomain.com/default/0001/";
                                    $video_url = "http://cdn.yourdomain.com/video/0001/";
                                    $author_url = "http://cdn.yourdomain.com/author/0001/";
                                } else {
                                    $story_url = "http://localhost:8888/yourdomain.com/wp-content/uploads/cdn/story/0001/";
                                    $default_url = "http://localhost:8888/yourdomain.com/wp-content/uploads/cdn/default/0001/";
                                    $video_url = "http://localhost:8888/yourdomain.com/wp-content/uploads/cdn/video/0001/";
                                    $author_url = "http://localhost:8888/yourdomain.com/wp-content/uploads/cdn/author/0001/";
                                }

                                // Loop to find the image related to post

                                $i = 1;
                                while ($i <= 70) {
                                    $subdirectory = $i;
                                    switch ($subdirectory) {
                                        case 0 :
                                            $subdirectory = "00";
                                            break;
                                        case 1 :
                                            $subdirectory = "01";
                                            break;
                                        case 2 :
                                            $subdirectory = "02";
                                            break;
                                        case 3 :
                                            $subdirectory = "03";
                                            break;
                                        case 4 :
                                            $subdirectory = "04";
                                            break;
                                        case 5 :
                                            $subdirectory = "05";
                                            break;
                                        case 6 :
                                            $subdirectory = "06";
                                            break;
                                        case 7 :
                                            $subdirectory = "07";
                                            break;
                                        case 8 :
                                            $subdirectory = "08";
                                            break;
                                        case 9 :
                                            $subdirectory = "09";
                                            break;
                                    }

                                    $url_to_validate01 = $story_url . $subdirectory . "/" . $row_query_media['provider_reference'];
                                    $url_to_validate02 = $default_url . $subdirectory . "/" . $row_query_media['provider_reference'];
                                    $check_image01 = checkRemoteFile($url_to_validate01);
                                    $check_image02 = checkRemoteFile($url_to_validate02);

                                    if ($check_image01 == true) {
                                        echo "Image found in story directory<br/><br/>";
                                        $url_to_validate = $url_to_validate01;
                                        break 1;
                                    }
                                    if ($check_image02 == true) {
                                        echo "Image found in default directory<br/><br/>";
                                        $url_to_validate = $url_to_validate02;
                                        break 1;
                                    }
                                    $i++;
                                }
                                $image_url = $url_to_validate;
                                list($image_url_width, $image_url_height) = getimagesize($image_url);
                                echo "image_url_width: " . $image_url_width . "<br/>";
                                echo "image_url_height: " . $image_url_height . "<br/>";

                                if ($image_url_width >= 635) {
                                    echo "mayor<br/>";
                                    $text_temp = "<div align='center'><img src=" . $image_url . " width='625'></div>";
                                } else {
                                    echo "menor<br/>";
                                    $text_temp = "<div align='center'><img src=" . $image_url . " width='" . $image_url_width . "'></div>";
                                }

                                $text_broken = str_replace("../../../../../uploads/media/", "http://cdn.yourdomain.com/", $row['text']);
                                $my_post_image = array(
                                    'post_title' => $row['title'],
                                    'post_content' => $text_temp . $text_broken,
                                    'post_status' => $row['active'],
                                    'post_date' => $row['published_at'],
                                    'post_author' => $row['author_id'],
                                    'post_excerpt' => $row['summary'],
                                    'post_category' => array($row['section_id'])
                                );

                                // Insert the post into the database
                                $post_image_id = wp_insert_post($my_post_image);
                                preprint($my_post_image);

                                if ($post_image_id == TRUE) {
                                    //story_id
                                    update_field("field_5243ebac8ccec", $row['id'], $post_image_id);
                                    //media_id
                                    update_field("field_5243e3965732a", $row['media_id'], $post_image_id);
                                    //type
                                    update_field("field_5243f1551bb7b", $row['type'], $post_image_id);

                                    //Erase original file on disk
                                    $image_to_delete = str_replace("http://localhost:8888/draft.ie/", "", $image_url);
                                    echo $image_to_delete;
                                    #unlink($image_to_delete);
                                }
                                add_filter('content_save_pre', 'wp_filter_post_kses');
                                add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
                                break 3;
                        }
                    }
                }

            default:
                // All categories posts including Competitions (Giveaway)
                echo "stories.id: " . $row['id'] . "<br/>";
                echo "stories.title: " . $row['title'] . "<br/>";
                echo "post_type: " . $row['type'] . "<br/><br/>";
                $text_broken = str_replace("../../../../../uploads/media/", "http://cdn.yourdomain.com/", $row['text']);
                $my_post_story = array(
                    'post_title' => $row['title'],
                    'post_content' => $text_broken,
                    'post_status' => $row['active'],
                    'post_date' => $row['published_at'],
                    'post_author' => $row['author_id'],
                    'post_excerpt' => $row['summary'],
                    'post_category' => array($row['section_id'])
                );
                // Insert the post into the database
                $post_story_id = wp_insert_post($my_post_story);
                preprint($my_post_story);

                if ($post_story_id == TRUE) {
                    //story_id
                    update_field("field_5243ebac8ccec", $row['id'], $post_story_id);
                    //media_id
                    update_field("field_5243e3965732a", $row['media_id'], $post_story_id);
                    //type
                    update_field("field_5243f1551bb7b", $row['type'], $post_story_id);
                }

        }
        echo "<hr/>";
        $cont++;
    }
    echo "<br/><br/>counter: " . $cont . "<br/>";
    #unset($my_post_gallery, $my_post_story, $my_post_image, $my_post_youtube, $my_post_vimeo, $my_post_jwplayer, $my_post_dailymotion);
} else {
    // print status message
    echo "No rows found!";
}

mysql_close($connection);
?>