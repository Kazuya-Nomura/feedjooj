<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)                           @
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                    @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

if (empty($_GET["uname"])) {
	cl_redirect("404");
}

$uname           = fetch_or_get($_GET["uname"], false);
$uname           = cl_text_secure($uname);
$cl['prof_user'] = cl_get_user_by_name($uname);
$cl['page_tab']  = fetch_or_get($_GET["tab"], 'posts');


if (empty($cl['prof_user'])) {
	cl_redirect("404");
}

require_once(cl_full_path("core/apps/profile/app_ctrl.php"));

$cl["right_sidebar"]  = cl_template('main/right_sidebar_profile');
$cl["page_title"]  = cl_strf('%s %s%s%s',$cl['prof_user']['name'], '(@', $cl['prof_user']['username'], ')');
$cl["page_desc"]   = $cl['prof_user']['about'];
$cl["page_img"]    = $cl['prof_user']['avatar'];
$cl["page_kw"]     = $cl["config"]["keywords"];
$cl["pn"]          = "profile";
$cl["page_xdata"]  = array();
$cl["sbr"]         = true;
$cl["sbl"]         = true;
$cl["user_posts"]  = array();
$cl["user_likes"]  = array();
$cl["can_view"]    = cl_can_view_profile($cl['prof_user']['id']);
$cl["app_statics"] = array(
	"scripts" => array()
);

if (not_empty($cl["is_logged"])) {
	$cl['prof_user']['is_blocked'] = false;
	$cl['prof_user']['me_blocked'] = false;

	if (($cl['prof_user']['id'] != $me['id'])) {
		$cl['prof_user']['is_blocked'] = cl_is_blocked($me['id'], $cl['prof_user']['id']);
		$cl['prof_user']['me_blocked'] = cl_is_blocked($cl['prof_user']['id'], $me['id']);
	}

	$cl['prof_user']['owner']            = ($cl['prof_user']['id'] == $me['id']);
	$cl['prof_user']['is_following']     = cl_is_following($me['id'], $cl['prof_user']['id']);
	$cl['prof_user']['follow_requested'] = false;
	$cl['prof_user']['common_follows']   = cl_get_common_follows($cl['prof_user']['id']);
	$cl['prof_user']['is_myfollower']    = cl_is_following($cl['prof_user']['id'], $me["id"]);
	$cl['prof_user']['follow_requests'] = cl_get_follow_requests_total();

	if (empty($cl['prof_user']['is_following'])) {
		$cl['prof_user']['follow_requested'] = cl_follow_requested($me['id'], $cl['prof_user']['id']);
	}
	 
	if (not_empty($cl['prof_user']['owner'])) {

		$cl["page_xdata"]["is_me"] = true;

		$cl["app_statics"]["scripts"] = array(
			cl_js_template("statics/js/libs/jquery-ui")
		);
	}

	if ($me["id"] != $cl['prof_user']['id']) {
		cl_notify_user(array(
            'subject'  => 'visit',
            'user_id'  => $cl['prof_user']['id'],
            'entry_id' => $me["id"]
        ));
	}
	 
}

if (empty($cl['prof_user']['is_blocked']) && empty($cl['prof_user']['me_blocked']) && $cl['prof_user']['active'] == "1") {
	if (in_array($cl['page_tab'], array('posts', 'media'))) {
		if (not_empty($cl["can_view"])) {
			$media_type       = (($cl['page_tab'] == 'media') ? true : false);
			$cl["user_posts"] = cl_get_profile_posts($cl['prof_user']['id'], 30, $media_type);
		}
	}

	else {
		if (not_empty($cl["can_view"])) {
			$cl["user_likes"] = cl_get_profile_likes($cl['prof_user']['id'], 30);
		}
	}
}

$cl["http_res"] = cl_template("profile/content");

