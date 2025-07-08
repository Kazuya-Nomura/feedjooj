<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)                                    @
# @ Author_url 1: https://jooj.us                                           @
# @ Author_url 2: http://jooj.us/twitter-clone                              @
# @ Author E-mail: sales@jooj.us                                            @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

function cl_search_hashtags($keyword = "", $offset = false, $limit = 30) {
	global $db;

    $data    = array();
    $db      = $db->where('posts', '0', '>');
    $db      = $db->orderBy('posts','DESC');
    $db      = $db->orderBy('time','DESC');
    $keyword = ltrim($keyword,'#');
    $db      = (not_empty($keyword)) ? $db->where('tag', "%{$keyword}%", 'LIKE') : $db;

    if (is_posnum($offset)) {
        $tags = $db->get(T_HTAGS, array($offset, $limit));
    }
    else{
        $tags = $db->get(T_HTAGS, $limit);
    }
    

    if (cl_queryset($tags)) {
    	foreach ($tags as $tag_data) {
            $tag_data['tag']     = cl_rn_strip($tag_data['tag']);
    		$tag_data['hashtag'] = cl_strf("#%s", $tag_data['tag']);
    		$tag_data['url']     = cl_link(cl_strf("explore/posts?q=%s", cl_remove_emoji($tag_data['tag'])));
    		$tag_data['total']   = cl_number($tag_data['posts']);
    		$data[]              = $tag_data;
    	}
    }
    
    return $data;
}
function cl_search_symbols($keyword = "", $offset = false, $limit = 30) {
    global $db;
    $data    = array();
    $db      = $db->where('posts', '0', '>');
    $keyword = ltrim($keyword,'$');

    // Add prioritization for symbols starting with the keyword
    if (not_empty($keyword)) {
        $db = $db->where('symbol', "%{$keyword}%", 'LIKE');
        // Add custom order: symbols starting with keyword come first
        $db = $db->orderBy("(symbol LIKE '{$keyword}%')", 'DESC');
    }

    // $db      = $db->orderBy('posts','DESC');
    // $db      = $db->orderBy('time','DESC');

    if (is_posnum($offset)) {
        $symbols = $db->get(T_HSYMBOLS, array($offset, $limit));
    }
    else{
        $symbols = $db->get(T_HSYMBOLS, $limit);
    }
    

    if (cl_queryset($symbols)) {
        foreach ($symbols as $symbol_data) {
            $symbol_data['symbol']     = cl_rn_strip($symbol_data['symbol']);
            $symbol_data['hashsymbol'] = cl_strf("$%s", $symbol_data['symbol']);
            $symbol_data['url']     = cl_link(cl_strf("explore/posts?q=%s", cl_remove_emoji($symbol_data['symbol'])));
            $symbol_data['total']   = cl_number($symbol_data['posts']);
            $data[]              = $symbol_data;
        }
    }
    
    return $data;
}
function cl_search_people($keyword = "", $offset = false, $limit = 30) {
    global $db, $cl, $me;

    $data          = array();
    $user_id       = ((not_empty($cl['is_logged'])) ? $me['id'] : false);
    $sql           = cl_sqltepmlate('apps/explore/sql/fetch_people',array(
        't_users'  => T_USERS,
        't_blocks' => T_BLOCKS,
        'user_id'  => $user_id,
        'limit'    => $limit,
        'offset'   => $offset,
        'keyword'  => $keyword
    ));

    $query_result = $db->rawQuery($sql);

    if (cl_queryset($query_result)) {
        foreach ($query_result as $row) {
            $row['about']            = cl_rn_strip($row['about']);
            $row['about']            = stripslashes($row['about']);
            $row['name']             = cl_strf("%s",$row['fname']);      
            $row['avatar']           = cl_get_media($row['avatar']);
            $row['url']              = cl_link($row['username']);
            $row['last_active']      = date("d M, y h:m A",$row['last_active']); 
            $row['is_user']          = false;
            $row['is_following']     = false;
            $row['follow_requested'] = false;
            $row['common_follows']   = array();
            $row['country_a2c']      = fetch_or_get($cl['country_codes'][$row['country_id']], 'us');
            $row['country_name']     = cl_translate($cl['countries'][$row['country_id']], 'Unknown');
            $row['type']             = 'user';
            if (not_empty($user_id)) {
            	$row['is_user']      = ($user_id == $row['id']);
            	$row['is_following'] = cl_is_following($user_id, $row['id']);

                if (empty($row['is_following'])) {
                    $row['follow_requested'] = cl_follow_requested($user_id, $row['id']);
                }

                $row['common_follows'] = cl_get_common_follows($row['id']);
            }

            $row['about'] = cl_linkify_urls($row['about']);
            $data[]       = $row;
        }
    }

    return $data;
}
function cl_search_page($keyword = "", $offset = false, $limit = 30) {
    global $db, $cl, $me;

    $data          = array();
    $user_id       = ((not_empty($cl['is_logged'])) ? $me['id'] : false);
    $sql           = cl_sqltepmlate('apps/explore/sql/fetch_page',array(
        't_users'  => T_SYMBOLS,
        't_blocks' => T_BLOCKS,
        'user_id'  => $user_id,
        'limit'    => $limit,
        'offset'   => $offset,
        'keyword'  => $keyword
    ));

    $query_result = $db->rawQuery($sql);

    if (cl_queryset($query_result)) {
        foreach ($query_result as $row) {
            $row['about']            = cl_rn_strip($row['about']);
            $row['about']            = stripslashes($row['about']);
            $row['name']             = cl_strf("%s",$row['fname']);      
            $row['avatar']           = cl_get_media($row['avatar']);
            $row['url']              = cl_link('symbol/' . $row['username']); 
            $row['last_active']      = date("d M, y h:m A",$row['last_active']); 
            $row['is_user']          = false;
            $row['is_following']     = false;
            $row['follow_requested'] = false;
            $row['common_follows']   = array();
            $row['country_a2c']      = fetch_or_get($cl['country_codes'][$row['country_id']], 'us');
            $row['country_name']     = cl_translate($cl['countries'][$row['country_id']], 'Unknown');
            $row['type']             = 'page';
            if (not_empty($user_id)) {
            	$row['is_user']      = ($user_id == $row['id']);
            	$row['is_following'] = cl_is_watching($user_id, $row['id']);

                if (empty($row['is_following'])) {
                    $row['follow_requested'] = cl_watch_requested($user_id, $row['id']);
                }

                $row['common_follows'] = cl_get_common_follows($row['id']);
            }

            $row['about'] = cl_linkify_urls($row['about']);
            $data[]       = $row;
        }
    }

    return $data;
}

