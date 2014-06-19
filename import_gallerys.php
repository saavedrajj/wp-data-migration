<?php
/*
 * This script imports to Wordpress all the posts gallerys from her.ie
 *
 */
require_once('wp-config.php');

/*
 * story_id = field_5243ebac8ccec
 * media_id = field_5243e3965732a
 * gallery_id = field_5245493167a1e
 * gallery_images = field_524abf9cadfd1
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

// Set database server access variables:
if ($_SERVER['REMOTE_ADDR'] == '10.190.254.11') {
    $host = "36cecfd7206f8f4cc7f556a30f11588edd1e4025.rackspaceclouddb.com";
    $user = "herdotIEuser";
    $pwd = "aafpjKEmL1tZUojg";
    $db = "herdotIEdata";
} else {
    $host = "localhost";
    $user = "root";
    $pwd = "root";
    $db = "her";
}

// open connection
$connection = mysql_connect($host, $user, $pwd) or die("Unable to connect!");

mysql_select_db($db) or die("Unable to select database!");
# UNTIL HERE: 17319
$query01 =
    "SELECT id, gallery_id
    FROM story__gallery
    WHERE gallery_id NOT IN ('NULL')
    AND  (convert(id,unsigned)  BETWEEN '17201' AND '17319')
    ORDER BY id
    ";

// execute query
$result01 = mysql_query($query01) or die("Error in query: $query01. " . mysql_error());

// see if any rows were returned
if (mysql_num_rows($result01) > 0) {

    $cont = 0;

    while ($row01 = mysql_fetch_assoc($result01)) {
        echo "<strong>stories.id</strong> => " . $row01['id'] . "<br/>";

        $query01a = "
		SELECT id, title
		from stories
		where id=" . $row01['id'];

        $result01a = mysql_query($query01a) or die("Error in query: $query01a. " . mysql_error());

        // see if any rows were returned
        if (mysql_num_rows($result01a) > 0) {
            while ($row01a = mysql_fetch_assoc($result01a)) {
                echo "<strong>stories.title</strong> => " . $row01a['title'] . "<br/>";
                #$titulo = $row01a['title'];
            }
        }

        $query01c = "
        SELECT ID, post_title, post_date,
        convert(m1.meta_value,unsigned) AS 'meta_value_story_id',
        convert(m2.meta_value,unsigned)  AS 'meta_value_media_id'
        FROM wp_posts
        INNER JOIN wp_postmeta m1
          ON ( wp_posts.ID = m1.post_id)
        INNER JOIN wp_postmeta m2
          ON ( wp_posts.ID = m2.post_id )
        WHERE
        wp_posts.post_type = 'post'
        AND  (convert(m1.meta_value,unsigned)  = " . $row01['id'] . " )
        AND ( m1.meta_key = 'story_id' )
        AND ( m2.meta_key = 'media_id' )
        GROUP BY wp_posts.ID
        ORDER BY wp_posts.ID ASC";

        $result01c = mysql_query($query01c) or die("Error in query: $query01c. " . mysql_error());

        if (mysql_num_rows($result01c) > 0) {
            while ($row01c = mysql_fetch_assoc($result01c)) {
                echo "<strong>wp_posts.id</strong> => " . $row01c['ID'] . "<br/>";
                #echo "<strong>wp_posts.title</strong> => " . $row01c['post_title'] . "<br/>";
                $post_date = $row01c['post_date'];
                $current_post = $row01c['ID'];
            }
        }

        echo "<strong>gallery_id</strong> => " . $row01['gallery_id'] . "<br/>";

        $gallery01 = $row01['gallery_id'];
        $query02 =
            "
            SELECT id, gallery_id, media_id, position
            FROM media__gallery_media
            WHERE gallery_id=" . $gallery01 . "
            ";

        $result02 = mysql_query($query02) or die("Error in query: $query02. " . mysql_error());
        $array_gallery_field = array();
        $count_gallery_field = 0;
        if (mysql_num_rows($result02) > 0) {
            while ($row02 = mysql_fetch_assoc($result02)) {
                echo "media_id => " . $row02['media_id'] . "<br/>";

                $query3 = "
			    select *
			    from media__media
			    where id=	    			
			    " . $row02['media_id'];

                // execute query
                $result3 = mysql_query($query3) or die("Error in query: $query3. " . mysql_error());

                if (mysql_num_rows($result3) > 0) {

                    while ($row3 = mysql_fetch_assoc($result3)) {

                        if ($_SERVER['REMOTE_ADDR'] == '10.190.254.11') {
                            $story_url = "http://cdn.her.ie/story/0001/";
                            $default_url = "http://cdn.her.ie/default/0001/";
                            $video_url = "http://cdn.her.ie/video/0001/";
                            $author_url = "http://cdn.her.ie/author/0001/";
                        } else {
                            #$story_url = "http://localhost:8888/cdn/story/0001/";
                            #$default_url = "http://localhost:8888/cdn/default/0001/";
                            #$video_url = "http://localhost:8888/cdn/video/0001/";
                            #$author_url = "http://localhost:8888/cdn/author/0001/";
                            $story_url = "http://localhost:8888/her.ie/wp-content/uploads/cdn/story/0001/";
                            $default_url = "http://localhost:8888/her.ie/wp-content/uploads/cdn/default/0001/";
                            $video_url = "http://localhost:8888/her.ie/wp-content/uploads/cdn/video/0001/";
                            $author_url = "http://localhost:8888/her.ie/wp-content/uploads/cdn/author/0001/";
                        }
                        $image_name_with_extension = $row3['name'];
                        $image_description = $row3['description'];

                        // Loop to find the image related to post
                        $i = 60;
                        while ($i <= 61) {
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
                            $url_to_validate01 = $story_url . $subdirectory . "/" . $row3['provider_reference'];
                            $url_to_validate02 = $default_url . $subdirectory . "/" . $row3['provider_reference'];
                            $check_image01 = checkRemoteFile($url_to_validate01);
                            $check_image02 = checkRemoteFile($url_to_validate02);
                            $image_description = $row3['description'];

                            if ($check_image01 == true) {
                                $url_to_validate = $url_to_validate01;
                                break 1;
                            }
                            if ($check_image02 == true) {
                                $url_to_validate = $url_to_validate02;
                                break 1;
                            }
                            $i++;
                        }


                        $image_url = $url_to_validate;


                        if ($image_url == TRUE) {
                            echo "count_gallery: " . $count_gallery_field . "<br/>";
                            echo "post date: " . $post_date . "<br/>";
                            // Add Featured Image to Post

                            $upload_dir = wp_upload_dir($post_date); // Set upload folder
                            $image_data = file_get_contents($image_url); // Get image data
                            $filename = basename($image_url); // Create image file name

                            // Check folder permission and define file location
                            if (wp_mkdir_p($upload_dir['path'])) {
                                $file = $upload_dir['path'] . '/' . $filename;
                            } else {
                                $file = $upload_dir['basedir'] . '/' . $filename;
                            }

                            // Create the image  file on the server
                            file_put_contents($file, $image_data);

                            // Check image file type
                            $wp_filetype = wp_check_filetype($filename, null);

                            $post_id = $current_post;
                            // Remove extension from image title
                            $image_name = preg_replace("/\\.[^.\\s]{3,4}$/", "", $image_name_with_extension);

                            // Set attachment data
                            $attachment = array(
                                'post_mime_type' => $wp_filetype['type'],
                                'post_title' => $image_name,
                                'post_content' => $image_description,
                                'post_excerpt' => $image_description,
                                'post_parent' => $post_id,
                                'post_status' => 'inherit',

                            );
                            preprint($attachment);
                            preprint($image_url);
                            preprint($file);

                            // Create the attachment
                            $attach_id = wp_insert_attachment($attachment, $file, $post_id);

                            $array_gallery_field[$count_gallery_field] = $attach_id;

                            // Include image.php
                            require_once(ABSPATH . 'wp-admin/includes/image.php');

                            // Define attachment metadata
                            $attach_data = wp_generate_attachment_metadata($attach_id, $file);

                            // Assign metadata to attachment
                            wp_update_attachment_metadata($attach_id, $attach_data);

                            $count_gallery_field++;
                        } else {
                            echo "&nbsp;";
                        }
                        echo "<img src='" . $image_url . "' width=100><br/>";
                    }
                } else {
                    echo "No rows for result03<br/>";
                }
            }
        } else {
            echo "No rows for result02<br/>";
        }
        preprint($array_gallery_field);
        update_field("field_524abf9cadfd1", $array_gallery_field, $post_id);
        echo "END OF POST<br/>";
        # FUNCTION TO DELETE ?
        echo "<hr/>";
        $cont++;
    }
    echo "<br/><strong>TOTAL GALLERYS</strong>: " . $cont . "<br/>";
    unset($my_post, $attachment);
} else {
    // No results
    echo "No rows found!";
}
mysql_close($connection);
?>