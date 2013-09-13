# copy realurl paths to pathfinder history cache
insert ignore into tx_pathfinder_domain_model_cachehistory (pid, sys_language_uid, rootpage, mpvar, path) select page_id, language_id, rootpage_id, mpvar, concat(pagepath, '/') from tx_realurl_pathcache where language_id = 0;

# remove duplicate history paths
