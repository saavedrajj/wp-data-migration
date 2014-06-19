<?php
/*
 * This script imports to Wordpress all the Featured images to its posts
 * 
 */
require_once('wp-config.php');

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
if ($_SERVER['REMOTE_ADDR'] == '10.10.01.10') {
    $host = "HOST";
    $user = "USER";
    $pwd = "PASSWORD";
    $db = "DATABASE";
} else {
    $host = "HOST";
    $user = "USER";
    $pwd = "PASSWORD";
    $db = "DATABASE";
}

// open connection
$connection = mysql_connect($host, $user, $pwd) or die("Unable to connect!");

mysql_select_db($db) or die("Unable to select database!");

$query = "
SELECT ID, post_title, post_date,
convert(m1.meta_value,unsigned) AS 'meta_value_story_id',
convert(m2.meta_value,unsigned)  AS 'meta_value_media_id',
m3.meta_value AS 'meta_value_type'
FROM wp_posts
INNER JOIN wp_postmeta m1
  ON ( wp_posts.ID = m1.post_id)
INNER JOIN wp_postmeta m2
  ON ( wp_posts.ID = m2.post_id )
INNER JOIN wp_postmeta m3
  ON ( wp_posts.ID = m3.post_id )
WHERE
wp_posts.post_type = 'post'
AND  (convert(m1.meta_value,unsigned)  BETWEEN '17201' AND '17319')
AND ( m1.meta_key = 'story_id' )
AND ( m2.meta_key = 'media_id' )
AND ( m3.meta_key = 'type' )
GROUP BY wp_posts.ID
ORDER BY wp_posts.ID ASC
";
#final: 17319
// execute query
$result = mysql_query($query) or die("Error in query: $query. " . mysql_error());

// see if any rows were returned
if (mysql_num_rows($result) > 0) {

    $cont = 0;
    while ($row = mysql_fetch_assoc($result)) {

        $my_post = array(
            'id' => $row['ID'],
            'post_author' => $row['post_author'],
            'post_title' => $row['post_title'],
            'post_date' => $row['post_date'],
            'story_id' => $row['meta_value_story_id'],
            'media_id' => $row['meta_value_media_id'],
            'type' => $row['meta_value_type']
        );
        #preprint($my_post);

        $query2 = "
	    select *
	    from media__media
	    where id=	    			
	    " . $row['meta_value_media_id'];

        // execute query
        $result2 = mysql_query($query2) or die("Error in query: $query2. " . mysql_error());

        if (mysql_num_rows($result2) > 0) {

            while ($row2 = mysql_fetch_assoc($result2)) {
                echo "<strong>STORY_ID</strong> => " . $row['meta_value_story_id'] . "<br/>";
                echo "wp_posts.id => " . $row['ID'] . "<br/>";
                echo "wp_posts.posts_title: " . $row['post_title'] . "<br/>";
                echo "wp_posts.type: " . $row['meta_value_type'] . "<br/>";
                echo "wp_posts.posts_date: " . $row['post_date'] . "<br/>";
                $image_name_with_extension = $row2['name'];
                $image_description = $row2['description'];
                echo "media__media.name: " . $image_name . "<br/>";
                echo "media__media.description: " . $image_description . "<br/>";

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
                #$i = 1;
                $i = 1;
                while ($i <= 70) {
                    $subdirectory = $i;
                    switch ($subdirectory) {
                        case 0 : $subdirectory = "00"; break;
                        case 1 : $subdirectory = "01"; break;
                        case 2 : $subdirectory = "02"; break;
                        case 3 : $subdirectory = "03"; break;
                        case 4 : $subdirectory = "04"; break;
                        case 5 : $subdirectory = "05"; break;
                        case 6 : $subdirectory = "06"; break;
                        case 7 : $subdirectory = "07"; break;
                        case 8 : $subdirectory = "08"; break;
                        case 9 : $subdirectory = "09"; break;
                    }

                    $url_to_validate01 = $story_url . $subdirectory . "/" . $row2['provider_reference'];
                    $url_to_validate02 = $default_url . $subdirectory . "/" . $row2['provider_reference'];
                    #echo "url to validate 01: ".$url_to_validate01."<br/>";
                    #echo "url to validate 02: ".$url_to_validate02."<br/>";
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

                if ($image_url == TRUE) {

                    // Add Featured Image to Post
                    $upload_dir = wp_upload_dir($row['post_date']); // Set upload folder
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

                    // Remove extension from image title
                    $image_name = preg_replace("/\\.[^.\\s]{3,4}$/", "", $image_name_with_extension);

                    // Set attachment data
                    $attachment = array(
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title' => $image_name,
                        #'post_title'     => sanitize_file_name( $filename ),
                        'post_content' => $image_description,
                        'post_excerpt' => $image_description,
                        'post_status' => 'inherit',
                    );

                    $post_id = $row['ID'];
                    // Create the attachment
                    $attach_id = wp_insert_attachment($attachment, $file, $post_id);
                    // Include image.php
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    // Define attachment metadata
                    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
                    // Assign metadata to attachment
                    wp_update_attachment_metadata($attach_id, $attach_data);
                    // And finally assign featured image to post
                    set_post_thumbnail($post_id, $attach_id);
                } else {
                    echo "There is no featured image<br/>";
                }
                $image_attributes = wp_get_attachment_image_src($attach_id); // returns an array

                // Show image uploaded
                echo "<img src='" . $image_attributes[0] . "' width=100><br/><br/>";

                //Erase original file on disk
                $image_to_delete = str_replace("http://localhost:8888/yourdomain.com/", "", $image_url);
                #echo $image_to_delete;
                echo "<hr/><br/>";
                #unlink($image_to_delete);

                //Free memory
                unset($$attachment);
            }
        }
        $cont++;
    }
    unset($my_post);
    echo "<strong>TOTAL POSTS</strong>: " . $cont . "<br/>";
} else {
    // Print status message
    echo "No rows found!";
}

mysql_close($connection);

?>