function cl_search_posts($keyword = "",  $offset = false, $limit = 10) {
	global $db,$cl,$me;

	$user_id       = ((not_empty($cl['is_logged'])) ? $me['id'] : true);
	$data           = array();
    $htag           = ((not_empty($keyword)) ? cl_get_htag_id($keyword) : false);
    $symbol           = ((not_empty($keyword)) ? cl_get_symbol_id($keyword) : false);
	$sql            = cl_sqltepmlate("apps/explore/sql/fetch_posts",array(
		"t_pubs"    => T_PUBS,
        "t_posts"   => T_POSTS,
        "t_blocks"  => T_BLOCKS,
        "t_conns"   => T_CONNECTIONS,
        't_reports' => T_PUB_REPORTS,
		"keyword"   => $keyword,
        "user_id"   => $user_id,
        "htag"      => $htag,
        "symbol"      => $symbol,
		"offset"    => $offset,
		"limit"     => $limit
 	));
     $query_res = $db->rawQuery($sql);
    //  error_log("DEBUG cl_search_posts: Reposter info set: " . json_encode($query_res));
    $counter   = 0;

	if (cl_queryset($query_res)) {
		foreach ($query_res as $row) {
			$post_data = cl_raw_post_data($row['publication_id']);

			

			if (not_empty($post_data) && in_array($post_data['status'], array('active'))) {
				$post_data['is_repost']   = (($row['type'] == 'repost') ? true : false);
				$post_data['is_quote']   = (($row['type'] == 'quote') ? true : false);		/* edited by kevin to quote comment */
				$post_data['is_reposter'] = false;
				$post_data['attrs']       = array();
				$post_data['comment_on']  = null;						/* edited by kevin to fetch comment on (added) */

				if ($post_data['is_repost']) {
					$reposter_data         = cl_user_data($row['reposter_id']);
					$post_data['reposter'] = array(
						'name' => $reposter_data['name'],
						'username' => $reposter_data['username'],
						'url' => $reposter_data['url'],
					);
				}

				if($post_data['is_quote']){
					$post_data['comment_on']  = cl_get_guest_feed_one($row['comment_on']);
					if($post_data['comment_on']) $post_data['comment_on'] = $post_data['comment_on'][0];		/* edited by kevin to fetch comment on (added) */
				}

				if (isset($me['id']) && $row['user_id'] == $me['id']) {
					$post_data['is_reposter'] = true;
				}

				$post_data['attrs'] = ((not_empty($post_data['attrs'])) ? implode(' ', $post_data['attrs']) : '');
				$data[]             = cl_post_data($post_data);
			}

            if ($cl['config']['advertising_system'] == 'on') {
                if (cl_is_feed_nad_allowed()) {
                    if (not_empty($offset)) {
                        if ($counter == 5) {
                            $counter = 0;
                            $ad      = cl_get_timeline_ads();

                            if (not_empty($ad)) {
                                $data[] = $ad;
                            }
                        }
                        else {
                            $counter += 1;
                        }
                    }
                }
            }
		}

        if ($cl['config']['advertising_system'] == 'on') {
            if (cl_is_feed_nad_allowed()) {
                if (empty($offset)) {
                    $ad = cl_get_timeline_ads();

                    if (not_empty($ad)) {
                        $data[] = $ad;
                    }
                }
            }
        }
	}

	return $data;
}