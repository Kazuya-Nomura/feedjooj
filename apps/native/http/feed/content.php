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

if (not_empty($cl['is_logged'])) {
	cl_redirect("home");
}

else {
	require_once(cl_full_path("core/apps/explore/app_ctrl.php"));
	require_once(cl_full_path("core/apps/feed/app_ctrl.php"));
	$cl["right_sidebar"]  = cl_template('main/guest_sidebar');
	$cl["page_title"] = cl_translate("Feed");
	$cl["page_desc"]  = $cl["config"]["description"];
	$cl["page_kw"]    = $cl["config"]["keywords"];
	$cl["pn"]         = "feed";
	$cl["sbr"]        = true;
	$cl["sbl"]        = true;
	$cl["feed_trending"] = cl_get_guest_trending(false, 30);
	$cl["feed_latest"]  = cl_get_guest_latest(false, 30);

	if (not_empty($cl["search_query"])) {
		$cl["search_query"] = cl_text_secure($cl["search_query"]);
		$cl["page_title"]   = $cl["search_query"];
		$cl["search_query"] = cl_croptxt($cl["search_query"], 32);
	}

	$cl["http_res"]   = cl_template("feed/content");
}