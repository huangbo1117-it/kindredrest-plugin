#!/usr/bin/php -q
<?php
include '../../../wp-load.php';
global $wpdb;

function log_var($tmp_log, $mode = 0) {
    global $filename;
    if (isset($filename)) {
        $fname = './log/cron_log2_' . $filename;
    } else {
        $fname = './log/cron_log2';
    }

    if ($mode == 0) {
        if (is_string($tmp_log)) {
            echo $tmp_log;
        } else if (is_array($tmp_log)) {
            print_r($tmp_log);
        } else {
            var_dump($tmp_log);
        }
        $myfile = file_put_contents($fname, $tmp_log . PHP_EOL, FILE_APPEND | LOCK_EX);
    } else if ($mode == 1) {
        $myfile = file_put_contents($fname, $tmp_log . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

$op_cron = get_option('kindredrest_cronjob');
if ($op_cron['crond_is_valid'] != 1) {
    $tmp_log = "crond_is_valid Exit " . date("D M d, Y G:i:s");
    log_var($tmp_log);
    exit();
}
if (isset($_REQUEST['file'])) {
    $filename = $_REQUEST['file'];
} else {
    $tmp_log = "file not set Exit " . date("D M d, Y G:i:s");
    log_var($tmp_log);
    exit();
}

$option = get_option('kindredrest_plugin');
$apiKey = $option['api_key'];
$access_token = $option['api_secret'];
$mesmo_id = $option['mesmo_id'];
$mesmo_code = $option['mesmo_code'];
$mesmo_path = "https://mesmo.co/api_sig/data/user" . "$mesmo_id/";

$tmp_log = $mesmo_path . "kindred/$filename";
log_var("filename_path " . $tmp_log);

$file = file_get_contents($mesmo_path . "kindred/$filename");
$file_data = unserialize($file);

$proeprtiesData = $wpdb->get_results("SELECT * FROM property_ids");
$propIDArr = array();
$allIDArr = array();
if (!empty($proeprtiesData)) {
    foreach ($proeprtiesData as $proeprtiesID) {
        $propertyListingtype = $proeprtiesID->type;

        $allIDArr[] = $proeprtiesID->property_id;
    }
}
try {
    foreach ($file_data->properties as $key => $i_property) {
        $id = $i_property->id;
        $tmp_log = $id . " ";
        log_var($tmp_log);
        if (in_array($id, $allIDArr)) {
            $propIDArr[] = $id;
            //$data = json_decode($result);

            print_r($i_property);
            if (!empty($i_property)) {
                $wp_upload_dir = wp_upload_dir();
                $uploadFolderPath = str_replace(home_url(), '', $wp_upload_dir['url']);

                $title = $i_property->heading;
                if ($proeprtiesID->type == 'sale') {
                    $price = $i_property->pricetext;
                } else {
                    $price = $i_property->pricefrom;
                }

                //print_r($i_property);

                $curPropID = $i_property->id;
                $sqlid = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key ='post_exist' AND meta_value ='$curPropID'");

                $poststatus = $sqlid[0]->meta_value;

                /* if($poststatus == 17982976)
                  { */
                if (!empty($sqlid[0]->post_id)) {
                    $post_id = $sqlid[0]->post_id;
                }

                global $wpdb;
                /* Create post object */

                if ($poststatus == $i_property->id) {
                    if (!empty($i_property->heading)) {
                        $title = $i_property->heading;
                    } else {
                        if (get_the_title($post_id) == 'No Name') {
                            $title = $i_property->displayaddress;
                        }
                    }

                    wp_update_post(
                            array(
                                'ID' => $post_id,
                                'post_author' => '1',
                                'post_title' => $title,
                                'post_content' => $i_property->description,
                                'edit_date' => true,
                                'post_status' => 'publish',
                                'post_type' => 'property',
                            )
                    );

                    $tmp_log = "<br>Updated - " . $post_id . " - " . $proeprtiesID->type . " - " . $i_property->id . "<br>";
                    log_var($tmp_log);
                } else {
                    if (!empty($i_property->heading)) {
                        $title = $i_property->heading;
                    } else {
                        $title = $i_property->displayaddress;
                    }

                    $post = array(
                        'post_author' => '1',
                        'post_content' => $i_property->description,
                        'post_status' => 'publish',
                        'post_title' => $title,
                        'post_type' => 'property',
                    );

                    /* Create post */
                    $post_id = wp_insert_post($post, $wp_error);
                    add_post_meta($post_id, 'post_exist', $i_property->id, true);

                    if ($post_id == 0) {
                        $wpdb->query("INSERT INTO prop_not_inserted(prop_id) VALUES('$i_property->id')");
                        $tmp_log = "<br>Not Inserted - " . $post_id . " - " . $propertyListingtype . " - " . $i_property->id . "<br>";
                        log_var($tmp_log);
                    } else {
                        $tmp_log = "<br>Inserted - " . $post_id . " - " . $propertyListingtype . " - " . $i_property->id . "<br>";
                        log_var($tmp_log);
                    }
                }

                if ($post_id != "0") {
                    if (!term_exists($i_property->classification)) {
                        wp_insert_term(
                                $i_property->classification, 'property-types'
                        );
                        wp_set_object_terms($post_id, $i_property->classification, 'property-types');
                    } else {
                        wp_set_object_terms($post_id, $i_property->classification, 'property-types');
                    }

                    if (!term_exists($i_property->listingtype)) {
                        wp_insert_term(
                                $i_property->listingtype, 'property-status'
                        );
                        wp_set_object_terms($post_id, $i_property->listingtype, 'property-status');
                    } else {
                        wp_set_object_terms($post_id, $i_property->listingtype, 'property-status');
                    }

                    if (!empty($i_property->agent->imageurl)) {
                        $agentimageLink = $i_property->agent->imageurl;
                    }
                    if (!term_exists($i_property->agent->firstname . " " . $i_property->agent->lastname)) {
                        $cat_id = wp_insert_term(
                                $i_property->agent->firstname . " " . $i_property->agent->lastname, 'property-agents'
                        );
                        $a = wp_set_object_terms($post_id, $i_property->agent->firstname . " " . $i_property->agent->lastname, 'property-agents');

                        $agent_id = $cat_id['term_taxonomy_id'];

                        update_field('agent_id', $i_property->agent->id, 'property-agents_' . $agent_id);
                        if (!empty($i_property->agent->email)) {
                            update_field('agent_email', $i_property->agent->email, 'property-agents_' . $agent_id);
                        }
                        if (!empty($i_property->agent->mobile)) {
                            update_field('agent_mobile', $i_property->agent->mobile, 'property-agents_' . $agent_id);
                        }
                        if (!empty($i_property->agent->mobiledisplay)) {
                            update_field('agent_mobile_display', $i_property->agent->mobiledisplay, 'property-agents_'
                                    . $agent_id);
                        }
                        if (!empty($i_property->agent->telephone)) {
                            update_field('agent_telephone', $i_property->agent->telephone, 'property-agents_' . $agent_id);
                        }

                        if (!empty($i_property->agent->fax)) {
                            update_field('agent_fax', $i_property->agent->fax, 'property-agents_' . $agent_id);
                        }
                    } else {
                        $a = wp_set_object_terms($post_id, $i_property->agent->firstname . " " . $i_property->agent->lastname, 'property-agents');

                        $agent_id = $a[0];
                        update_field('agent_id', $i_property->agent->id, 'property-agents_' . $agent_id);

                        if (!empty($i_property->agent->email)) {
                            update_field('agent_email', $i_property->agent->email, 'property-agents_' . $agent_id);
                        }
                        if (!empty($i_property->agent->mobile)) {
                            update_field('agent_mobile', $i_property->agent->mobile, 'property-agents_' . $agent_id);
                        }
                        if (!empty($i_property->agent->mobiledisplay)) {
                            update_field('agent_mobile_display', $i_property->agent->mobiledisplay, 'property-agents_'
                                    . $agent_id);
                        }
                        if (!empty($i_property->agent->telephone)) {
                            update_field('agent_telephone', $i_property->agent->telephone, 'property-agents_' . $agent_id);
                        }

                        if (!empty($i_property->agent->fax)) {
                            update_field('agent_fax', $i_property->agent->fax, 'property-agents_' . $agent_id);
                        }
                    }

                    if (!empty($i_property->agent->imageurl)) {
                        update_field('profile_image_url', $agentimageLink, 'property-agents_' . $agent_id);
                    }
                }



                $filesCount = count($i_property->files);

                $filecounter = 0;

                $propCat = $i_property->classification;


                $imagesCount = count($i_property->images) - 1;


                if (!empty($i_property->floorplans)) {
                    $fl = 0;
                    foreach ($i_property->floorplans as $floorplan) {
                        update_field('floor_plans_' . $fl . '_floorplan', $floorplan->url, $post_id);
                        $fl++;
                    }
                }

                update_field('floor_plans', count($i_property->floorplans), $post_id);
                $floorcount = count($i_property->floorplans);
                $wpdb->query("UPDATE {$wpdb->prefix}postmeta SET meta_value = '$floorcount' WHERE post_id= '$post_id' AND meta_key='floor_plans'");

                $address = '';
                if (!empty($i_property->address->unitnum)) {
                    $address .= $i_property->address->unitnum . "/";
                }

                $address .= $i_property->address->streetnum . " " . $i_property->address->street . ", " . $i_property->address->suburb->name . ", " . $i_property->address->suburb->state->abbreviation . ", " . $i_property->address->suburb->country . " " . $i_property->address->suburb->postcode;

                $thedata = Array(
                    'address' => $address,
                    'lat' => $i_property->address->suburb->latitude,
                    'lng' => $i_property->address->suburb->longitude,
                );

                update_field('address_map', $thedata, $post_id);
                update_field('display_address', $i_property->displayaddress, $post_id);
                update_field('price', $price, $post_id);

                update_field('bedrooms', $i_property->bedrooms, $post_id);
                update_field('bathrooms', $i_property->bathrooms, $post_id);
                update_field('toilets', $i_property->toilets, $post_id);
                update_field('garages', $i_property->garages, $post_id);
                update_field('carports', $i_property->carports, $post_id);

                update_field('landarea', $i_property->landarea . $i_property->landareatype, $post_id);
                update_field('building_area', $i_property->buildingarea . $i_property->buildingareatype, $post_id);

                update_field('auction_venue', $i_property->auctionvenue, $post_id);
                update_field('auction_date', $i_property->auctiondate, $post_id);
                update_field('display_address', $i_property->displayaddress, $post_id);

                update_field('video_url', $i_property->videourl, $post_id);


                update_field('water_rates', $i_property->rates->water->amount . " per " . $i_property->rates->water->per, $post_id);
                update_field('council_rates', $i_property->rates->council->amount . " per " . $i_property->rates->strata->per, $post_id);
                update_field('strata_levies', $i_property->rates->strata->amount . " per " . $i_property->rates->strata->per, $post_id);

                $imgCounter = 0;

                $attachImages = array();



                if ($poststatus == $i_property->id) {
                    
                } else {
                    if (!empty($i_property->images) && count($i_property->images)) {
                        $attachImages = array();
                        foreach ($i_property->images as $image) {
                            $imageLink = $image->url;

                            if ($imgCounter == 0) {

                                $imageName = explode('/', $imageLink);
                                $curImageName = $imageName[count($imageName) - 1];
                                $content = file_get_contents($imageLink);
                                $imagepath = $_SERVER['DOCUMENT_ROOT'] . $uploadFolderPath . '/' . $curImageName;

                                $filetype = wp_check_filetype(basename($imagepath), null);

                                if (file_exists($imagepath)) {
                                    $attach_id = pippin_get_image_id(home_url() . $uploadFolderPath . '/' . $curImageName);

                                    set_post_thumbnail($post_id, $attach_id);
                                } else {
                                    file_put_contents($imagepath, $content);


                                    // Prepare an array of post data for the attachment.
                                    $attachment1 = array(
                                        'guid' => $wp_upload_dir['url'] . '/' . basename($imagepath),
                                        'post_mime_type' => $filetype['type'],
                                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($imagepath)),
                                        'post_content' => '',
                                        'post_author' => '1',
                                        'post_status' => 'inherit'
                                    );

                                    // Insert the attachment.
                                    $attach_id = wp_insert_attachment($attachment1, $imagepath, '');

                                    // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                                    require_once( ABSPATH . 'wp-admin/includes/image.php' );

                                    // Generate the metadata for the attachment, and update the database record.
                                    $attach_data = wp_generate_attachment_metadata($attach_id, $imagepath);
                                    wp_update_attachment_metadata($attach_id, $attach_data);

                                    //update_post_meta('_thumbnail_id',$attach_id,$post_id);
                                    set_post_thumbnail($post_id, $attach_id);
                                }

                                update_field('featured_image', $imageLink, $post_id);
                            } else {
                                $attachImages[] = $imageLink;
                            }

                            $imgCounter++;
                        }

                        if (!empty($attachImages)) {
                            for ($i = 0; $i < count($attachImages); $i++) {
                                update_field('gallert_images_links_' . $i . '_image_url', $attachImages[$i], $post_id);
                            }
                        }
                    }

                    $chkGallery = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key='gallert_images_links' AND post_id= '$post_id'");

                    if (!empty($chkGallery)) {
                        $wpdb->query("UPDATE {$wpdb->prefix}postmeta SET meta_value = '$imagesCount' WHERE post_id= '$post_id' AND meta_key='gallert_images_links'");
                    } else {
                        $wpdb->query("INSERT INTO {$wpdb->prefix}postmeta (post_id, meta_key, meta_value) VALUES('$post_id','gallert_images_links','$imagesCount')");
                    }
                }
            } else {
                //echo "No Property";
//            $tmp_log = "";
//            log_var($tmp_log);
            }
        }
    }

    print_r("Array of properties received from API <br/>");
    print_r($propIDArr);
    print_r("<br/>");
    print_r("DELETED RECORDS<br/>");
    if (!empty($propIDArr)) {
        $counter = 1;
        $args = array(
            'post_type' => 'property',
            'post_status' => 'publish',
            'meta_key' => 'post_exist',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'post_exist',
                    'value' => $propIDArr,
                    'compare' => 'NOT IN',
                ),
            )
        );

        $the_query = new WP_Query($args);
        if ($the_query->have_posts()) :
            while ($the_query->have_posts()) : $the_query->the_post();
                $tmp_log = '<br> ' . $counter . '.) ' . get_the_ID() . ' -- ' . get_post_meta(get_the_ID(), 'post_exist', true) . ' -- ' . get_post_meta(get_the_ID(), 'display_address', true) . '<br>';
                log_var($tmp_log);
                wp_trash_post(get_the_ID());
                $counter++;
            endwhile;
            wp_reset_postdata();
        endif;
    }
} catch (Exception $ex) {
    $tmp_log = 'error';
    log_var($tmp_log);
}
